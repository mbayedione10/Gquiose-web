
<?php

namespace App\Listeners;

use App\Events\CycleReminderTriggered;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class SendCycleReminderNotification implements ShouldQueue
{
    use InteractsWithQueue;

    protected $notificationService;

    public function __construct(PushNotificationService $notificationService)
    {
        $this->notificationService = $notificationService;
    }

    public function handle(CycleReminderTriggered $event): void
    {
        $user = $event->user;
        
        // Déterminer le message selon le type de rappel
        [$title, $message, $icon] = match($event->reminderType) {
            'period_coming' => [
                'Rappel de cycle',
                "Vos règles sont prévues dans {$event->daysUntil} jour(s). Préparez-vous !",
                '🩸'
            ],
            'ovulation' => [
                'Période d\'ovulation',
                "Vous êtes en période d\'ovulation. Restez informée !",
                '🌸'
            ],
            'fertile_window' => [
                'Fenêtre de fertilité',
                "Vous êtes dans votre fenêtre de fertilité.",
                '💫'
            ],
            default => [
                'Rappel de cycle',
                'Mise à jour concernant votre cycle menstruel',
                '🩸'
            ]
        };

        // Créer une notification push automatique
        $notification = PushNotification::create([
            'title' => $title,
            'message' => $message,
            'icon' => $icon,
            'action' => 'cycle_tracker',
            'type' => 'automatic',
            'target_audience' => 'filtered',
            'filters' => ['user_ids' => [$user->id]],
            'status' => 'pending',
        ]);

        // Envoyer immédiatement à cet utilisateur spécifique
        $this->notificationService->sendPushNotification($notification, [$user]);

        $notification->update([
            'status' => 'sent',
            'sent_at' => now(),
        ]);
    }
}
