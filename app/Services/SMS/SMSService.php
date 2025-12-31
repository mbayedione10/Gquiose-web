<?php

namespace App\Services\SMS;

use Exception;
use Illuminate\Support\Facades\Log;

class SMSService
{
    protected $provider;

    public function __construct()
    {
        $providerName = config('services.sms.provider', 'twilio');

        $this->provider = match ($providerName) {
            'twilio' => new TwilioSMS(),
            'vonage' => new VonageSMS(),
            'nimba' => new NimbaSMS(),
            default => throw new Exception("Unsupported SMS provider: {$providerName}")
        };
    }

    /**
     * Envoie un code de vérification par SMS
     */
    public function sendVerificationCode(string $phone, string $code): bool
    {
        $message = "Votre code de vérification G Qui Ose est: {$code}. Valide pendant 10 minutes.";

        $result = $this->provider->send($phone, $message);

        if ($result['success']) {
            Log::info('Verification code sent', [
                'phone' => $this->maskPhone($phone),
                'message_id' => $result['message_id']
            ]);
            return true;
        }

        Log::error('Verification code failed', [
            'phone' => $this->maskPhone($phone),
            'error' => $result['error']
        ]);

        return false;
    }

    /**
     * Envoie un code de réinitialisation de mot de passe par SMS
     */
    public function sendPasswordResetCode(string $phone, string $code): bool
    {
        $message = "Votre code de réinitialisation G Qui Ose est: {$code}. Valide pendant 10 minutes.";

        $result = $this->provider->send($phone, $message);

        if ($result['success']) {
            Log::info('Password reset code sent', [
                'phone' => $this->maskPhone($phone),
                'message_id' => $result['message_id']
            ]);
            return true;
        }

        Log::error('Password reset code failed', [
            'phone' => $this->maskPhone($phone),
            'error' => $result['error']
        ]);

        return false;
    }

    /**
     * Masque un numéro de téléphone pour les logs (RGPD)
     */
    private function maskPhone(string $phone): string
    {
        if (strlen($phone) <= 4) {
            return '****';
        }

        return substr($phone, 0, 4) . str_repeat('*', strlen($phone) - 4);
    }

    /**
     * Vérifie le statut d'un message
     */
    public function getMessageStatus(string $messageId): array
    {
        return $this->provider->getStatus($messageId);
    }
}
