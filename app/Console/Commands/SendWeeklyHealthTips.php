<?php

namespace App\Console\Commands;

use App\Models\Conseil;
use App\Models\PushNotification;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class SendWeeklyHealthTips extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'notifications:send-weekly-health-tips';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Envoie un conseil santé hebdomadaire à tous les utilisateurs';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Récupérer un conseil aléatoire
        $conseil = Conseil::inRandomOrder()->first();

        if (!$conseil) {
            $this->error('Aucun conseil santé disponible dans la base de données');
            return Command::FAILURE;
        }

        // Créer une notification push
        $notification = PushNotification::create([
            'title' => 'Conseil santé de la semaine',
            'message' => $conseil->message,
            'icon' => '💡',
            'action' => 'health_tips',
            'type' => 'automatic',
            'target_audience' => 'all',
            'status' => 'pending',
        ]);

        // Envoyer la notification
        $service = app(PushNotificationService::class);
        $service->sendNotification($notification);

        $this->info("Conseil santé envoyé avec succès : {$conseil->message}");
        return Command::SUCCESS;
    }
}
