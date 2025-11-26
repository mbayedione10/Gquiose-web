<?php

namespace App\Listeners;

use App\Events\NewQuizPublished;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendQuizNotification implements ShouldQueue
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
    public function handle(NewQuizPublished $event): void
    {
        $thematique = $event->thematique;

        // Créer une notification push automatique
        $notification = PushNotification::create([
            'title' => 'Nouveau quiz disponible',
            'message' => "Un nouveau quiz sur « {$thematique->name} » est maintenant disponible !",
            'icon' => '❓',
            'action' => 'quiz/' . $thematique->id,
            'type' => 'automatic',
            'target_audience' => 'all',
            'status' => 'pending',
        ]);

        // Envoyer immédiatement
        $this->notificationService->sendNotification($notification);
    }
}
