<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use App\Models\PushNotification;
use App\Services\Push\OneSignalService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class SendTestNotification extends Command
{
    protected $signature = 'notifications:test {user_id} {--title=} {--message=} {--bypass-preferences}';

    protected $description = 'Envoyer une notification de test à un utilisateur spécifique';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $bypassPreferences = $this->option('bypass-preferences');

        $user = Utilisateur::find($userId);
        if (!$user) {
            $this->error("❌ Utilisateur #{$userId} introuvable");
            return Command::FAILURE;
        }

        if (empty($user->onesignal_player_id)) {
            $this->error("❌ L'utilisateur n'a pas de Player ID OneSignal enregistré");
            return Command::FAILURE;
        }

        $title = $this->option('title') ?? 'Test de notification';
        $message = $this->option('message') ?? 'Ceci est une notification de test envoyée depuis la console.';

        // Créer une notification temporaire
        $notification = new PushNotification([
            'title' => $title,
            'message' => $message,
            'icon' => '🔔',
            'action' => 'test',
            'type' => 'manual',
            'target_audience' => 'specific',
        ]);
        $notification->save();

        $this->info("📱 Envoi d'une notification de test à {$user->name}");
        $this->info("   Player ID: {$user->onesignal_player_id}");
        $this->info("   Titre: {$title}");
        $this->info("   Message: {$message}");
        
        if ($bypassPreferences) {
            $this->warn("⚠️  Mode BYPASS: Les préférences utilisateur seront ignorées");
        }
        $this->newLine();

        try {
            $oneSignalService = new OneSignalService();
            
            if ($bypassPreferences) {
                // Envoi direct sans vérification des préférences
                $reflection = new \ReflectionClass($oneSignalService);
                $method = $reflection->getMethod('sendToPlayerIds');
                $method->setAccessible(true);
                
                $result = $method->invoke($oneSignalService, [$user->onesignal_player_id], $notification, [$user]);
                
                if ($result) {
                    $this->info("✅ Notification envoyée avec succès (mode bypass) !");
                    Log::info("Test notification sent to user {$userId} (bypass mode)", [
                        'title' => $title,
                        'message' => $message,
                    ]);
                } else {
                    $this->error("❌ Échec de l'envoi");
                    $this->warn("Vérifiez les logs pour plus de détails");
                }
            } else {
                // Envoi normal avec vérification des préférences
                $result = $oneSignalService->sendToUser($user, $notification);
                
                if ($result) {
                    $this->info("✅ Notification envoyée avec succès !");
                    Log::info("Test notification sent to user {$userId}", [
                        'title' => $title,
                        'message' => $message,
                    ]);
                } else {
                    $this->error("❌ Échec de l'envoi");
                    $this->warn("L'utilisateur a peut-être bloqué ce type de notification dans ses préférences");
                    $this->warn("Utilisez --bypass-preferences pour ignorer les préférences");
                }
            }

            // Ne pas supprimer la notification de test pour permettre le tracking
            $this->info("💡 Notification ID: {$notification->id} (conservée pour testing)");

        } catch (\Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
            Log::error("Test notification failed for user {$userId}", [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            return Command::FAILURE;
        }

        return Command::SUCCESS;
    }
}
