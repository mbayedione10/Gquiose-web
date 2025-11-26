<?php

namespace App\Listeners;

use App\Events\NewVideoPublished;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendVideoNotification implements ShouldQueue
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
    public function handle(NewVideoPublished $event): void
    {
        $video = $event->video;

        // Créer une notification push automatique
        $notification = PushNotification::create([
            'title' => 'Nouvelle vidéo disponible',
            'message' => "Une nouvelle vidéo « {$video->name} » est maintenant disponible !",
            'icon' => '🎥',
            'action' => 'video/' . $video->id,
            'type' => 'automatic',
            'target_audience' => 'all',
            'status' => 'pending',
        ]);

        // Envoyer immédiatement
        $this->notificationService->sendNotification($notification);
    }
}
