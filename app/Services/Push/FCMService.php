<?php

namespace App\Services\Push;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;
use Kreait\Firebase\Exception\FirebaseException;
use Kreait\Firebase\Exception\Messaging\NotFound;
use Kreait\Firebase\Exception\MessagingException;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Messaging\CloudMessage;
use Kreait\Firebase\Messaging\Notification as FirebaseNotification;

class FCMService
{
    protected $messaging;

    public function __construct()
    {
        try {
            $credentialsPath = config('services.fcm.credentials_path');

            if (! $credentialsPath || ! file_exists($credentialsPath)) {
                Log::warning('FCM credentials file not found at: '.$credentialsPath);

                return;
            }

            $factory = (new Factory)->withServiceAccount($credentialsPath);
            $this->messaging = $factory->createMessaging();
        } catch (\Exception $e) {
            Log::error('Failed to initialize Firebase Messaging: '.$e->getMessage());
        }
    }

    /**
     * Envoie une notification push à un utilisateur via FCM.
     */
    public function sendToDevice(Utilisateur $user, PushNotification $notification): bool
    {
        if (! $this->messaging) {
            Log::error('Firebase Messaging not initialized');

            return false;
        }

        if (empty($user->fcm_token)) {
            Log::warning("User {$user->id} has no FCM token");

            return false;
        }

        try {
            // Créer la notification Firebase
            $firebaseNotification = FirebaseNotification::create(
                $notification->title,
                $notification->message
            );

            if ($notification->image) {
                $firebaseNotification = $firebaseNotification->withImageUrl($notification->image);
            }

            // Données additionnelles
            $data = [
                'notification_id' => (string) $notification->id,
                'type' => $notification->type ?? 'general',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            if ($notification->action) {
                $data['action'] = $notification->action;
            }

            if ($notification->icon) {
                $data['icon'] = $notification->icon;
            }

            // Créer le message complet
            $message = CloudMessage::withTarget('token', $user->fcm_token)
                ->withNotification($firebaseNotification)
                ->withData($data)
                ->withAndroidConfig([
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'gquiose_notifications',
                        'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
                    ],
                ])
                ->withApnsConfig([
                    'headers' => [
                        'apns-priority' => '10',
                    ],
                    'payload' => [
                        'aps' => [
                            'alert' => [
                                'title' => $notification->title,
                                'body' => $notification->message,
                            ],
                            'sound' => 'default',
                            'badge' => 1,
                        ],
                    ],
                ]);

            // Envoyer le message
            $this->messaging->send($message);

            Log::info("FCM notification sent successfully to user {$user->id}");

            return true;

        } catch (NotFound $e) {
            // Token invalide ou expiré
            Log::warning("Invalid FCM token for user {$user->id}: ".$e->getMessage());
            // Optionnel: Supprimer le token invalide
            $user->update(['fcm_token' => null]);

            return false;

        } catch (MessagingException $e) {
            Log::error("FCM messaging error for user {$user->id}: ".$e->getMessage());

            return false;

        } catch (FirebaseException $e) {
            Log::error("Firebase error for user {$user->id}: ".$e->getMessage());

            return false;

        } catch (\Exception $e) {
            Log::error("Unexpected error sending FCM to user {$user->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Envoie une notification à plusieurs utilisateurs en batch.
     * Plus efficace pour l'envoi massif.
     *
     * @param  array  $users  Array of Utilisateur models
     * @return array ['success' => int, 'failed' => int, 'invalid_tokens' => array]
     */
    public function sendToMultipleDevices(array $users, PushNotification $notification): array
    {
        if (! $this->messaging) {
            Log::error('Firebase Messaging not initialized');

            return ['success' => 0, 'failed' => count($users), 'invalid_tokens' => []];
        }

        $tokens = [];
        $userTokenMap = [];

        foreach ($users as $user) {
            if (! empty($user->fcm_token)) {
                $tokens[] = $user->fcm_token;
                $userTokenMap[$user->fcm_token] = $user->id;
            }
        }

        if (empty($tokens)) {
            Log::warning('No valid FCM tokens found for batch send');

            return ['success' => 0, 'failed' => 0, 'invalid_tokens' => []];
        }

        try {
            // Créer la notification
            $firebaseNotification = FirebaseNotification::create(
                $notification->title,
                $notification->message
            );

            if ($notification->image) {
                $firebaseNotification = $firebaseNotification->withImageUrl($notification->image);
            }

            // Données
            $data = [
                'notification_id' => (string) $notification->id,
                'type' => $notification->type ?? 'general',
                'click_action' => 'FLUTTER_NOTIFICATION_CLICK',
            ];

            if ($notification->action) {
                $data['action'] = $notification->action;
            }

            if ($notification->icon) {
                $data['icon'] = $notification->icon;
            }

            // Créer le message multicast
            $message = CloudMessage::new()
                ->withNotification($firebaseNotification)
                ->withData($data)
                ->withAndroidConfig([
                    'priority' => 'high',
                    'notification' => [
                        'sound' => 'default',
                        'channel_id' => 'gquiose_notifications',
                    ],
                ]);

            // Envoyer en batch (max 500 tokens par requête)
            $chunks = array_chunk($tokens, 500);
            $totalSuccess = 0;
            $totalFailed = 0;
            $invalidTokens = [];

            foreach ($chunks as $chunkTokens) {
                $report = $this->messaging->sendMulticast($message, $chunkTokens);

                $totalSuccess += $report->successes()->count();
                $totalFailed += $report->failures()->count();

                // Identifier les tokens invalides
                foreach ($report->failures()->getItems() as $failure) {
                    $failedToken = $chunkTokens[$failure->messageTargetIndex()];

                    if (in_array($failure->error()->getMessage(), [
                        'registration-token-not-registered',
                        'invalid-registration-token',
                    ])) {
                        $invalidTokens[] = $failedToken;

                        // Supprimer le token invalide
                        if (isset($userTokenMap[$failedToken])) {
                            $userId = $userTokenMap[$failedToken];
                            Utilisateur::where('id', $userId)->update(['fcm_token' => null]);
                            Log::info("Removed invalid FCM token for user {$userId}");
                        }
                    }

                    Log::warning("FCM send failed for token {$failedToken}: ".$failure->error()->getMessage());
                }
            }

            Log::info("FCM batch send completed: {$totalSuccess} success, {$totalFailed} failed, ".count($invalidTokens).' invalid tokens removed');

            return [
                'success' => $totalSuccess,
                'failed' => $totalFailed,
                'invalid_tokens' => $invalidTokens,
            ];

        } catch (\Exception $e) {
            Log::error('FCM batch send error: '.$e->getMessage());

            return ['success' => 0, 'failed' => count($tokens), 'invalid_tokens' => []];
        }
    }

    /**
     * Valide un token FCM en envoyant un message de test.
     */
    public function validateToken(string $token): bool
    {
        if (! $this->messaging) {
            return false;
        }

        try {
            $message = CloudMessage::withTarget('token', $token)
                ->withData(['test' => 'validation']);

            $this->messaging->validate($message);

            return true;
        } catch (\Exception $e) {
            Log::debug('FCM token validation failed: '.$e->getMessage());

            return false;
        }
    }
}
