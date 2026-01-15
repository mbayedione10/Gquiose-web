<?php

namespace App\Services\Push;

use App\Models\NotificationLog;
use App\Models\PushNotification;
use App\Models\Utilisateur;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Log;

class OneSignalService
{
    protected Client $client;
    protected string $appId;
    protected string $apiKey;

    public function __construct()
    {
        $this->client = new Client();
        $this->appId = config('onesignal.app_id');
        $this->apiKey = config('onesignal.rest_api_key');
    }

    /**
     * Envoyer une notification à un utilisateur spécifique via son player_id.
     */
    public function sendToUser(Utilisateur $user, PushNotification $notification): bool
    {
        if (empty($user->onesignal_player_id)) {
            Log::warning("User {$user->id} has no OneSignal player_id");

            return false;
        }

        return $this->sendToPlayerIds(
            [$user->onesignal_player_id],
            $notification,
            [$user]
        );
    }

    /**
     * Envoyer une notification à plusieurs utilisateurs via leurs player_ids.
     */
    public function sendToUsers(array $users, PushNotification $notification): array
    {
        $playerIds = [];
        $userMap = [];

        foreach ($users as $user) {
            if (!empty($user->onesignal_player_id)) {
                $playerIds[] = $user->onesignal_player_id;
                $userMap[$user->onesignal_player_id] = $user;
            }
        }

        if (empty($playerIds)) {
            Log::warning('No valid OneSignal player_ids found for batch send');

            return ['success' => 0, 'failed' => count($users)];
        }

        // OneSignal supporte jusqu'à 2000 player_ids par requête
        $chunks = array_chunk($playerIds, 2000);
        $totalSuccess = 0;
        $totalFailed = 0;

        foreach ($chunks as $chunk) {
            $chunkUsers = array_map(fn($id) => $userMap[$id], $chunk);
            $success = $this->sendToPlayerIds($chunk, $notification, $chunkUsers);

            if ($success) {
                $totalSuccess += count($chunk);
            } else {
                $totalFailed += count($chunk);
            }
        }

        // Comptabiliser les utilisateurs sans player_id
        $totalFailed += count($users) - count($playerIds);

        return [
            'success' => $totalSuccess,
            'failed' => $totalFailed,
        ];
    }

    /**
     * Envoyer une notification à des player_ids spécifiques.
     */
    protected function sendToPlayerIds(array $playerIds, PushNotification $notification, array $users = []): bool
    {
        try {
            $params = [
                'app_id' => $this->appId,
                'include_player_ids' => $playerIds,
                'headings' => ['en' => $notification->title, 'fr' => $notification->title],
                'contents' => ['en' => $notification->message, 'fr' => $notification->message],
            ];

            // Ajouter l'image si présente
            if (!empty($notification->image)) {
                $params['big_picture'] = $notification->image; // Android
                $params['ios_attachments'] = ['image' => $notification->image]; // iOS
            }

            // Ajouter l'icône si présente
            if (!empty($notification->icon)) {
                $params['small_icon'] = $notification->icon;
                $params['large_icon'] = $notification->icon;
            }

            // Ajouter les données additionnelles pour l'action
            if (!empty($notification->action)) {
                $params['data'] = [
                    'action' => $notification->action,
                    'notification_id' => $notification->id,
                ];
            }

            // Configuration iOS
            $params['ios_badgeType'] = 'Increase';
            $params['ios_badgeCount'] = 1;

            $response = $this->sendRequest($params);

            if (isset($response['id'])) {
                Log::info("OneSignal notification sent successfully", [
                    'onesignal_id' => $response['id'],
                    'notification_id' => $notification->id,
                    'recipients' => $response['recipients'] ?? count($playerIds),
                ]);

                // Créer les logs pour chaque utilisateur
                foreach ($users as $user) {
                    $this->createNotificationLog($notification, $user, 'sent');
                }

                return true;
            }

            Log::error('OneSignal notification failed', [
                'response' => $response,
                'notification_id' => $notification->id,
            ]);

            return false;

        } catch (\Exception $e) {
            Log::error('OneSignal send error: ' . $e->getMessage(), [
                'notification_id' => $notification->id,
                'player_ids_count' => count($playerIds),
            ]);

            return false;
        }
    }

    /**
     * Envoyer une notification à tous les abonnés (broadcast).
     */
    public function sendToAll(PushNotification $notification): bool
    {
        try {
            $params = [
                'app_id' => $this->appId,
                'included_segments' => ['All'],
                'headings' => ['en' => $notification->title, 'fr' => $notification->title],
                'contents' => ['en' => $notification->message, 'fr' => $notification->message],
            ];

            if (!empty($notification->image)) {
                $params['big_picture'] = $notification->image;
                $params['ios_attachments'] = ['image' => $notification->image];
            }

            if (!empty($notification->action)) {
                $params['data'] = [
                    'action' => $notification->action,
                    'notification_id' => $notification->id,
                ];
            }

            $response = $this->sendRequest($params);

            if (isset($response['id'])) {
                Log::info("OneSignal broadcast sent successfully", [
                    'onesignal_id' => $response['id'],
                    'notification_id' => $notification->id,
                    'recipients' => $response['recipients'] ?? 0,
                ]);

                return true;
            }

            return false;

        } catch (\Exception $e) {
            Log::error('OneSignal broadcast error: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Envoyer une notification à un segment spécifique.
     */
    public function sendToSegment(string $segment, PushNotification $notification): bool
    {
        try {
            $params = [
                'app_id' => $this->appId,
                'included_segments' => [$segment],
                'headings' => ['en' => $notification->title, 'fr' => $notification->title],
                'contents' => ['en' => $notification->message, 'fr' => $notification->message],
            ];

            if (!empty($notification->image)) {
                $params['big_picture'] = $notification->image;
                $params['ios_attachments'] = ['image' => $notification->image];
            }

            if (!empty($notification->action)) {
                $params['data'] = [
                    'action' => $notification->action,
                    'notification_id' => $notification->id,
                ];
            }

            $response = $this->sendRequest($params);

            return isset($response['id']);

        } catch (\Exception $e) {
            Log::error('OneSignal segment send error: ' . $e->getMessage());

            return false;
        }
    }

    /**
     * Envoyer la requête à l'API OneSignal.
     */
    protected function sendRequest(array $params): array
    {
        $response = $this->client->request('POST', 'https://onesignal.com/api/v1/notifications', [
            'headers' => [
                'Authorization' => 'Basic ' . $this->apiKey,
                'Content-Type' => 'application/json',
            ],
            'json' => $params,
        ]);

        return json_decode($response->getBody(), true);
    }

    /**
     * Créer un log de notification.
     */
    protected function createNotificationLog(PushNotification $notification, $user, string $status): void
    {
        try {
            NotificationLog::create([
                'utilisateur_id' => $user->id,
                'notification_schedule_id' => $notification->id,
                'title' => $notification->title,
                'message' => $notification->message,
                'icon' => $notification->icon,
                'action' => $notification->action,
                'image' => $notification->image,
                'type' => $notification->type ?? 'manual',
                'category' => $this->getNotificationType($notification),
                'status' => $status,
                'platform' => $user->platform ?? 'unknown',
                'sent_at' => now(),
            ]);
        } catch (\Exception $e) {
            Log::error('Error creating notification log: ' . $e->getMessage());
        }
    }

    /**
     * Déterminer le type de notification.
     */
    protected function getNotificationType(PushNotification $notification): string
    {
        $action = $notification->action ?? '';

        if (str_contains($action, 'cycle') || str_contains($notification->title, 'Cycle')) {
            return 'cycle';
        }
        if (str_contains($action, 'article') || str_contains($action, 'quiz') ||
            str_contains($action, 'video') || str_contains($action, 'health_center')) {
            return 'content';
        }
        if (str_contains($action, 'forum') || str_contains($action, 'message')) {
            return 'forum';
        }
        if (str_contains($action, 'conseil') || str_contains($action, 'health')) {
            return 'health_tips';
        }

        return 'content';
    }
}
