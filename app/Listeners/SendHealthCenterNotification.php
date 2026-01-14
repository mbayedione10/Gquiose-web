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

    protected $evaluationTriggerService;

    /**
     * Create the event listener.
     */
    public function __construct(
        PushNotificationService $notificationService,
        \App\Services\EvaluationTriggerService $evaluationTriggerService
    ) {
        $this->notificationService = $notificationService;
        $this->evaluationTriggerService = $evaluationTriggerService;
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
            'message' => $structure->name.' - '.$structure->ville->name,
            'icon' => '🏥',
            'action' => 'health_center/'.$structure->id,
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

        // Programmer une évaluation 3 jours après pour les utilisateurs de cette ville
        $villeUserIds = \App\Models\Utilisateur::where('ville_id', $structure->ville_id)
            ->where('status', true)
            ->pluck('id')
            ->toArray();

        if (! empty($villeUserIds)) {
            $this->evaluationTriggerService->triggerAutoEvaluation('structure', $structure->id, [
                'delay_days' => 3,
                'target_users' => $villeUserIds,
                'evaluation_type' => 'satisfaction_structure',
            ]);
        }
    }
}
