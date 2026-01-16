<?php

namespace App\Console\Commands;

use App\Models\PushNotification;
use App\Models\Utilisateur;
use Illuminate\Console\Command;

class DiagnoseNotifications extends Command
{
    protected $signature = 'notifications:diagnose-system';

    protected $description = 'Diagnostiquer le système de notifications push complet';

    public function handle()
    {
        $this->info('====================================');
        $this->info('Diagnostic Notifications Push');
        $this->info('====================================');
        $this->newLine();

        // 1. Utilisateurs avec OneSignal player_id
        $this->info('1. Utilisateurs avec OneSignal player_id:');
        $totalWithPlayerId = Utilisateur::whereNotNull('onesignal_player_id')->count();
        $activeWithPlayerId = Utilisateur::whereNotNull('onesignal_player_id')
            ->where('status', true)
            ->count();
        
        $this->line("   Total avec player_id: {$totalWithPlayerId}");
        $this->line("   Actifs avec player_id: {$activeWithPlayerId}");

        if ($activeWithPlayerId > 0) {
            $this->info('   ✓ Des utilisateurs peuvent recevoir des notifications');
            
            $sample = Utilisateur::whereNotNull('onesignal_player_id')
                ->where('status', true)
                ->take(3)
                ->get(['id', 'nom', 'prenom', 'onesignal_player_id']);
            
            foreach ($sample as $user) {
                $playerId = substr($user->onesignal_player_id, 0, 20) . '...';
                $this->line("   - User #{$user->id}: {$user->nom} {$user->prenom} - {$playerId}");
            }
        } else {
            $this->error('   ✗ Aucun utilisateur actif avec player_id !');
        }

        $this->newLine();

        // 2. Préférences de notification
        $this->info('2. Préférences de notification:');
        $usersWithPrefs = Utilisateur::whereNotNull('onesignal_player_id')
            ->where('status', true)
            ->with('notificationPreferences')
            ->take(5)
            ->get();

        if ($usersWithPrefs->count() > 0) {
            foreach ($usersWithPrefs as $user) {
                $prefs = $user->notificationPreferences;
                if ($prefs) {
                    $enabled = $prefs->notifications_enabled ? 'OUI' : 'NON';
                    $dnd = $prefs->do_not_disturb ? 'OUI' : 'NON';
                    $this->line("   User #{$user->id}: Notifs={$enabled}, DND={$dnd}");
                } else {
                    $this->line("   User #{$user->id}: Aucune préférence (par défaut = activé)");
                }
            }
        } else {
            $this->warn('   Aucun utilisateur à analyser');
        }

        $this->newLine();

        // 3. Dernières notifications
        $this->info('3. Dernières notifications envoyées:');
        $notifications = PushNotification::orderBy('created_at', 'desc')
            ->take(5)
            ->get(['id', 'title', 'status', 'sent_count', 'target_audience', 'created_at']);

        if ($notifications->count() > 0) {
            foreach ($notifications as $notif) {
                $this->line(sprintf(
                    "   #%d: %s - Status: %s - Envoyés: %d - Audience: %s - Créé: %s",
                    $notif->id,
                    $notif->title,
                    $notif->status,
                    $notif->sent_count,
                    $notif->target_audience,
                    $notif->created_at->format('Y-m-d H:i')
                ));
            }
        } else {
            $this->warn('   Aucune notification trouvée');
        }

        $this->newLine();

        // 4. Configuration OneSignal
        $this->info('4. Configuration OneSignal:');
        $appId = config('onesignal.app_id');
        $apiKey = config('onesignal.rest_api_key');

        $appIdStatus = $appId ? '✓ Configuré (' . substr($appId, 0, 10) . '...)' : '✗ NON CONFIGURÉ';
        $apiKeyStatus = $apiKey ? '✓ Configuré (' . substr($apiKey, 0, 10) . '...)' : '✗ NON CONFIGURÉ';

        $this->line("   App ID: {$appIdStatus}");
        $this->line("   API Key: {$apiKeyStatus}");

        if (!$appId || !$apiKey) {
            $this->error('   ✗ Configuration OneSignal incomplète !');
        }

        $this->newLine();

        // 5. Recommandations
        $this->info('5. Recommandations:');
        
        if ($activeWithPlayerId === 0) {
            $this->error('   ⚠ CRITIQUE: Aucun utilisateur actif avec player_id');
            $this->line('   → Les utilisateurs mobiles doivent appeler /api/v1/notifications/register-token');
        }

        if (!$appId || !$apiKey) {
            $this->error('   ⚠ CRITIQUE: Configuration OneSignal manquante');
            $this->line('   → Vérifier le fichier .env: ONESIGNAL_APP_ID et ONESIGNAL_REST_API_KEY');
        }

        $usersWithoutPrefs = Utilisateur::whereNotNull('onesignal_player_id')
            ->where('status', true)
            ->whereDoesntHave('notificationPreferences')
            ->count();

        if ($usersWithoutPrefs > 0) {
            $this->warn("   ⚠ {$usersWithoutPrefs} utilisateur(s) sans préférences");
            $this->line('   → Exécuter: php artisan notifications:create-default-preferences');
        }

        $this->newLine();
        $this->info('====================================');
        $this->info('Fin du diagnostic');
        $this->info('====================================');

        return 0;
    }
}
