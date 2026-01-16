<?php

namespace App\Console\Commands;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use App\Services\PushNotificationService;
use Illuminate\Console\Command;

class TestNotificationSend extends Command
{
    protected $signature = 'notifications:test {user_id?}';

    protected $description = 'Tester l\'envoi d\'une notification à un utilisateur';

    public function handle()
    {
        $userId = $this->argument('user_id');

        if (!$userId) {
            // Prendre le premier utilisateur avec player_id
            $user = Utilisateur::whereNotNull('onesignal_player_id')
                ->where('status', true)
                ->first();

            if (!$user) {
                $this->error('Aucun utilisateur avec player_id trouvé !');
                return 1;
            }

            $userId = $user->id;
            $this->info("Utilisateur sélectionné automatiquement: #{$userId} - {$user->nom} {$user->prenom}");
        } else {
            $user = Utilisateur::find($userId);
            if (!$user) {
                $this->error("Utilisateur #{$userId} introuvable !");
                return 1;
            }

            if (!$user->onesignal_player_id) {
                $this->error("L'utilisateur #{$userId} n'a pas de player_id OneSignal !");
                return 1;
            }

            $this->info("Utilisateur: #{$userId} - {$user->nom} {$user->prenom}");
        }

        // Créer une notification de test
        $this->info('Création de la notification de test...');
        
        $notification = PushNotification::create([
            'title' => 'Test depuis Console',
            'message' => 'Ceci est un test d\'envoi depuis la ligne de commande',
            'icon' => '🔔',
            'type' => 'manual',
            'target_audience' => 'all',
            'status' => 'pending',
            'category' => 'admin',
        ]);

        $this->info("Notification créée: #{$notification->id}");

        // Envoyer
        $this->info('Envoi en cours...');
        
        $service = app(PushNotificationService::class);
        $service->sendNotification($notification);

        // Vérifier le résultat
        $notification->refresh();
        
        $this->newLine();
        $this->info('Résultat:');
        $this->line("  Status: {$notification->status}");
        $this->line("  Envoyés: {$notification->sent_count}");
        $this->line("  Livrés: {$notification->delivered_count}");

        if ($notification->sent_count > 0) {
            $this->info('✓ Notification envoyée avec succès !');
            return 0;
        } else {
            $this->error('✗ Échec de l\'envoi de la notification');
            $this->line('Vérifiez les logs: tail -f storage/logs/laravel.log');
            return 1;
        }
    }
}
