<?php

namespace App\Listeners;

use App\Events\NewArticlePublished;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendArticleNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    /**
     * Create the event listener.
     */
    public function __construct(PushNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     */
    public function handle(NewArticlePublished $event): void
    {
        $article = $event->article;

        // Créer une notification push automatique
        $notification = PushNotification::create([
            'title' => 'Nouvel article publié',
            'message' => $article->titre,
            'icon' => 'article_icon',
            'action' => 'article/' . $article->id,
            'image' => $article->image,
            'type' => 'automatic',
            'target_audience' => 'all',
            'status' => 'pending',
        ]);

        // Envoyer immédiatement
        $this->notificationService->sendNotification($notification);
    }
}
