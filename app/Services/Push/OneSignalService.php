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
            Log::warning("User {$user->id} has no OneSignal player_id", [
                'user_id' => $user->id,
                'email' => $user->email,
                'notification_id' => $notification->id,
            ]);

            return false;
        }

        // Vérifier les préférences de notification
        if (!$this->shouldSendNotification($user, $notification)) {
            Log::info("Notification skipped for user {$user->id} due to preferences", [
                'user_id' => $user->id,
                'notification_id' => $notification->id,
                'notification_title' => $notification->title,
            ]);
            return false;
        }

        Log::info("Sending notification to user {$user->id}", [
            'user_id' => $user->id,
            'player_id' => $user->onesignal_player_id,
            'notification_id' => $notification->id,
            'notification_title' => $notification->title,
        ]);

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
            Log::info("Preparing to send OneSignal notification", [
                'notification_id' => $notification->id,
                'player_ids_count' => count($playerIds),
                'player_ids' => $playerIds,
                'title' => $notification->title,
            ]);

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

            Log::info("Sending request to OneSignal API", [
                'params' => $params,
            ]);

            $response = $this->sendRequest($params);

            Log::info("OneSignal API response received", [
                'response' => $response,
            ]);

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
                'player_ids' => $playerIds,
                'exception' => $e,
                'trace' => $e->getTraceAsString(),
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
    /**
     * Vérifier si une notification doit être envoyée à l'utilisateur
     * en fonction de ses préférences et de la période silencieuse
     */
    protected function shouldSendNotification(Utilisateur $user, PushNotification $notification): bool
    {
        $preferences = $user->notificationPreferences;

        if (!$preferences) {
            return true; // Pas de préférences = tout autorisé
        }

        // Mode Ne Pas Déranger actif
        if ($preferences->do_not_disturb) {
            Log::info("User {$user->id} has do_not_disturb enabled");
            return false;
        }

        // Notifications globalement désactivées
        if (!$preferences->notifications_enabled) {
            Log::info("User {$user->id} has notifications disabled");
            return false;
        }

        // Vérifier la période silencieuse (quiet hours)
        if ($preferences->quiet_start && $preferences->quiet_end) {
            $now = now();
            $quietStart = \Carbon\Carbon::createFromFormat('H:i', $preferences->quiet_start);
            $quietEnd = \Carbon\Carbon::createFromFormat('H:i', $preferences->quiet_end);

            // Gérer le cas où quiet_end est le lendemain (ex: 22:00 - 07:00)
            if ($quietEnd->lessThan($quietStart)) {
                // La période traverse minuit
                if ($now->format('H:i') >= $quietStart->format('H:i') || $now->format('H:i') < $quietEnd->format('H:i')) {
                    Log::info("User {$user->id} is in quiet hours ({$preferences->quiet_start} - {$preferences->quiet_end})");
                    return false;
                }
            } else {
                // Période normale dans la même journée
                if ($now->format('H:i') >= $quietStart->format('H:i') && $now->format('H:i') < $quietEnd->format('H:i')) {
                    Log::info("User {$user->id} is in quiet hours ({$preferences->quiet_start} - {$preferences->quiet_end})");
                    return false;
                }
            }
        }

        // Vérifier les préférences par catégorie
        $category = $this->getNotificationType($notification);
        if ($category === 'cycle' && !$preferences->cycle_notifications) {
            return false;
        }
        if ($category === 'content' && !$preferences->content_notifications) {
            return false;
        }
        if ($category === 'forum' && !$preferences->forum_notifications) {
            return false;
        }
        if ($category === 'health_tips' && !$preferences->health_tips_notifications) {
            return false;
        }
        if ($category === 'admin' && !$preferences->admin_notifications) {
            return false;
        }

        return true;
    }}
