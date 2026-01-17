<?php

namespace App\Console\Commands;

use App\Models\Utilisateur;
use Illuminate\Console\Command;

class InitializeOldUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:initialize-old
                            {--before= : Date limite (YYYY-MM-DD) pour considérer un utilisateur comme "ancien"}
                            {--dry-run : Afficher les changements sans les appliquer}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prépare les anciens utilisateurs (créés avant OneSignal) pour recevoir des notifications';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $beforeDate = $this->option('before') ?: '2024-01-01'; // Date par défaut

        $this->info("🔍 Recherche des utilisateurs créés avant {$beforeDate}...");
        
        $oldUsers = Utilisateur::where('created_at', '<', $beforeDate)
            ->whereNull('onesignal_player_id')
            ->where('status', true)
            ->get();
        
        $this->info("Trouvé {$oldUsers->count()} ancien(s) utilisateur(s) sans player_id");
        
        if ($oldUsers->isEmpty()) {
            $this->info('✅ Aucun utilisateur à traiter');
            return 0;
        }

        $this->newLine();
        
        if ($isDryRun) {
            $this->warn('🔸 MODE DRY-RUN - Aucune modification ne sera effectuée');
            $this->newLine();
        }

        $this->table(
            ['ID', 'Nom', 'Email/Phone', 'Créé le', 'Status', 'Player ID'],
            $oldUsers->take(10)->map(function($u) {
                return [
                    $u->id,
                    $u->prenom . ' ' . $u->nom,
                    $u->email ?: $u->phone,
                    $u->created_at->format('Y-m-d'),
                    $u->status ? '✅' : '❌',
                    $u->onesignal_player_id ?: '❌ Non défini',
                ];
            })
        );

        if ($oldUsers->count() > 10) {
            $this->comment("... et " . ($oldUsers->count() - 10) . " autre(s)");
        }

        $this->newLine();
        $this->info('📋 Ces utilisateurs pourront s\'enregistrer pour recevoir des notifications lors de leur prochaine connexion.');
        $this->info('💡 Rien à faire manuellement - le player_id sera enregistré automatiquement via POST /notifications/register-token');
        
        $this->newLine();
        $this->comment('Note: Les utilisateurs doivent utiliser l\'application mobile pour s\'enregistrer.');
        
        return 0;
    }
}
