<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Normalise les valeurs du champ dob vers les tranches d'âge standardisées
     * Tranches : -15 ans, 15-17 ans, 18-24 ans, 25-29 ans, 30-35 ans, +35 ans
     */
    public function up(): void
    {
        // Traiter par lots pour éviter les problèmes de mémoire sur de grosses tables
        DB::table('utilisateurs')->orderBy('id')->chunk(500, function ($users) {
            foreach ($users as $user) {
                $tranche = null;

                // Si on a une année de naissance, l'utiliser en priorité
                if ($user->anneedenaissance) {
                    $age = now()->year - $user->anneedenaissance;
                    $tranche = $this->getAgeRange($age);
                }
                // Sinon, essayer de parser le champ dob
                elseif ($user->dob) {
                    // Déjà au bon format ?
                    if (in_array($user->dob, ['-15 ans', '15-17 ans', '18-24 ans', '25-29 ans', '30-35 ans', '+35 ans'])) {
                        continue; // Passer au suivant
                    }

                    // Format "+ 35 ans" avec espace -> normaliser
                    if (preg_match('/^\+\s*35\s*ans$/i', trim($user->dob))) {
                        $tranche = '+35 ans';
                    }
                    // Format "30-35 ans" déjà bon
                    elseif (preg_match('/^(15-17|18-24|25-29|30-35)\s*ans$/i', trim($user->dob))) {
                        $tranche = trim($user->dob);
                        // Normaliser (enlever espaces supplémentaires)
                        $tranche = preg_replace('/\s+/', ' ', $tranche);
                    }
                    // Essayer de parser comme date (dd/mm/yyyy ou yyyy-mm-dd)
                    else {
                        $birthYear = $this->extractYearFromDate($user->dob);
                        if ($birthYear) {
                            $age = now()->year - $birthYear;
                            $tranche = $this->getAgeRange($age);
                        }
                    }
                }

                // Mettre à jour seulement si on a une tranche valide et différente
                // Ne jamais effacer les données existantes qu'on ne peut pas parser
                if ($tranche && $user->dob !== $tranche) {
                    DB::table('utilisateurs')
                        ->where('id', $user->id)
                        ->update(['dob' => $tranche]);
                }
            }
        });
    }

    /**
     * Détermine la tranche d'âge selon l'âge calculé
     */
    private function getAgeRange(int $age): string
    {
        return match (true) {
            $age < 15 => '-15 ans',
            $age >= 15 && $age <= 17 => '15-17 ans',
            $age >= 18 && $age <= 24 => '18-24 ans',
            $age >= 25 && $age <= 29 => '25-29 ans',
            $age >= 30 && $age <= 35 => '30-35 ans',
            $age > 35 => '+35 ans',
            default => '+35 ans',
        };
    }

    /**
     * Essaie d'extraire l'année d'une date au format dd/mm/yyyy ou yyyy-mm-dd
     */
    private function extractYearFromDate(?string $date): ?int
    {
        if (! $date) {
            return null;
        }

        // Format dd/mm/yyyy
        if (preg_match('#^(\d{1,2})/(\d{1,2})/(\d{4})$#', $date, $matches)) {
            $year = (int) $matches[3];

            return ($year > 1900 && $year <= now()->year) ? $year : null;
        }

        // Format yyyy-mm-dd
        if (preg_match('#^(\d{4})-(\d{1,2})-(\d{1,2})$#', $date, $matches)) {
            $year = (int) $matches[1];

            return ($year > 1900 && $year <= now()->year) ? $year : null;
        }

        return null;
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // On ne peut pas restaurer les anciennes valeurs car on ne les a pas sauvegardées
        // Cette migration est considérée comme irréversible
    }
};
