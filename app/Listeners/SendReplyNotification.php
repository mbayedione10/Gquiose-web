<?php

namespace App\Listeners;

use App\Events\MessageReplied;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Support\Str;

class SendReplyNotification
{
    protected PushNotificationService $pushNotificationService;

    public function __construct(PushNotificationService $pushNotificationService)
    {
        $this->pushNotificationService = $pushNotificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(MessageReplied $event): void
    {
        $chat = $event->chat;
        $message = $event->message;

        // Load relationships
        $message->load('utilisateur');
        $chat->load('utilisateur');

        $messageAuthor = $message->utilisateur;
        $replier = $chat->utilisateur;

        // Don't notify if replying to own message
        if ($messageAuthor->id === $replier->id) {
            return;
        }

        // Check notification preferences
        if (! $messageAuthor->notificationPreferences?->forum_notifications) {
            return;
        }

        // Create preview of reply (first 100 chars)
        $preview = Str::limit($chat->message, 100);

        // Create notification
        $notification = PushNotification::create([
            'title' => "{$replier->prenom} a répondu à votre discussion",
            'message' => $preview,
            'icon' => '💬',
            'action' => 'forum_reply',
            'type' => 'automatic',
            'target_audience' => 'custom',
            'status' => 'pending',
            'data' => json_encode([
                'message_id' => $message->id,
                'chat_id' => $chat->id,
            ]),
        ]);

        // Send notification to the message author specifically
        $this->pushNotificationService->sendPushNotification($notification, [$messageAuthor]);
    }
}
