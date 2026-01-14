<?php

namespace App\Jobs;

use App\Models\PushNotification;
use App\Services\Push\APNsService;
use App\Services\Push\FCMService;
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
     * @var string
     */
    protected $platform; // 'fcm', 'apns', or 'all'

    /**
     * Create a new job instance.
     */
    public function __construct(PushNotification $notification, array $userIds, string $platform = 'all')
    {
        $this->notification = $notification;
        $this->userIds = $userIds;
        $this->platform = $platform;
    }

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        try {
            Log::info("Starting batch notification send for notification {$this->notification->id} to ".count($this->userIds).' users');

            // Charger les utilisateurs
            $users = \App\Models\Utilisateur::whereIn('id', $this->userIds)->get();

            if ($users->isEmpty()) {
                Log::warning('No users found for batch send');

                return;
            }

            // Séparer les utilisateurs par plateforme
            $fcmUsers = [];
            $apnsUsers = [];

            foreach ($users as $user) {
                if (($this->platform === 'all' || $this->platform === 'fcm') && ! empty($user->fcm_token)) {
                    $fcmUsers[] = $user;
                }
                if (($this->platform === 'all' || $this->platform === 'apns') && ! empty($user->apns_token)) {
                    $apnsUsers[] = $user;
                }
            }

            $totalSuccess = 0;
            $totalFailed = 0;

            // Envoyer via FCM
            if (! empty($fcmUsers)) {
                Log::info('Sending to '.count($fcmUsers).' FCM users');
                $fcmService = app(FCMService::class);
                $result = $fcmService->sendToMultipleDevices($fcmUsers, $this->notification);

                $totalSuccess += $result['success'];
                $totalFailed += $result['failed'];
            }

            // Envoyer via APNs
            if (! empty($apnsUsers)) {
                Log::info('Sending to '.count($apnsUsers).' APNs users');
                $apnsService = app(APNsService::class);
                $result = $apnsService->sendToMultipleDevices($apnsUsers, $this->notification);

                $totalSuccess += $result['success'];
                $totalFailed += $result['failed'];
            }

            // Mettre à jour les statistiques de la notification
            $this->notification->increment('sent_count', $totalSuccess);

            Log::info("Batch notification send completed: {$totalSuccess} success, {$totalFailed} failed");

        } catch (\Exception $e) {
            Log::error('Batch notification send error: '.$e->getMessage());
            throw $e; // Relancer l'exception pour déclencher les tentatives
        }
    }

    /**
     * Le job a échoué
     */
    public function failed(\Throwable $exception): void
    {
        Log::error("Batch notification send failed permanently for notification {$this->notification->id}: ".$exception->getMessage());

        // Optionnel: marquer la notification comme échouée
        $this->notification->update([
            'status' => 'failed',
        ]);
    }
}
