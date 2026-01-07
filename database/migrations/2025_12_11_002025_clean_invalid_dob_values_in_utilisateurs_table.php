<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * MIGRATION OBSOLÈTE - Désactivée car incompatible avec le nouveau système de tranches d'âge
     * Le champ dob stocke maintenant des tranches d'âge (ex: "18-24 ans") et non des dates
     */
    public function up(): void
    {
        // Migration désactivée - le nettoyage est géré par la migration
        // 2026_01_07_150000_update_dob_to_standard_age_ranges_in_utilisateurs_table.php
        
        // Ne rien faire pour préserver les données existantes
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Impossible de restaurer les données supprimées
    }
};
