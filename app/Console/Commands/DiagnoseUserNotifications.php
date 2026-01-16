<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use App\Models\PushNotification;
use App\Services\Push\OneSignalService;
use Illuminate\Console\Command;

class DiagnoseUserNotifications extends Command
{
    protected $signature = 'notifications:diagnose {user_id} {--notification_id=}';

    protected $description = 'Diagnostiquer pourquoi un utilisateur ne reçoit pas de notifications';

    public function handle()
    {
        $userId = $this->argument('user_id');
        $notificationId = $this->option('notification_id');

        $this->info("🔍 Diagnostic des notifications pour l'utilisateur #{$userId}");
        $this->newLine();

        // 1. Vérifier l'existence de l'utilisateur
        $user = Utilisateur::find($userId);
        if (!$user) {
            $this->error("❌ Utilisateur #{$userId} introuvable");
            return Command::FAILURE;
        }

        $this->info("✅ Utilisateur trouvé: {$user->name} ({$user->email})");
        $this->newLine();

        // 2. Vérifier le OneSignal Player ID
        $this->line("📱 Configuration OneSignal:");
        if (empty($user->onesignal_player_id)) {
            $this->error("  ❌ Player ID: NON CONFIGURÉ");
            $this->warn("  → L'utilisateur n'a jamais enregistré son Player ID OneSignal");
            $this->warn("  → L'application mobile doit appeler /api/v1/push/register-token");
        } else {
            $this->info("  ✅ Player ID: {$user->onesignal_player_id}");
            $this->info("  ✅ Platform: " . ($user->platform ?? 'non définie'));
        }
        $this->newLine();

        // 3. Vérifier le statut du compte
        $this->line("👤 Statut du compte:");
        if ($user->status) {
            $this->info("  ✅ Compte actif");
        } else {
            $this->error("  ❌ Compte inactif/désactivé");
            $this->warn("  → Seuls les utilisateurs actifs reçoivent des notifications");
        }
        $this->newLine();

        // 4. Vérifier les préférences de notification
        $this->line("⚙️  Préférences de notification:");
        $preferences = $user->notificationPreferences;
        
        if (!$preferences) {
            $this->warn("  ⚠️  Aucune préférence définie (tout autorisé par défaut)");
        } else {
            $this->info("  Notifications globales: " . ($preferences->notifications_enabled ? '✅ Activées' : '❌ Désactivées'));
            $this->info("  Mode Ne Pas Déranger: " . ($preferences->do_not_disturb ? '❌ ACTIVÉ (bloque tout)' : '✅ Désactivé'));
            
            if ($preferences->quiet_start && $preferences->quiet_end) {
                $this->info("  Heures silencieuses: {$preferences->quiet_start} - {$preferences->quiet_end}");
                
                $now = now();
                $quietStart = \Carbon\Carbon::createFromFormat('H:i', $preferences->quiet_start);
                $quietEnd = \Carbon\Carbon::createFromFormat('H:i', $preferences->quiet_end);
                
                $inQuietHours = false;
                if ($quietEnd->lessThan($quietStart)) {
                    $inQuietHours = $now->format('H:i') >= $quietStart->format('H:i') || $now->format('H:i') < $quietEnd->format('H:i');
                } else {
                    $inQuietHours = $now->format('H:i') >= $quietStart->format('H:i') && $now->format('H:i') < $quietEnd->format('H:i');
                }
                
                if ($inQuietHours) {
                    $this->warn("  ⚠️  Actuellement en période silencieuse !");
                }
            }
            
            $this->newLine();
            $this->line("  Préférences par catégorie:");
            $this->info("    - Cycle menstruel: " . ($preferences->cycle_notifications ? '✅' : '❌'));
            $this->info("    - Contenus: " . ($preferences->content_notifications ? '✅' : '❌'));
            $this->info("    - Forum: " . ($preferences->forum_notifications ? '✅' : '❌'));
            $this->info("    - Conseils santé: " . ($preferences->health_tips_notifications ? '✅' : '❌'));
            $this->info("    - Admin: " . ($preferences->admin_notifications ? '✅' : '❌'));
        }
        $this->newLine();

        // 5. Tester l'envoi si notification_id fourni
        if ($notificationId) {
            $notification = PushNotification::find($notificationId);
            if (!$notification) {
                $this->error("❌ Notification #{$notificationId} introuvable");
                return Command::FAILURE;
            }

            $this->info("🔔 Test d'envoi de la notification: {$notification->title}");
            $this->newLine();

            // Vérifier si l'utilisateur peut recevoir cette notification
            $oneSignalService = new OneSignalService();
            
            // Utiliser reflection pour accéder à la méthode protected
            $reflection = new \ReflectionClass($oneSignalService);
            $method = $reflection->getMethod('shouldSendNotification');
            $method->setAccessible(true);
            
            $canSend = $method->invoke($oneSignalService, $user, $notification);
            
            if ($canSend && !empty($user->onesignal_player_id) && $user->status) {
                $this->info("✅ L'utilisateur PEUT recevoir cette notification");
                
                if ($this->confirm('Voulez-vous envoyer un test ?', true)) {
                    try {
                        $result = $oneSignalService->sendToUser($user, $notification);
                        if ($result) {
                            $this->info("✅ Notification de test envoyée avec succès !");
                        } else {
                            $this->error("❌ Échec de l'envoi (voir les logs)");
                        }
                    } catch (\Exception $e) {
                        $this->error("❌ Erreur: " . $e->getMessage());
                    }
                }
            } else {
                $this->error("❌ L'utilisateur NE PEUT PAS recevoir cette notification");
                $this->warn("  Raisons possibles:");
                if (empty($user->onesignal_player_id)) {
                    $this->warn("  - Player ID manquant");
                }
                if (!$user->status) {
                    $this->warn("  - Compte inactif");
                }
                if (!$canSend) {
                    $this->warn("  - Bloqué par les préférences de notification");
                }
            }
        }

        // 6. Historique des notifications reçues
        $this->newLine();
        $this->line("📊 Historique récent (7 derniers jours):");
        $logs = \App\Models\NotificationLog::where('utilisateur_id', $userId)
            ->where('sent_at', '>=', now()->subDays(7))
            ->orderBy('sent_at', 'desc')
            ->limit(10)
            ->get();

        if ($logs->isEmpty()) {
            $this->warn("  Aucune notification envoyée dans les 7 derniers jours");
        } else {
            foreach ($logs as $log) {
                $this->info("  [{$log->sent_at->format('d/m H:i')}] {$log->title} - {$log->status}");
            }
        }

        $this->newLine();
        $this->info("✅ Diagnostic terminé");
        
        return Command::SUCCESS;
    }
}
