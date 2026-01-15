<?php

namespace App\Jobs;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use App\Services\Push\OneSignalService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBatchNotifications implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Le nombre de tentatives du job
     *
     * @var int
     */
    public $tries = 3;

    /**
     * Le timeout du job en secondes
     *
     * @var int
     */
    public $timeout = 300; // 5 minutes

    /**
     * @var PushNotification
     */
    protected $notification;

    /**
     * @var array
     */
    protected $userIds;

    /**
     * Create a new job instance.
     */
    public function __construct(PushNotification $notification, array $userIds)
    {
        $this->notification = $notification;
        $this->userIds = $userIds;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting batch notification send for notification {$this->notification->id} to " . count($this->userIds) . ' users');

            // Charger les utilisateurs avec un player_id OneSignal
            $users = Utilisateur::whereIn('id', $this->userIds)
                ->whereNotNull('onesignal_player_id')
                ->get();

            if ($users->isEmpty()) {
                Log::warning('No users with OneSignal player_id found for batch send');

                return;
            }

            // Envoyer via OneSignal
            $oneSignalService = app(OneSignalService::class);
            $result = $oneSignalService->sendToUsers($users->toArray(), $this->notification);

            // Mettre à jour les statistiques de la notification
            $this->notification->increment('sent_count', $result['success']);

            Log::info("Batch notification send completed: {$result['success']} success, {$result['failed']} failed");

        } catch (\Exception $e) {
            Log::error('Batch notification send error: ' . $e->getMessage());
            throw $e; // Relancer l'exception pour déclencher les tentatives
        }
    }

    /**
     * Le job a échoué
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Batch notification send failed permanently for notification {$this->notification->id}: " . $exception->getMessage());

        // Optionnel: marquer la notification comme échouée
        $this->notification->update([
            'status' => 'failed',
        ]);
    }
}
