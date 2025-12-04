<?php

namespace App\Services\SMS;

use Illuminate\Support\Facades\Log;
use Twilio\Rest\Client;
use Exception;

class TwilioSMS implements SMSInterface
{
    protected $client;
    protected $from;

    public function __construct()
    {
        $sid   = config('services.sms.twilio.sid');
        $token = config('services.sms.twilio.token');
        $this->from = config('services.sms.twilio.from');

        if (!$sid || !$token || !$this->from) {
            throw new Exception('Twilio credentials not configured');
        }

        $this->client = new Client($sid, $token);
    }

    public function send(string $to, string $message): array
    {
        try {
            $result = $this->client->messages->create($to, [
                'from' => $this->from,
                'body' => $message
            ]);

            Log::info('SMS sent via Twilio', [
                'to' => $to,
                'message_id' => $result->sid,
                'status' => $result->status
            ]);

            return [
                'success' => true,
                'message_id' => $result->sid,
                'error' => null
            ];
        } catch (Exception $e) {
            Log::error('Twilio SMS failed', [
                'to' => $to,
                'error' => $e->getMessage()
            ]);

            return [
                'success' => false,
                'message_id' => null,
                'error' => $e->getMessage()
            ];
        }
    }

    public function getStatus(string $messageId): array
    {
        try {
            $message = $this->client->messages($messageId)->fetch();

            return [
                'status' => $message->status,
                'delivered' => in_array($message->status, ['delivered', 'sent'])
            ];
        } catch (Exception $e) {
            Log::error('Twilio status check failed', [
                'message_id' => $messageId,
                'error' => $e->getMessage()
            ]);

            return [
                'status' => 'unknown',
                'delivered' => false
            ];
        }
    }
}
