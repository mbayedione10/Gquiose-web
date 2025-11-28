<?php

namespace App\Listeners;

use App\Events\NewHealthCenterAdded;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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

        Log::info("Event triggered: New health center added - {$structure->name}");

        // Créer la notification push ciblée par ville
        $notification = PushNotification::create([
            'title' => '🏥 Nouveau centre de santé !',
            'message' => $structure->name . ' - ' . $structure->ville->name,
            'icon' => '🏥',
            'action' => 'health_center/' . $structure->id,
            'type' => 'automatic',
            'target_audience' => 'filtered',
            'filters' => [
                'ville_id' => $structure->ville_id,
            ],
            'status' => 'sending',
        ]);

        // Envoyer la notification en batch
        $notificationService = app(PushNotificationService::class);
        $notificationService->sendNotificationInBatches($notification, 100);

        Log::info("Health center notification dispatched: {$notification->id}");
    }
}