<?php

namespace App\Listeners;

use App\Events\NewVideoPublished;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
    public function handle(NewVideoPublished $event)
    {
        $video = $event->video;

        Log::info("Event triggered: New video published - {$video->titre}");

        // Créer la notification push
        $notification = PushNotification::create([
            'title' => '🎥 Nouvelle vidéo disponible !',
            'message' => substr($video->titre, 0, 100),
            'icon' => '🎥',
            'action' => 'video/'.$video->id,
            'image' => $video->image,
            'type' => 'automatic',
            'target_audience' => 'all',
            'status' => 'sending',
        ]);

        // Envoyer la notification en batch
        $notificationService = app(PushNotificationService::class);
        $notificationService->sendNotificationInBatches($notification, 100);

        Log::info("Video notification dispatched: {$notification->id}");
    }
}
