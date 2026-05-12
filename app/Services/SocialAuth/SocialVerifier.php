<?php

namespace App\Services\SocialAuth;

use Exception;
use Illuminate\Support\Facades\Cache;
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
     * Effectue une vérification complète de la signature JWT via les clés publiques Apple.
     *
     * @param  string  $identityToken  Identity Token Apple (JWT)
     */
    public function verifyAppleToken(string $identityToken): ?array
    {
        try {
            $parts = explode('.', $identityToken);
            if (count($parts) !== 3) {
                Log::warning('Apple token invalid format');
                return null;
            }

            $header = json_decode(base64_decode(strtr($parts[0], '-_', '+/')), true);
            $payload = json_decode(base64_decode(strtr($parts[1], '-_', '+/')), true);

            if (! $header || ! $payload) {
                Log::warning('Apple token decode failed');
                return null;
            }

            // Vérifier expiration avant tout appel réseau
            if (isset($payload['exp']) && $payload['exp'] < time()) {
                Log::warning('Apple token expired');
                return null;
            }

            // Récupérer les clés publiques Apple (mises en cache 1h)
            $jwks = Cache::remember('apple_public_keys', 3600, function () {
                $response = Http::timeout(10)->get('https://appleid.apple.com/auth/keys');
                return $response->json();
            });

            // Trouver la clé correspondant au kid du token
            $kid = $header['kid'] ?? null;
            $matchingKey = null;
            foreach ($jwks['keys'] ?? [] as $key) {
                if ($key['kid'] === $kid) {
                    $matchingKey = $key;
                    break;
                }
            }

            // Si la clé n'est pas trouvée, vider le cache et réessayer (rotation de clés Apple)
            if (! $matchingKey) {
                Cache::forget('apple_public_keys');
                $response = Http::timeout(10)->get('https://appleid.apple.com/auth/keys');
                $jwks = $response->json();
                foreach ($jwks['keys'] ?? [] as $key) {
                    if ($key['kid'] === $kid) {
                        $matchingKey = $key;
                        break;
                    }
                }
            }

            if (! $matchingKey) {
                Log::warning('Apple token: no matching public key', ['kid' => $kid]);
                return null;
            }

            // Convertir la clé JWK en PEM pour openssl_verify
            $pem = $this->jwkToPem($matchingKey);
            if (! $pem) {
                Log::warning('Apple token: JWK to PEM conversion failed');
                return null;
            }

            // Vérifier la signature RS256
            $dataToVerify = $parts[0].'.'.$parts[1];
            $signature = base64_decode(strtr($parts[2], '-_', '+/'));
            $publicKey = openssl_pkey_get_public($pem);

            if (! $publicKey) {
                Log::warning('Apple token: invalid public key from PEM');
                return null;
            }

            $verified = openssl_verify($dataToVerify, $signature, $publicKey, OPENSSL_ALGO_SHA256);

            if ($verified !== 1) {
                Log::warning('Apple token signature verification failed');
                return null;
            }

            // Vérifier l'issuer
            if (($payload['iss'] ?? '') !== 'https://appleid.apple.com') {
                Log::warning('Apple token iss mismatch', ['iss' => $payload['iss'] ?? 'missing']);
                return null;
            }

            // Vérifier l'audience (bundle ID de l'app)
            $bundleId = config('services.apple.bundle_id');
            if ($bundleId && isset($payload['aud']) && $payload['aud'] !== $bundleId) {
                Log::warning('Apple token aud mismatch', [
                    'expected' => $bundleId,
                    'received' => $payload['aud'],
                ]);
                return null;
            }

            return [
                'provider_id' => $payload['sub'] ?? null,
                'email' => $payload['email'] ?? null, // null si l'utilisateur a caché son email
                'email_verified' => $payload['email_verified'] ?? false,
                'name' => null, // Apple ne fournit le nom qu'à la première connexion (via fullName côté app)
            ];
        } catch (Exception $e) {
            Log::error('Apple token verification exception', [
                'error' => $e->getMessage(),
            ]);

            return null;
        }
    }

    /**
     * Convertit une clé publique RSA au format JWK en PEM (SubjectPublicKeyInfo DER encodé).
     */
    private function jwkToPem(array $jwk): ?string
    {
        if (! isset($jwk['n'], $jwk['e'])) {
            return null;
        }

        $n = base64_decode(strtr($jwk['n'], '-_', '+/'));
        $e = base64_decode(strtr($jwk['e'], '-_', '+/'));

        $nDer = $this->derEncodeInteger($n);
        $eDer = $this->derEncodeInteger($e);
        $rsaKeyBody = $nDer.$eDer;
        $rsaKeySeq = "\x30".$this->derEncodeLength(strlen($rsaKeyBody)).$rsaKeyBody;

        // BIT STRING: préfixer 0x00 (aucun bit inutilisé)
        $bitString = "\x03".$this->derEncodeLength(strlen($rsaKeySeq) + 1)."\x00".$rsaKeySeq;

        // AlgorithmIdentifier pour rsaEncryption (OID 1.2.840.113549.1.1.1)
        $algorithmId = "\x30\x0d\x06\x09\x2a\x86\x48\x86\xf7\x0d\x01\x01\x01\x05\x00";

        // SubjectPublicKeyInfo SEQUENCE
        $spki = "\x30".$this->derEncodeLength(strlen($algorithmId.$bitString)).$algorithmId.$bitString;

        return "-----BEGIN PUBLIC KEY-----\n".chunk_split(base64_encode($spki), 64, "\n")."-----END PUBLIC KEY-----";
    }

    private function derEncodeLength(int $length): string
    {
        if ($length < 128) {
            return chr($length);
        }
        $temp = '';
        while ($length > 0) {
            $temp = chr($length & 0xff).$temp;
            $length >>= 8;
        }

        return chr(0x80 | strlen($temp)).$temp;
    }

    private function derEncodeInteger(string $bytes): string
    {
        $bytes = ltrim($bytes, "\x00");
        if ($bytes === '') {
            $bytes = "\x00";
        }
        // Préfixer 0x00 si le bit de poids fort est à 1 (indiquer nombre positif)
        if (ord($bytes[0]) & 0x80) {
            $bytes = "\x00".$bytes;
        }

        return "\x02".$this->derEncodeLength(strlen($bytes)).$bytes;
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
