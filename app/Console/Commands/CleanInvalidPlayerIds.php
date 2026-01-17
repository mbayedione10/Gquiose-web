<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;

class CleanInvalidPlayerIds extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'onesignal:clean-invalid-players
                            {--dry-run : Afficher les changements sans les appliquer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Nettoie les player_id OneSignal invalides/expirés pour forcer le réenregistrement';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🔍 Recherche des utilisateurs avec player_id...');
        
        $usersWithPlayerId = Utilisateur::whereNotNull('onesignal_player_id')->get();
        
        $this->info("Trouvé {$usersWithPlayerId->count()} utilisateur(s) avec player_id");
        
        if ($usersWithPlayerId->isEmpty()) {
            $this->info('✅ Aucun player_id à nettoyer');
            return 0;
        }

        $this->newLine();
        
        if ($isDryRun) {
            $this->warn('🔸 MODE DRY-RUN - Aucune modification ne sera effectuée');
            $this->newLine();
        }

        $cleaned = 0;
        $failed = 0;

        $progressBar = $this->output->createProgressBar($usersWithPlayerId->count());
        $progressBar->start();

        foreach ($usersWithPlayerId as $user) {
            if ($isDryRun) {
                $identifier = $user->email ?: $user->phone;
                $this->line("  → User #{$user->id} ({$identifier}) - Player: {$user->onesignal_player_id}");
                $cleaned++;
            } else {
                try {
                    $user->onesignal_player_id = null;
                    $user->save();
                    $cleaned++;
                } catch (\Exception $e) {
                    $this->error("Erreur pour user #{$user->id}: {$e->getMessage()}");
                    $failed++;
                }
            }
            $progressBar->advance();
        }

        $progressBar->finish();
        $this->newLine(2);

        if ($isDryRun) {
            $this->info("📊 RÉSUMÉ (DRY-RUN):");
            $this->info("   • {$cleaned} player_id(s) seraient nettoyé(s)");
            $this->newLine();
            $this->comment('💡 Lancez sans --dry-run pour appliquer les changements');
        } else {
            $this->info('📊 RÉSUMÉ:');
            $this->info("   ✅ {$cleaned} player_id(s) nettoyé(s) avec succès");
            if ($failed > 0) {
                $this->error("   ❌ {$failed} échec(s)");
            }
            $this->newLine();
            $this->info('✨ Les utilisateurs devront réenregistrer leur player_id à la prochaine connexion');
        }

        return 0;
    }
}
