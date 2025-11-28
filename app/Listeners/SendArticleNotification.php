<?php

namespace App\Listeners;

use App\Events\NewArticlePublished;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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

        Log::info("Event triggered: New article published - {$article->titre}");

        // Créer la notification push
        $notification = PushNotification::create([
            'title' => '📚 Nouvel article disponible !',
            'message' => substr($article->titre, 0, 100) . '...',
            'icon' => '📚',
            'action' => 'article/' . $article->slug,
            'image' => $article->image,
            'type' => 'automatic',
            'target_audience' => 'all',
            'status' => 'sending',
        ]);

        // Envoyer la notification en batch pour optimisation
        $notificationService = app(PushNotificationService::class);
        $notificationService->sendNotificationInBatches($notification, 100);

        Log::info("Article notification dispatched: {$notification->id}");
    }
}