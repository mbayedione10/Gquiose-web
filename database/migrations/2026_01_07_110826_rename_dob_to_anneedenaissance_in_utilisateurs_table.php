<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Ajoute les champs dob (tranche d'âge) et anneedenaissance si manquants
     */
    public function up(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            // Vérifier si dob existe déjà (pour MySQL/PostgreSQL)
            if (!Schema::hasColumn('utilisateurs', 'dob')) {
                $table->string('dob')->nullable()->after('status');
            }
            
            // Ajouter anneedenaissance si manquant
            if (!Schema::hasColumn('utilisateurs', 'anneedenaissance')) {
                $table->integer('anneedenaissance')->nullable()->after('status');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->dropColumn('anneedenaissance');
        });
    }
};
