<?php

namespace App\Console\Commands;

use App\Jobs\SendScheduledNotification;
use App\Models\PushNotification;
use Illuminate\Console\Command;

class SendScheduledNotifications extends Command
{
    protected $signature = 'notifications:send-scheduled';
    protected $description = 'Envoie les notifications programmées qui sont dues';

    public function handle()
    {
        $this->info('Checking for scheduled notifications...');

        // Get notifications that are scheduled and due
        $notifications = PushNotification::where('type', 'scheduled')
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        if ($notifications->isEmpty()) {
            $this->info('No scheduled notifications to send.');
            return 0;
        }

        foreach ($notifications as $notification) {
            $this->info("Sending notification: {$notification->title}");

            // Dispatch the job to send the notification
            SendScheduledNotification::dispatch($notification->id);

            // Update status to sending
            $notification->update([
                'status' => 'sending',
            ]);
        }

        $this->info("Dispatched {$notifications->count()} scheduled notifications.");

        return 0;
    }
}