<?php

namespace App\Console\Commands;

use App\Models\UserNotificationPreference;
use App\Models\Utilisateur;
use Illuminate\Console\Command;

class CreateDefaultNotificationPreferences extends Command
{
    protected $signature = 'notifications:create-default-preferences';

    protected $description = 'Créer les préférences de notification par défaut pour tous les utilisateurs';

    public function handle()
    {
        $this->info('Création des préférences de notification par défaut...');

        $users = Utilisateur::whereDoesntHave('notificationPreferences')->get();
        $count = 0;

        foreach ($users as $user) {
            try {
                UserNotificationPreference::create([
                    'utilisateur_id' => $user->id,
                    'notifications_enabled' => true,
                    'cycle_notifications' => true,
                    'content_notifications' => true,
                    'forum_notifications' => true,
                    'health_tips_notifications' => true,
                    'admin_notifications' => true,
                    'do_not_disturb' => false,
                ]);
                $count++;
            } catch (\Exception $e) {
                $this->error("Erreur pour l'utilisateur #{$user->id}: " . $e->getMessage());
            }
        }

        $this->info("Préférences créées pour {$count} utilisateur(s).");

        return 0;
    }
}
