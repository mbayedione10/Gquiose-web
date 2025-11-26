<?php

namespace App\Listeners;

use App\Events\UserMentioned;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Support\Str;

class SendMentionNotification
{
    protected PushNotificationService $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(UserMentioned $event): void
    {
        $mentionedUser = $event->mentionedUser;
        $mentioner = $event->mentioner;

        // Don't notify if mentioning yourself
        if ($mentionedUser->id === $mentioner->id) {
            return;
        }

        // Check notification preferences
        if (!$mentionedUser->notificationPreferences?->forum_notifications) {
            return;
        }

        // Create preview of content (first 100 chars)
        $preview = Str::limit($event->content, 100);

        // Build data payload based on context
        $data = [];
        if ($event->context === 'message') {
            $data['message_id'] = $event->contextId;
        } elseif ($event->context === 'chat') {
            $data['chat_id'] = $event->contextId;
        }

        // Create notification
        $notification = PushNotification::create([
            'title' => "{$mentioner->prenom} vous a mentionné",
            'message' => $preview,
            'icon' => '💬',
            'action' => 'forum_mention',
            'type' => 'automatic',
            'target_audience' => 'custom',
            'status' => 'pending',
            'data' => json_encode($data),
        ]);

        // Send notification to the mentioned user specifically
        $this->pushNotificationService->sendPushNotification($notification, [$mentionedUser]);
    }
}
