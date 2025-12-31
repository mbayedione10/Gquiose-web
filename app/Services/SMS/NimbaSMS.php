<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Exception;

class NimbaSMS implements SMSInterface
{
    protected string $baseUrl;
    protected string $serviceId;
    protected string $secret;
    protected string $senderName;

    public function __construct()
    {
        $this->baseUrl = config('services.sms.nimba.base_url', 'https://api.nimbasms.com');
        $this->serviceId = config('services.sms.nimba.service_id');
        $this->secret = config('services.sms.nimba.secret');
        $this->senderName = config('services.sms.nimba.sender_name', 'GQUIOSE');

        if (!$this->serviceId || !$this->secret) {
            throw new Exception('NimbaSMS credentials not configured');
        }
    }

    /**
     * Envoie un SMS via NimbaSMS API
     */
    public function send(string $to, string $message): array
    {
        try {
            $response = Http::withBasicAuth($this->serviceId, $this->secret)
                ->timeout(30)
                ->post("{$this->baseUrl}/v1/messages", [
                    'sender_name' => $this->senderName,
                    'to' => [$this->formatPhoneNumber($to)],
                    'message' => $message,
                ]);

            if ($response->successful()) {
                $data = $response->json();

                Log::info('SMS sent via NimbaSMS', [
                    'to' => $this->maskPhone($to),
                    'message_id' => $data['id'] ?? null,
                    'status' => $data['status'] ?? 'sent'
                ]);

                return [
                    'success' => true,
                    'message_id' => $data['id'] ?? null,
                    'error' => null
                ];
            }

            $errorMessage = $response->json('message') ?? $response->body();

            Log::error('NimbaSMS API error', [
                'to' => $this->maskPhone($to),
                'status_code' => $response->status(),
                'error' => $errorMessage
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $errorMessage
            ];
        } catch (Exception $e) {
            Log::error('NimbaSMS request failed', [
                'to' => $this->maskPhone($to),
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    /**
     * Vérifie le statut d'un message envoyé
     */
    public function getStatus(string $messageId): array
    {
        try {
            $response = Http::withBasicAuth($this->serviceId, $this->secret)
                ->timeout(30)
                ->get("{$this->baseUrl}/v1/messages/{$messageId}");

            if ($response->successful()) {
                $data = $response->json();
                $status = $data['status'] ?? 'unknown';

                return [
                    'status' => $status,
                    'delivered' => in_array($status, ['delivered', 'sent'])
                ];
            }

            Log::error('NimbaSMS status check failed', [
                'message_id' => $messageId,
                'status_code' => $response->status()
            ]);

            return [
                'status' => 'unknown',
                'delivered' => false
            ];
        } catch (Exception $e) {
            Log::error('NimbaSMS status check error', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'unknown',
                'delivered' => false
            ];
        }
    }

    /**
     * Formate le numéro de téléphone pour NimbaSMS
     * NimbaSMS accepte les formats: +224XXXXXXXX ou 224XXXXXXXX
     */
    private function formatPhoneNumber(string $phone): string
    {
        // Supprime les espaces et caractères spéciaux
        $phone = preg_replace('/[\s\-\.\(\)]/', '', $phone);

        // Si le numéro commence par 00, remplacer par +
        if (str_starts_with($phone, '00')) {
            $phone = '+' . substr($phone, 2);
        }

        return $phone;
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
}
