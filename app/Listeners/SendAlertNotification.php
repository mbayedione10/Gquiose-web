<?php

namespace App\Listeners;

use App\Events\AlertCreated;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendAlertNotification implements ShouldQueue
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
    public function handle(AlertCreated $event): void
    {
        $alerte = $event->alerte;

        // Créer une notification push automatique
        $notification = PushNotification::create([
            'title' => 'Nouvelle alerte signalée',
            'message' => "Une alerte de type '{$alerte->type}' a été signalée dans votre région.",
            'icon' => 'alert_icon',
            'action' => 'alert_details',
            'type' => 'automatic',
            'target_audience' => 'filtered',
            'filters' => [
                'ville_id' => $alerte->ville_id,
            ],
            'status' => 'pending',
        ]);

        // Envoyer immédiatement
        $this->notificationService->sendNotification($notification);
    }
}
