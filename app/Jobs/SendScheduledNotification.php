<?php

namespace App\Jobs;

use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Support\Facades\Log;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendScheduledNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Le nombre de tentatives du job
     *
     * @var int
     */
    public $tries = 5;

    /**
     * Délai entre les tentatives (en secondes)
     *
     * @var array
     */
    public $backoff = [10, 30, 60, 120, 300]; // 10s, 30s, 1min, 2min, 5min

    /**
     * Le timeout du job en secondes
     *
     * @var int
     */
    public $timeout = 600; // 10 minutes

    protected $notificationId;

    public function __construct($notificationId)
    {
        $this->notificationId = $notificationId;
    }

    public function handle(PushNotificationService $service)
    {
        $notification = PushNotification::find($this->notificationId);

        if (! $notification) {
            Log::warning("Notification {$this->notificationId} not found");

            return;
        }

        if ($notification->status !== 'pending' && $notification->status !== 'sending') {
            Log::info("Notification {$this->notificationId} already processed with status: {$notification->status}");

            return;
        }

        try {
            Log::info("Sending scheduled notification {$this->notificationId}: {$notification->title}");

            // Use batch sending for better performance
            $service->sendNotificationInBatches($notification, 100);

            $notification->update([
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            Log::info("Successfully sent scheduled notification {$this->notificationId}");
        } catch (\Exception $e) {
            Log::error("Failed to send scheduled notification {$this->notificationId}: ".$e->getMessage());

            $notification->update([
                'status' => 'failed',
                'error_message' => $e->getMessage(),
            ]);

            throw $e; // Relancer pour déclencher les tentatives
        }
    }

    /**
     * Le job a échoué après toutes les tentatives
     */
    public function failed(\Throwable $exception): void
    {
        $notification = PushNotification::find($this->notificationId);

        if ($notification) {
            $notification->update([
                'status' => 'failed',
                'error_message' => 'Échec après ' . $this->attempts() . ' tentatives: ' . $exception->getMessage(),
            ]);
        }

        Log::error("Scheduled notification send failed permanently", [
            'notification_id' => $this->notificationId,
            'error' => $exception->getMessage(),
            'attempts' => $this->attempts(),
        ]);
    }
}
