<?php

namespace App\Listeners;

use App\Events\NewHealthCenterAdded;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendHealthCenterNotification implements ShouldQueue
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
    public function handle(NewHealthCenterAdded $event): void
    {
        $structure = $event->structure;

        // Créer une notification push automatique pour la ville concernée
        $notification = PushNotification::create([
            'title' => 'Nouveau centre de santé ajouté',
            'message' => "Le centre « {$structure->name} » a été ajouté près de chez vous !",
            'icon' => '🏥',
            'action' => 'health_center/' . $structure->id,
            'type' => 'automatic',
            'target_audience' => 'filtered',
            'filters' => [
                'ville_id' => $structure->ville_id,
            ],
            'status' => 'pending',
        ]);

        // Envoyer immédiatement
        $this->notificationService->sendNotification($notification);
    }
}
