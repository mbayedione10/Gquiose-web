<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Convertit les emails vides ('') en NULL pour éviter la violation
     * de contrainte unique lors d'une inscription par téléphone.
     */
    public function up(): void
    {
        // Convertir les chaînes vides en NULL
        DB::statement("UPDATE utilisateurs SET email = NULL WHERE email = ''");

        // S'assurer que la colonne est bien nullable
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->string('email')->nullable()->change();
        });
    }

    public function down(): void
    {
        // Rien à annuler : on ne peut pas récupérer les emails vides supprimés
    }
};
