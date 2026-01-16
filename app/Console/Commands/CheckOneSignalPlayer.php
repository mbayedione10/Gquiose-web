<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use GuzzleHttp\Client;
use Illuminate\Console\Command;

class CheckOneSignalPlayer extends Command
{
    protected $signature = 'onesignal:check-player {user_id}';

    protected $description = 'Vérifier le statut d\'un Player ID OneSignal sur l\'API OneSignal';

    public function handle()
    {
        $userId = $this->argument('user_id');

        $this->info("🔍 Vérification du Player ID OneSignal pour l'utilisateur #{$userId}");
        $this->newLine();

        // Récupérer l'utilisateur
        $user = Utilisateur::find($userId);
        if (!$user) {
            $this->error("❌ Utilisateur #{$userId} introuvable");
            return Command::FAILURE;
        }

        $this->info("👤 Utilisateur: {$user->name}");
        $this->info("📧 Email: {$user->email}");
        $this->info("📱 Player ID: " . ($user->onesignal_player_id ?? 'NON DÉFINI'));
        $this->info("📲 Platform: " . ($user->platform ?? 'NON DÉFINI'));
        $this->newLine();

        if (empty($user->onesignal_player_id)) {
            $this->error("❌ Aucun Player ID enregistré pour cet utilisateur");
            $this->warn("→ L'application mobile n'a jamais appelé /api/v1/push/register-token");
            return Command::FAILURE;
        }

        // Vérifier sur OneSignal
        $appId = config('onesignal.app_id');
        $apiKey = config('onesignal.rest_api_key');
        $playerId = $user->onesignal_player_id;

        $this->info("🌐 Interrogation de l'API OneSignal...");
        $this->newLine();

        $client = new Client();
        try {
            $response = $client->request('GET', "https://onesignal.com/api/v1/players/{$playerId}?app_id={$appId}", [
                'headers' => [
                    'Authorization' => 'Basic ' . $apiKey,
                ],
            ]);

            $data = json_decode($response->getBody(), true);

            $this->line("📊 <fg=cyan>Informations OneSignal:</>");
            $this->line("   ID: " . ($data['id'] ?? 'N/A'));
            $this->line("   Device Type: " . $this->getDeviceTypeName($data['device_type'] ?? null));
            $this->line("   Device Model: " . ($data['device_model'] ?? 'N/A'));
            $this->line("   SDK Version: " . ($data['sdk'] ?? 'N/A'));
            $this->line("   App Version: " . ($data['game_version'] ?? 'N/A'));
            $this->line("   Session Count: " . ($data['session_count'] ?? 0));
            $this->line("   Created At: " . ($data['created_at'] ?? 'N/A'));
            
            if (isset($data['last_active'])) {
                $lastActiveTime = $data['last_active'];
                $daysSinceActive = floor((time() - $lastActiveTime) / 86400);
                $this->line("   Last Active: " . date('Y-m-d H:i:s', $lastActiveTime) . " (il y a {$daysSinceActive} jour(s))");
            } else {
                $this->line("   Last Active: Jamais");
            }
            
            $this->newLine();

            // Vérifier si le player est invalide
            $isInvalid = isset($data['invalid_identifier']) && $data['invalid_identifier'];
            
            if ($isInvalid) {
                $this->error("❌ Ce Player ID est INVALIDE sur OneSignal");
                $this->warn("   → L'utilisateur a probablement:");
                $this->warn("      • Désinstallé l'application");
                $this->warn("      • Désactivé les notifications dans les paramètres Android");
                $this->warn("      • Réinitialisé son téléphone");
                $this->newLine();
                $this->info("💡 Solutions:");
                $this->info("   1. Demander à l'utilisateur de réinstaller l'app");
                $this->info("   2. Ou activer les notifications: Paramètres → Apps → Gquiose → Notifications");
                $this->info("   3. L'app va automatiquement enregistrer un nouveau Player ID");
            } else {
                $this->info("✅ Player ID VALIDE sur OneSignal");
                
                // Vérifier la dernière activité
                if (isset($data['last_active'])) {
                    $daysSinceActive = floor((time() - $data['last_active']) / 86400);
                    
                    if ($daysSinceActive > 30) {
                        $this->warn("⚠️  L'utilisateur n'a pas ouvert l'app depuis {$daysSinceActive} jours");
                        $this->warn("   → Le Player ID pourrait être obsolète");
                    } elseif ($daysSinceActive > 7) {
                        $this->warn("⚠️  Dernière activité: il y a {$daysSinceActive} jours");
                    } else {
                        $this->info("✅ Utilisateur actif (dernière activité: il y a {$daysSinceActive} jours)");
                    }
                }

                // Vérifier si les notifications sont activées
                if (isset($data['notification_types'])) {
                    $notifTypes = $data['notification_types'];
                    if ($notifTypes == -2 || $notifTypes == 0) {
                        $this->error("❌ Notifications DÉSACTIVÉES sur cet appareil");
                        $this->warn("   → L'utilisateur doit activer les notifications dans les paramètres Android");
                    } else {
                        $this->info("✅ Notifications activées sur l'appareil");
                    }
                }
            }

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            if ($e->getResponse()->getStatusCode() == 404) {
                $this->error("❌ Ce Player ID n'existe PAS sur OneSignal");
                $this->warn("   → Le Player ID est invalide ou a été supprimé");
                $this->warn("   → L'utilisateur doit ouvrir l'app pour enregistrer un nouveau Player ID");
            } else {
                $this->error("❌ Erreur API OneSignal: " . $e->getMessage());
            }
            return Command::FAILURE;
        } catch (\Exception $e) {
            $this->error("❌ Erreur: " . $e->getMessage());
            return Command::FAILURE;
        }

        $this->newLine();
        $this->info("✅ Vérification terminée");
        
        return Command::SUCCESS;
    }

    protected function getDeviceTypeName($type)
    {
        return match($type) {
            0 => 'iOS',
            1 => 'Android',
            2 => 'Amazon',
            3 => 'WindowsPhone',
            4 => 'ChromeApp',
            5 => 'ChromeWebsite',
            6 => 'WindowsPhone',
            7 => 'Safari',
            8 => 'Firefox',
            9 => 'MacOS',
            10 => 'Alexa',
            11 => 'Email',
            default => "Unknown ({$type})",
        };
    }
}
