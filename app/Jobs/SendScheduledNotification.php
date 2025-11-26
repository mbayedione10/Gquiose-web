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

        if ($notification && $notification->status === 'pending') {
            $service->sendNotification($notification);
        }
    }
}
