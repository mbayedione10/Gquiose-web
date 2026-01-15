<?php

namespace App\Observers;

use App\Models\Chat;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

class ChatObserver
{
    /**
     * Gérer l'événement "created" pour les réponses aux messages du forum.
     * Notifier l'auteur du message original.
     */
    public function created(Chat $chat)
    {
        $this->sendChatReplyNotification($chat);
    }

    /**
     * Envoyer une notification à l'auteur du message quand quelqu'un répond
     */
    protected function sendChatReplyNotification(Chat $chat)
    {
        try {
            $message = $chat->message;
            $replier = $chat->utilisateur;

            if (!$message || !$replier || !$message->utilisateur) {
                return;
            }

            // Ne pas notifier si c'est l'auteur qui répond à son propre message
            if ($message->utilisateur_id === $chat->utilisateur_id) {
                return;
            }

            $originalAuthor = $message->utilisateur;

            // Vérifier les préférences de notification de l'auteur
            $preferences = $originalAuthor->notificationPreferences;
            if (!$preferences || !$preferences->forum_notifications || !$preferences->notifications_enabled) {
                return;
            }

            // Créer une notification ciblée uniquement pour l'auteur du message
            $notification = PushNotification::create([
                'title' => '💬 Nouvelle réponse à votre message',
                'message' => $replier->prenom ? "{$replier->prenom} a répondu à votre message" : "Quelqu'un a répondu à votre message",
                'type' => 'instant',
                'category' => 'forum',
                'target_audience' => 'specific',
                'action' => json_encode([
                    'type' => 'forum_reply',
                    'message_id' => $message->id,
                    'chat_id' => $chat->id,
                ]),
                'icon' => 'forum',
                'status' => 'pending',
            ]);

            Log::info("Forum reply notification created", [
                'chat_id' => $chat->id,
                'notification_id' => $notification->id,
                'recipient_id' => $originalAuthor->id,
            ]);

            // Envoyer directement à l'auteur
            dispatch(function () use ($notification, $originalAuthor) {
                $service = app(\App\Services\Push\OneSignalService::class);
                $service->sendToUser($originalAuthor, $notification);
            })->afterResponse();

        } catch (\Exception $e) {
            Log::error("Failed to send notification for forum reply", [
                'chat_id' => $chat->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
