<?php

namespace App\Listeners;

use App\Events\NewQuizPublished;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

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
        $quiz = $event->quiz;

        Log::info("Event triggered: New quiz published - {$quiz->name}");

        // Créer la notification push
        $notification = PushNotification::create([
            'title' => '❓ Nouveau quiz disponible !',
            'message' => 'Testez vos connaissances : ' . substr($quiz->name, 0, 80),
            'icon' => '❓',
            'action' => 'quiz/' . $quiz->id,
            'type' => 'automatic',
            'target_audience' => 'all',
            'status' => 'sending',
        ]);

        // Envoyer la notification en batch
        $notificationService = app(PushNotificationService::class);
        $notificationService->sendNotificationInBatches($notification, 100);

        Log::info("Quiz notification dispatched: {$notification->id}");
    }
}