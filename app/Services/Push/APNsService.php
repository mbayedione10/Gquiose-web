<?php

namespace App\Services\Push;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Log;
use Pushok\AuthProvider\Token;
use Pushok\Client;
use Pushok\Notification;
use Pushok\Payload;
use Pushok\Payload\Alert;

class APNsService
{
    protected $client;

    protected $isConfigured = false;

    public function __construct()
    {
        try {
            $keyId = config('services.apns.key_id');
            $teamId = config('services.apns.team_id');
            $bundleId = config('services.apns.bundle_id');
            $keyPath = config('services.apns.key_path');
            $environment = config('services.apns.environment', 'production');

            if (! $keyId || ! $teamId || ! $bundleId || ! $keyPath) {
                Log::warning('APNs configuration incomplete');

                return;
            }

            if (! file_exists($keyPath)) {
                Log::warning('APNs key file not found at: '.$keyPath);

                return;
            }

            // Créer le token provider avec les credentials Apple
            $authProvider = Token::create([
                'key_id' => $keyId,
                'team_id' => $teamId,
                'app_bundle_id' => $bundleId,
                'private_key_path' => $keyPath,
                'private_key_secret' => null, // Null si pas de passphrase
            ]);

            // Initialiser le client
            $this->client = new Client($authProvider, $environment === 'production');
            $this->isConfigured = true;

        } catch (\Exception $e) {
            Log::error('Failed to initialize APNs client: '.$e->getMessage());
        }
    }

    /**
     * Envoie une notification push à un utilisateur via APNs.
     */
    public function sendToDevice(Utilisateur $user, PushNotification $notification): bool
    {
        if (! $this->isConfigured) {
            Log::error('APNs client not configured');

            return false;
        }

        if (empty($user->apns_token)) {
            Log::warning("User {$user->id} has no APNs token");

            return false;
        }

        try {
            // Créer l'alerte
            $alert = Alert::create()
                ->setTitle($notification->title)
                ->setBody($notification->message);

            // Créer le payload
            $payload = Payload::create()
                ->setAlert($alert)
                ->setSound('default')
                ->setBadge(1)
                ->setMutableContent()
                ->setContentAvailability();

            // Ajouter les données custom
            $customData = [
                'notification_id' => (string) $notification->id,
                'type' => $notification->type ?? 'general',
            ];

            if ($notification->action) {
                $customData['action'] = $notification->action;
            }

            if ($notification->icon) {
                $customData['icon'] = $notification->icon;
            }

            if ($notification->image) {
                $customData['image'] = $notification->image;
            }

            $payload->setCustomValue('data', $customData);

            // Créer la notification
            $apnsNotification = new Notification($payload, $user->apns_token);

            // Configuration additionnelle
            $apnsNotification
                ->setTopic(config('services.apns.bundle_id'))
                ->setPriority(Notification::PRIORITY_HIGH)
                ->setExpiration(time() + 86400); // Expire après 24h

            // Envoyer
            $responses = $this->client->addNotification($apnsNotification);
            $this->client->push();

            // Vérifier la réponse
            foreach ($responses as $response) {
                if ($response->getStatusCode() === 200) {
                    Log::info("APNs notification sent successfully to user {$user->id}");

                    return true;
                } else {
                    $errorReason = $response->getReasonPhrase();
                    Log::error("APNs send failed for user {$user->id}: {$errorReason}");

                    // Supprimer les tokens invalides
                    if (in_array($errorReason, ['BadDeviceToken', 'Unregistered'])) {
                        $user->update(['apns_token' => null]);
                        Log::info("Removed invalid APNs token for user {$user->id}");
                    }

                    return false;
                }
            }

            return false;

        } catch (\Exception $e) {
            Log::error("APNs error for user {$user->id}: ".$e->getMessage());

            return false;
        }
    }

    /**
     * Envoie une notification à plusieurs utilisateurs.
     * APNs supporte jusqu'à 5000 notifications concurrentes par connexion.
     *
     * @param  array  $users  Array of Utilisateur models
     * @return array ['success' => int, 'failed' => int, 'invalid_tokens' => array]
     */
    public function sendToMultipleDevices(array $users, PushNotification $notification): array
    {
        if (! $this->isConfigured) {
            Log::error('APNs client not configured');

            return ['success' => 0, 'failed' => count($users), 'invalid_tokens' => []];
        }

        $tokens = [];
        $userTokenMap = [];

        foreach ($users as $user) {
            if (! empty($user->apns_token)) {
                $tokens[] = $user->apns_token;
                $userTokenMap[$user->apns_token] = $user->id;
            }
        }

        if (empty($tokens)) {
            Log::warning('No valid APNs tokens found for batch send');

            return ['success' => 0, 'failed' => 0, 'invalid_tokens' => []];
        }

        try {
            // Créer l'alerte
            $alert = Alert::create()
                ->setTitle($notification->title)
                ->setBody($notification->message);

            // Créer le payload
            $payload = Payload::create()
                ->setAlert($alert)
                ->setSound('default')
                ->setBadge(1)
                ->setMutableContent()
                ->setContentAvailability();

            // Ajouter les données custom
            $customData = [
                'notification_id' => (string) $notification->id,
                'type' => $notification->type ?? 'general',
            ];

            if ($notification->action) {
                $customData['action'] = $notification->action;
            }

            if ($notification->icon) {
                $customData['icon'] = $notification->icon;
            }

            if ($notification->image) {
                $customData['image'] = $notification->image;
            }

            $payload->setCustomValue('data', $customData);

            // Ajouter toutes les notifications au client
            foreach ($tokens as $token) {
                $apnsNotification = new Notification($payload, $token);
                $apnsNotification
                    ->setTopic(config('services.apns.bundle_id'))
                    ->setPriority(Notification::PRIORITY_HIGH)
                    ->setExpiration(time() + 86400);

                $this->client->addNotification($apnsNotification);
            }

            // Envoyer en batch
            $responses = $this->client->push();

            $totalSuccess = 0;
            $totalFailed = 0;
            $invalidTokens = [];

            // Analyser les réponses
            foreach ($responses as $response) {
                if ($response->getStatusCode() === 200) {
                    $totalSuccess++;
                } else {
                    $totalFailed++;
                    $errorReason = $response->getReasonPhrase();
                    $deviceToken = $response->getDeviceToken();

                    Log::warning("APNs send failed for token {$deviceToken}: {$errorReason}");

                    // Tokens invalides
                    if (in_array($errorReason, ['BadDeviceToken', 'Unregistered'])) {
                        $invalidTokens[] = $deviceToken;

                        if (isset($userTokenMap[$deviceToken])) {
                            $userId = $userTokenMap[$deviceToken];
                            Utilisateur::where('id', $userId)->update(['apns_token' => null]);
                            Log::info("Removed invalid APNs token for user {$userId}");
                        }
                    }
                }
            }

            Log::info("APNs batch send completed: {$totalSuccess} success, {$totalFailed} failed, ".count($invalidTokens).' invalid tokens removed');

            return [
                'success' => $totalSuccess,
                'failed' => $totalFailed,
                'invalid_tokens' => $invalidTokens,
            ];

        } catch (\Exception $e) {
            Log::error('APNs batch send error: '.$e->getMessage());

            return ['success' => 0, 'failed' => count($tokens), 'invalid_tokens' => []];
        }
    }

    /**
     * Envoie une notification silencieuse (background notification).
     */
    public function sendSilentNotification(Utilisateur $user, array $data): bool
    {
        if (! $this->isConfigured || empty($user->apns_token)) {
            return false;
        }

        try {
            $payload = Payload::create()
                ->setContentAvailability()
                ->setCustomValue('data', $data);

            $notification = new Notification($payload, $user->apns_token);
            $notification
                ->setTopic(config('services.apns.bundle_id'))
                ->setPriority(Notification::PRIORITY_LOW)
                ->setPushType('background');

            $responses = $this->client->addNotification($notification);
            $this->client->push();

            foreach ($responses as $response) {
                return $response->getStatusCode() === 200;
            }

            return false;
        } catch (\Exception $e) {
            Log::error('APNs silent notification error: '.$e->getMessage());

            return false;
        }
    }
}
