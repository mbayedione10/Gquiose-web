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
        $notifications = PushNotification::where('type', 'scheduled')
            ->where('status', 'pending')
            ->where('scheduled_at', '<=', now())
            ->get();

        foreach ($notifications as $notification) {
            SendScheduledNotification::dispatch($notification->id);
            $this->info("Notification #{$notification->id} mise en file d'attente");
        }

        $this->info("Total: {$notifications->count()} notifications programmées");
    }
}
