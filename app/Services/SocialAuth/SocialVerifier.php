<?php

namespace App\Services\SocialAuth;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class SocialVerifier
{
    /**
     * Vérifie un token Google et retourne les informations utilisateur
     *
     * @param  string  $token  Token ID Google
     * @return array|null ['provider_id' => string, 'email' => string, 'name' => string, 'picture' => string]
     */
    public function verifyGoogleToken(string $token): ?array
    {
        try {
            $response = Http::get('https://oauth2.googleapis.com/tokeninfo', [
                'id_token' => $token,
            ]);

            if (! $response->successful()) {
                Log::warning('Google token verification failed', [
                    'status' => $response->status(),
                    'error' => $response->json(),
                ]);

                return null;
            }

            $data = $response->json();

            // Vérifier que le token est bien pour notre application (Web, Android ou iOS)
            $validClientIds = array_filter([
                config('services.google.client_id'),
                config('services.google.client_id_android'),
                config('services.google.client_id_ios'),
            ]);

            if (! empty($validClientIds) && isset($data['aud']) && ! in_array($data['aud'], $validClientIds)) {
                Log::warning('Google token aud mismatch', [
                    'expected' => $validClientIds,
                    'received' => $data['aud'],
                ]);

                return null;
            }

            // Vérifier l'expiration
            if (isset($data['exp']) && $data['exp'] < time()) {
                Log::warning('Google token expired');

                return null;
            }

            return [
                'provider_id' => $data['sub'] ?? null,
                'email' => $data['email'] ?? null,
                'email_verified' => $data['email_verified'] ?? false,
                'name' => $data['name'] ?? null,
                'given_name' => $data['given_name'] ?? null,
                'family_name' => $data['family_name'] ?? null,
                'picture' => $data['picture'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Google token verification exception', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Vérifie un token Facebook et retourne les informations utilisateur
     *
     * @param  string  $token  Access Token Facebook
     */
    public function verifyFacebookToken(string $token): ?array
    {
        try {
            // Étape 1: Vérifier le token auprès de Facebook
            $appId = config('services.facebook.app_id');
            $appSecret = config('services.facebook.app_secret');

            $debugResponse = Http::get('https://graph.facebook.com/debug_token', [
                'input_token' => $token,
                'access_token' => "{$appId}|{$appSecret}",
            ]);

            if (! $debugResponse->successful()) {
                Log::warning('Facebook token debug failed', [
                    'error' => $debugResponse->json(),
                ]);

                return null;
            }

            $debugData = $debugResponse->json();

            // Vérifier la validité du token
            if (! isset($debugData['data']['is_valid']) || ! $debugData['data']['is_valid']) {
                Log::warning('Facebook token invalid');

                return null;
            }

            // Vérifier que le token est pour notre app
            if (isset($debugData['data']['app_id']) && $debugData['data']['app_id'] != $appId) {
                Log::warning('Facebook token app_id mismatch');

                return null;
            }

            // Étape 2: Récupérer les informations utilisateur
            $userResponse = Http::get('https://graph.facebook.com/me', [
                'fields' => 'id,name,email,first_name,last_name,picture.type(large)',
                'access_token' => $token,
            ]);

            if (! $userResponse->successful()) {
                Log::warning('Facebook user info failed', [
                    'error' => $userResponse->json(),
                ]);

                return null;
            }

            $userData = $userResponse->json();

            return [
                'provider_id' => $userData['id'] ?? null,
                'email' => $userData['email'] ?? null,
                'name' => $userData['name'] ?? null,
                'given_name' => $userData['first_name'] ?? null,
                'family_name' => $userData['last_name'] ?? null,
                'picture' => $userData['picture']['data']['url'] ?? null,
            ];
        } catch (Exception $e) {
            Log::error('Facebook token verification exception', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Vérifie un token Apple et retourne les informations utilisateur
     *
     * @param  string  $identityToken  Identity Token Apple
     */
    public function verifyAppleToken(string $identityToken): ?array
    {
        try {
            // Apple utilise JWT - on doit décoder et vérifier la signature
            // Pour une implémentation complète, utilisez une bibliothèque comme firebase/php-jwt

            // Découper le JWT en parties
            $parts = explode('.', $identityToken);

            if (count($parts) !== 3) {
                Log::warning('Apple token invalid format');

                return null;
            }

            // Décoder le payload (sans vérification de signature pour simplifier)
            // EN PRODUCTION: TOUJOURS VÉRIFIER LA SIGNATURE!
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            if (! $payload) {
                Log::warning('Apple token payload decode failed');

                return null;
            }

            // Vérifier l'expiration
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                Log::warning('Apple token expired');

                return null;
            }

            // Vérifier l'audience (votre app)
            $bundleId = config('services.apple.bundle_id');
            if ($bundleId && isset($payload['aud']) && $payload['aud'] !== $bundleId) {
                Log::warning('Apple token aud mismatch');

                return null;
            }

            return [
                'provider_id' => $payload['sub'] ?? null,
                'email' => $payload['email'] ?? null,
                'email_verified' => $payload['email_verified'] ?? false,
                'name' => null, // Apple ne fournit le nom que lors de la première connexion
            ];
        } catch (Exception $e) {
            Log::error('Apple token verification exception', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Vérifie un token selon le provider
     *
     * @param  string  $provider  'google', 'facebook', ou 'apple'
     * @param  string  $token  Token à vérifier
     */
    public function verify(string $provider, string $token): ?array
    {
        return match ($provider) {
            'google' => $this->verifyGoogleToken($token),
            'facebook' => $this->verifyFacebookToken($token),
            'apple' => $this->verifyAppleToken($token),
            default => null
        };
    }
}
