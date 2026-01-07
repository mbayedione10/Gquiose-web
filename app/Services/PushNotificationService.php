<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use App\Models\NotificationLog;
use App\Services\Push\FCMService;
use App\Services\Push\APNsService;
use App\Jobs\SendBatchNotifications;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    /**
     * Envoie une notification automatiquement aux utilisateurs ciblés.
     *
     * @param PushNotification $notification
     * @return void
     */
    public function sendNotification(PushNotification $notification)
    {
        // Récupérer les utilisateurs ciblés
        $users = $this->getTargetedUsers($notification);

        // Envoyer aux utilisateurs
        $this->sendPushNotification($notification, $users->toArray());
    }

    /**
     * Envoie une notification en utilisant des jobs en queue pour optimiser les envois massifs.
     * Plus performant pour les grandes audiences.
     *
     * @param PushNotification $notification
     * @param int $batchSize Nombre d'utilisateurs par batch (défaut: 100)
     * @return void
     */
    public function sendNotificationInBatches(PushNotification $notification, int $batchSize = 100)
    {
        // Récupérer les utilisateurs ciblés
        $users = $this->getTargetedUsers($notification);
        $userIds = $users->pluck('id')->toArray();

        $totalUsers = count($userIds);
        Log::info("Preparing to send notification {$notification->id} to {$totalUsers} users in batches of {$batchSize}");

        // Si peu d'utilisateurs, envoyer de manière synchrone
        if ($totalUsers <= 50) {
            Log::info("Small audience ({$totalUsers} users), sending synchronously");
            $this->sendPushNotification($notification, $users->toArray());
            return;
        }

        // Diviser en batches et dispatcher les jobs
        $batches = array_chunk($userIds, $batchSize);
        $jobCount = 0;

        foreach ($batches as $batchUserIds) {
            SendBatchNotifications::dispatch($notification, $batchUserIds)
                ->onQueue('notifications'); // Queue spécifique pour les notifications

            $jobCount++;
        }

        Log::info("Dispatched {$jobCount} batch jobs for notification {$notification->id}");

        // Mettre à jour le statut
        $notification->update([
            'status' => 'sending',
        ]);
    }

    /**
     * Récupère les utilisateurs ciblés selon les filtres de la notification.
     *
     * @param PushNotification $notification
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getTargetedUsers(PushNotification $notification)
    {
        $query = Utilisateur::query();

        if ($notification->target_audience === 'filtered' && $notification->filters) {
            $filters = is_array($notification->filters) ? $notification->filters : json_decode($notification->filters, true);

            // Filtres démographiques (basés sur l'année de naissance)
            if (isset($filters['age_min'])) {
                $maxYear = now()->year - $filters['age_min'];
                $query->where('anneedenaissance', '<=', $maxYear);
            }

            if (isset($filters['age_max'])) {
                $minYear = now()->year - $filters['age_max'];
                $query->where('anneedenaissance', '>=', $minYear);
            }

            if (isset($filters['sexe'])) {
                $query->where('sexe', $filters['sexe']);
            }

            // Filtres géographiques
            if (isset($filters['ville_id'])) {
                $query->where('ville_id', $filters['ville_id']);
            }

            if (isset($filters['villes']) && is_array($filters['villes']) && count($filters['villes']) > 0) {
                $query->whereIn('ville_id', $filters['villes']);
            }

            // Filtres d'activité
            if (isset($filters['active_users'])) {
                $days = match($filters['active_users']) {
                    'last_7_days' => 7,
                    'last_30_days' => 30,
                    'last_90_days' => 90,
                    default => null,
                };

                if ($days) {
                    $query->where('updated_at', '>=', now()->subDays($days));
                }
            }

            if (isset($filters['has_cycle_data']) && $filters['has_cycle_data']) {
                $query->whereHas('menstrualCycles');
            }

            if (isset($filters['has_alerts']) && $filters['has_alerts']) {
                $query->whereHas('alertes');
            }
        }

        return $query->whereNotNull('fcm_token')->where('status', true)->get();
    }

    /**
     * Envoie une notification push à une liste d'utilisateurs.
     *
     * @param PushNotification $notification
     * @param array $users
     * @return void
     */
    public function sendPushNotification(PushNotification $notification, array $users)
    {
        $failed = 0;
        $success = 0;

        foreach ($users as $user) {
            if ($this->canSendToUser($user, $notification)) {
                $result = $this->sendToDevice($user, $notification);
                if ($result) {
                    $success++;
                } else {
                    $failed++;
                }
            } else {
                $failed++;
            }
        }

        // Enregistrer les statistiques
        $notification->update([
            'sent' => $success,
            'failed' => $failed,
        ]);

        Log::info("Push notification sent: Success={$success}, Failed={$failed}");
    }

    /**
     * Envoie la notification à un appareil spécifique.
     *
     * @param Utilisateur $user
     * @param PushNotification $notification
     * @return bool
     */
    protected function sendToDevice(Utilisateur $user, PushNotification $notification): bool
    {
        $sent = false;

        // Envoyer à Android (FCM) - Utilise le nouveau Firebase Admin SDK
        if (!empty($user->fcm_token)) {
            try {
                $fcmService = app(FCMService::class);
                $sent = $fcmService->sendToDevice($user, $notification);

                if ($sent) {
                    $this->trackNotificationStatus($notification->id, $user->id, 'sent');
                }
            } catch (\Exception $e) {
                Log::error("FCM send failed for user {$user->id}: " . $e->getMessage());
            }
        }

        // Envoyer à iOS (APNs)
        if (!$sent && !empty($user->apns_token)) {
            try {
                $apnsService = app(APNsService::class);
                $sent = $apnsService->sendToDevice($user, $notification);

                if ($sent) {
                    $this->trackNotificationStatus($notification->id, $user->id, 'sent');
                }
            } catch (\Exception $e) {
                Log::error("APNs send failed for user {$user->id}: " . $e->getMessage());
            }
        }

        return $sent;
    }

    /**
     * Vérifier si on peut envoyer à cet utilisateur.
     */
    protected function canSendToUser(Utilisateur $user, PushNotification $notification)
    {
        // Vérifier si l'utilisateur a un token FCM
        if (empty($user->fcm_token)) {
            return false;
        }

        // Vérifier les préférences de notification
        $preferences = $user->notificationPreferences;

        if (!$preferences || !$preferences->notifications_enabled) {
            return false;
        }

        // Vérifier le mode silencieux
        if ($preferences->do_not_disturb) {
            return false;
        }

        // Vérifier les heures de silence
        if ($preferences->quiet_start && $preferences->quiet_end) {
            $now = now()->format('H:i:s');
            if ($now >= $preferences->quiet_start && $now <= $preferences->quiet_end) {
                return false;
            }
        }

        // Vérifier les préférences par type de notification
        $notificationType = $this->getNotificationType($notification);

        if ($notificationType === 'cycle' && !$preferences->cycle_notifications) {
            return false;
        }
        if ($notificationType === 'content' && !$preferences->content_notifications) {
            return false;
        }
        if ($notificationType === 'forum' && !$preferences->forum_notifications) {
            return false;
        }
        if ($notificationType === 'health_tips' && !$preferences->health_tips_notifications) {
            return false;
        }
        if ($notificationType === 'admin' && !$preferences->admin_notifications) {
            return false;
        }

        return true;
    }

    /**
     * Déterminer le type de notification
     */
    protected function getNotificationType(PushNotification $notification)
    {
        // Utiliser l'action ou le type pour déterminer la catégorie
        $action = $notification->action ?? '';

        if (str_contains($action, 'cycle') || str_contains($notification->title, 'Cycle')) {
            return 'cycle';
        }
        if (str_contains($action, 'article') || str_contains($action, 'quiz') ||
            str_contains($action, 'video') || str_contains($action, 'health_center') ||
            str_contains($notification->title, 'quiz') || str_contains($notification->title, 'vidéo') ||
            str_contains($notification->title, 'centre de santé')) {
            return 'content';
        }
        if (str_contains($action, 'forum') || str_contains($action, 'message')) {
            return 'forum';
        }
        if (str_contains($action, 'conseil') || str_contains($action, 'health') ||
            str_contains($notification->title, 'Conseil')) {
            return 'health_tips';
        }
        if (str_contains($notification->title, 'Admin') || $notification->type === 'admin') {
            return 'admin';
        }

        return 'content'; // Par défaut - considérer comme contenu
    }

    /**
     * Enregistrer le statut de la notification (envoyé, ouvert, cliqué).
     *
     * @param int $notificationId
     * @param int $userId
     * @param string $status
     * @return NotificationLog|null
     */
    protected function trackNotificationStatus(int $notificationId, int $userId, string $status): ?NotificationLog
    {
        try {
            $notification = PushNotification::find($notificationId);

            if (!$notification) {
                Log::warning("Notification {$notificationId} not found for tracking");
                return null;
            }

            $user = Utilisateur::find($userId);
            if (!$user) {
                Log::warning("User {$userId} not found for tracking");
                return null;
            }

            // Déterminer la plateforme
            $platform = null;
            if (!empty($user->fcm_token)) {
                $platform = 'android';
            } elseif (!empty($user->apns_token)) {
                $platform = 'ios';
            }

            // Créer ou mettre à jour le log
            $log = NotificationLog::create([
                'utilisateur_id' => $userId,
                'notification_schedule_id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'icon' => $notification->icon,
                'action' => $notification->action,
                'image' => $notification->image,
                'type' => $notification->type ?? 'manual',
                'category' => $this->getNotificationType($notification),
                'status' => $status,
                'platform' => $platform,
                'sent_at' => $status === 'sent' ? now() : null,
            ]);

            Log::info("Tracked notification {$notificationId} for user {$userId} with status: {$status}");

            return $log;

        } catch (\Exception $e) {
            Log::error("Error tracking notification status: " . $e->getMessage());
            return null;
        }
    }

    /**
     * Mettre à jour les statistiques de la notification
     *
     * @param PushNotification $notification
     * @return void
     */
    public function updateNotificationStats(PushNotification $notification)
    {
        $stats = NotificationLog::where('notification_schedule_id', $notification->id)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "sent" THEN 1 ELSE 0 END) as sent,
                SUM(CASE WHEN status = "delivered" THEN 1 ELSE 0 END) as delivered,
                SUM(CASE WHEN opened_at IS NOT NULL THEN 1 ELSE 0 END) as opened,
                SUM(CASE WHEN clicked_at IS NOT NULL THEN 1 ELSE 0 END) as clicked,
                SUM(CASE WHEN status = "failed" THEN 1 ELSE 0 END) as failed
            ')
            ->first();

        $notification->update([
            'sent_count' => $stats->sent ?? 0,
            'delivered_count' => $stats->delivered ?? 0,
            'opened_count' => $stats->opened ?? 0,
            'clicked_count' => $stats->clicked ?? 0,
        ]);
    }
}