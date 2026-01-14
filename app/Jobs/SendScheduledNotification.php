<?php

namespace App\Jobs;

use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendScheduledNotification implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

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
            ]);

            throw $e;
        }
    }
}
