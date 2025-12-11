<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Nettoie les valeurs texte invalides dans le champ dob
     */
    public function up(): void
    {
        // Supprimer les valeurs qui ne sont pas des dates valides
        $users = DB::table('utilisateurs')
            ->whereNotNull('dob')
            ->get(['id', 'dob']);

        foreach ($users as $user) {
            // Vérifier si c'est une date valide (format YYYY-MM-DD)
            if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $user->dob)) {
                DB::table('utilisateurs')
                    ->where('id', $user->id)
                    ->update(['dob' => null]);
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Impossible de restaurer les données supprimées
    }
};
