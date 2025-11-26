<?php

namespace App\Services;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PushNotificationService
{
    protected $fcmServerKey;

    public function __construct()
    {
        $this->fcmServerKey = config('services.fcm.server_key');
    }

    /**
     * Envoyer une notification push à des utilisateurs ciblés
     */
    public function sendNotification(PushNotification $notification)
    {
        $users = $this->getTargetedUsers($notification);
        
        $sentCount = 0;
        $deliveredCount = 0;

        foreach ($users as $user) {
            if ($this->canSendToUser($user)) {
                $result = $this->sendToDevice($user, $notification);
                
                if ($result['sent']) {
                    $sentCount++;
                }
                if ($result['delivered']) {
                    $deliveredCount++;
                }
            }
        }

        $notification->update([
            'sent_at' => now(),
            'status' => 'sent',
            'sent_count' => $sentCount,
            'delivered_count' => $deliveredCount,
        ]);

        return [
            'sent_count' => $sentCount,
            'delivered_count' => $deliveredCount,
            'total_users' => $users->count(),
        ];
    }

    /**
     * Récupérer les utilisateurs ciblés selon les filtres
     */
    protected function getTargetedUsers(PushNotification $notification)
    {
        $query = Utilisateur::query();

        if ($notification->target_audience === 'all') {
            return $query->get();
        }

        $filters = $notification->filters ?? [];

        // Filtre par âge
        if (isset($filters['age_min'])) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) >= ?', [$filters['age_min']]);
        }
        if (isset($filters['age_max'])) {
            $query->whereRaw('TIMESTAMPDIFF(YEAR, dob, CURDATE()) <= ?', [$filters['age_max']]);
        }

        // Filtre par sexe
        if (isset($filters['sexe'])) {
            $query->where('sexe', $filters['sexe']);
        }

        // Filtre par ville/localisation
        if (isset($filters['ville_id'])) {
            $query->where('ville_id', $filters['ville_id']);
        }

        return $query->get();
    }

    /**
     * Vérifier si on peut envoyer à cet utilisateur
     */
    protected function canSendToUser(Utilisateur $user)
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

        return true;
    }

    /**
     * Envoyer notification à un appareil via FCM
     */
    protected function sendToDevice(Utilisateur $user, PushNotification $notification)
    {
        $payload = [
            'to' => $user->fcm_token,
            'notification' => [
                'title' => $notification->title,
                'body' => $notification->message,
                'icon' => $notification->icon ?? 'default_icon',
                'click_action' => $notification->action ?? 'FLUTTER_NOTIFICATION_CLICK',
            ],
            'data' => [
                'id' => $notification->id,
                'type' => $notification->type,
                'image' => $notification->image,
            ],
            'priority' => 'high',
        ];

        try {
            $response = Http::withHeaders([
                'Authorization' => 'key=' . $this->fcmServerKey,
                'Content-Type' => 'application/json',
            ])->post('https://fcm.googleapis.com/fcm/send', $payload);

            $result = $response->json();

            return [
                'sent' => true,
                'delivered' => isset($result['success']) && $result['success'] > 0,
            ];

        } catch (\Exception $e) {
            Log::error('Erreur envoi notification push: ' . $e->getMessage());
            
            return [
                'sent' => false,
                'delivered' => false,
            ];
        }
    }

    /**
     * Envoyer notification iOS via APNs
     */
    protected function sendToApple(Utilisateur $user, PushNotification $notification)
    {
        // TODO: Implémenter l'envoi via APNs
        // Nécessite certificat Apple et configuration supplémentaire
        return [
            'sent' => false,
            'delivered' => false,
        ];
    }
}
