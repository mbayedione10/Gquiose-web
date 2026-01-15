<?php

namespace App\Observers;

use App\Models\Message;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Support\Facades\Log;

class MessageObserver
{
    /**
     * Gérer l'événement "created" pour les messages du forum.
     * Envoyer une notification aux abonnés du thème.
     */
    public function created(Message $message)
    {
        // Ne notifier que si le message est approuvé
        if (!$message->status) {
            return;
        }

        $this->sendNewMessageNotification($message);
    }

    /**
     * Gérer l'événement "updated" pour les messages.
     * Notifier si le message vient d'être approuvé.
     */
    public function updated(Message $message)
    {
        // Si le message vient d'être approuvé
        if ($message->status && $message->isDirty('status') && $message->getOriginal('status') == false) {
            $this->sendNewMessageNotification($message);
        }
    }

    /**
     * Envoyer une notification pour un nouveau message dans le forum
     */
    protected function sendNewMessageNotification(Message $message)
    {
        try {
            $author = $message->utilisateur;
            $theme = $message->theme;

            if (!$author || !$theme) {
                return;
            }

            // Créer la notification
            $notification = PushNotification::create([
                'title' => '💬 Nouveau message dans le forum',
                'message' => $author->prenom ? "{$author->prenom} a posté dans {$theme->name}" : "Nouveau message dans {$theme->name}",
                'type' => 'instant',
                'category' => 'forum',
                'target_audience' => 'filtered',
                'filters' => json_encode([
                    'forum_notifications' => true,
                    'exclude_user_id' => $message->utilisateur_id, // Ne pas notifier l'auteur
                ]),
                'action' => json_encode([
                    'type' => 'forum_message',
                    'message_id' => $message->id,
                    'theme_id' => $message->theme_id,
                ]),
                'icon' => 'forum',
                'status' => 'pending',
            ]);

            Log::info("New forum message notification created", [
                'message_id' => $message->id,
                'notification_id' => $notification->id,
            ]);

            // Envoyer la notification en arrière-plan
            dispatch(function () use ($notification) {
                $service = app(PushNotificationService::class);
                $service->sendNotificationInBatches($notification, 100);
            })->afterResponse();

        } catch (\Exception $e) {
            Log::error("Failed to send notification for new forum message", [
                'message_id' => $message->id,
                'error' => $e->getMessage(),
            ]);
        }
    }
}
