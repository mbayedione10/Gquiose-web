<?php

namespace App\Services\SMS;

use Exception;
use Illuminate\Support\Facades\Log;
use Vonage\Client;
use Vonage\Client\Credentials\Basic;
use Vonage\SMS\Message\SMS;

class VonageSMS implements SMSInterface
{
    protected $client;

    protected $from;

    public function __construct()
    {
        $key = config('services.sms.vonage.key');
        $secret = config('services.sms.vonage.secret');
        $this->from = config('services.sms.vonage.from');

        if (! $key || ! $secret || ! $this->from) {
            throw new Exception('Vonage credentials not configured');
        }

        $basic = new Basic($key, $secret);
        $this->client = new Client($basic);
    }

    public function send(string $to, string $message): array
    {
        try {
            $response = $this->client->sms()->send(
                new SMS($to, $this->from, $message)
            );

            $messageData = $response->current();

            Log::info('SMS sent via Vonage', [
                'to' => $to,
                'message_id' => $messageData->getMessageId(),
                'status' => $messageData->getStatus(),
            ]);

            return [
                'success' => $messageData->getStatus() == 0,
                'message_id' => $messageData->getMessageId(),
                'error' => $messageData->getStatus() != 0 ? 'SMS failed with status: '.$messageData->getStatus() : null,
            ];
        } catch (Exception $e) {
            Log::error('Vonage SMS failed', [
                'to' => $to,
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    public function getStatus(string $messageId): array
    {
        // Vonage ne fournit pas d'API simple pour vérifier le statut après envoi
        // Il faut configurer des webhooks pour les receipts de livraison
        Log::warning('Vonage status check not implemented - use delivery receipts webhooks');

        return [
            'status' => 'unknown',
            'delivered' => false,
        ];
    }
}
