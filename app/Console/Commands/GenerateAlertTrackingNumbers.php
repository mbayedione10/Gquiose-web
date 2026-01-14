<?php

namespace App\Console\Commands;

use App\Models\Alerte;
use Illuminate\Console\Command;

class GenerateAlertTrackingNumbers extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'alertes:generate-tracking-numbers';

    /**
     * The console command description.
     */
    protected $description = 'Génère des numéros de suivi pour les alertes existantes qui n\'en ont pas';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Recherche des alertes sans numéro de suivi...');

        $alertesSansNumero = Alerte::whereNull('numero_suivi')->get();
        $count = $alertesSansNumero->count();

        if ($count === 0) {
            $this->info('✅ Toutes les alertes ont déjà un numéro de suivi.');

            return 0;
        }

        $this->info("📋 {$count} alerte(s) sans numéro de suivi trouvée(s).");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($alertesSansNumero as $alerte) {
            // Générer le numéro de suivi
            $year = $alerte->created_at->format('Y');
            $prefix = "VBG-{$year}-";

            // Récupérer le dernier numéro de suivi de l'année de création de l'alerte
            $lastAlerte = Alerte::where('numero_suivi', 'like', "{$prefix}%")
                ->orderBy('numero_suivi', 'desc')
                ->first();

            if ($lastAlerte) {
                // Extraire le numéro incrémental du dernier signalement
                $lastNumber = (int) substr($lastAlerte->numero_suivi, -5);
                $nextNumber = $lastNumber + 1;
            } else {
                // Premier signalement de l'année
                $nextNumber = 1;
            }

            // Format sur 5 chiffres : 00001, 00002, etc.
            $numeroSuivi = $prefix.str_pad($nextNumber, 5, '0', STR_PAD_LEFT);

            // Mettre à jour l'alerte (sans déclencher les observers)
            $alerte->numero_suivi = $numeroSuivi;
            $alerte->saveQuietly();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);
        $this->info("✅ {$count} numéro(s) de suivi généré(s) avec succès!");

        return 0;
    }
}
