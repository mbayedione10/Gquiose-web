<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;

class VerifyActiveUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:verify-active
                            {--dry-run : Afficher les changements sans les appliquer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Marque comme vérifiés tous les utilisateurs actifs qui ont un email/phone';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');

        $this->info('🔍 Recherche des utilisateurs actifs non vérifiés...');
        
        $unverifiedUsers = Utilisateur::where('status', true)
            ->where(function($query) {
                $query->whereNull('email_verified_at')
                      ->orWhereNull('phone_verified_at');
            })
            ->get();
        
        $this->info("Trouvé {$unverifiedUsers->count()} utilisateur(s) à vérifier");
        
        if ($unverifiedUsers->isEmpty()) {
            $this->info('✅ Aucun utilisateur à traiter');
            return 0;
        }

        $this->newLine();
        
        if ($isDryRun) {
            $this->warn('🔸 MODE DRY-RUN - Aucune modification ne sera effectuée');
            $this->newLine();
        }

        $verified = 0;
        $errors = 0;

        foreach ($unverifiedUsers as $user) {
            $changes = [];
            
            if ($user->email && !$user->email_verified_at) {
                $changes[] = 'email';
            }
            
            if ($user->phone && !$user->phone_verified_at) {
                $changes[] = 'phone';
            }

            if (empty($changes)) {
                continue;
            }

            $identifier = $user->email ?: $user->phone;
            $changesStr = implode(', ', $changes);

            if ($isDryRun) {
                $this->line("  ✓ User #{$user->id} ({$identifier}) - Vérifierait: {$changesStr}");
                $verified++;
            } else {
                try {
                    if (in_array('email', $changes)) {
                        $user->email_verified_at = now();
                    }
                    if (in_array('phone', $changes)) {
                        $user->phone_verified_at = now();
                    }
                    $user->save();
                    
                    $this->line("  ✓ User #{$user->id} ({$identifier}) - Vérifié: {$changesStr}");
                    $verified++;
                } catch (\Exception $e) {
                    $this->error("  ✗ Erreur pour user #{$user->id}: {$e->getMessage()}");
                    $errors++;
                }
            }
        }

        $this->newLine();

        if ($isDryRun) {
            $this->info("📊 RÉSUMÉ (DRY-RUN):");
            $this->info("   • {$verified} utilisateur(s) seraient vérifié(s)");
            $this->newLine();
            $this->comment('💡 Lancez sans --dry-run pour appliquer les changements');
        } else {
            $this->info('📊 RÉSUMÉ:');
            $this->info("   ✅ {$verified} utilisateur(s) vérifié(s) avec succès");
            if ($errors > 0) {
                $this->error("   ❌ {$errors} erreur(s)");
            }
            $this->newLine();
            $this->info('✨ Les utilisateurs peuvent maintenant recevoir toutes les fonctionnalités');
        }

        return 0;
    }
}
