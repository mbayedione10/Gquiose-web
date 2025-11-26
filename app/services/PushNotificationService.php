<?php

namespace App\Services\Notifications;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
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
        $fcmToken = $user->fcm_token;
        $payload = [
            'title' => $notification->title,
            'body' => $notification->body,
            'sound' => 'default',
            'data' => [
                'notification_id' => $notification->id,
                'action' => $notification->action,
                'deeplink' => $notification->deeplink,
            ],
        ];

        // Envoyer à Android (FCM)
        if (!empty($fcmToken) && config('services.fcm.server_key')) {
            try {
                $client = new \GuzzleHttp\Client();
                $response = $client->post('https://fcm.googleapis.com/fcm/send', [
                    'headers' => [
                        'Authorization' => 'key=' . config('services.fcm.server_key'),
                        'Content-Type' => 'application/json',
                    ],
                    'json' => [
                        'to' => $fcmToken,
                        'notification' => [
                            'title' => $payload['title'],
                            'body' => $payload['body'],
                            'sound' => $payload['sound'],
                        ],
                        'data' => $payload['data'],
                    ],
                ]);

                if ($response->getStatusCode() == 200) {
                    $this->trackNotificationStatus($notification->id, $user->id, 'sent');
                    return true;
                }
            } catch (\Exception $e) {
                Log::error("FCM send failed for user {$user->id}: " . $e->getMessage());
            }
        }

        // Envoyer à iOS (APNs)
        if (!empty($user->apns_token) && config('services.apns.key_id')) {
            try {
                // Logique d'envoi APNs (à implémenter)
                // Utiliser un package comme laravel-apn ou similaire
                // Exemple simplifié:
                // Apn::send($user->apns_token, $payload);
                $this->trackNotificationStatus($notification->id, $user->id, 'sent');
                return true;

            } catch (\Exception $e) {
                Log::error("APNs send failed for user {$user->id}: " . $e->getMessage());
            }
        }

        return false;
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
        if (str_contains($action, 'article') || str_contains($action, 'content')) {
            return 'content';
        }
        if (str_contains($action, 'forum') || str_contains($action, 'message')) {
            return 'forum';
        }
        if (str_contains($action, 'conseil') || str_contains($action, 'health')) {
            return 'health_tips';
        }
        if (str_contains($notification->title, 'Admin') || $notification->type === 'admin') {
            return 'admin';
        }

        return 'admin'; // Par défaut
    }

    /**
     * Enregistrer le statut de la notification (envoyé, ouvert, cliqué).
     *
     * @param int $notificationId
     * @param int $userId
     * @param string $status
     * @return void
     */
    protected function trackNotificationStatus(int $notificationId, int $userId, string $status)
    {
        // Implémenter la logique pour enregistrer le statut dans la base de données
        // Par exemple, créer un enregistrement dans une table 'notification_user_status'
        // Log::info("Tracking notification {$notificationId} for user {$userId} with status: {$status}");
    }
}