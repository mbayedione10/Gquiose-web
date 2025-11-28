<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            // Stockage sécurisé des preuves (photos, vidéos, documents, screenshots)
            $table->json('preuves')->nullable()->comment('Chemins des fichiers de preuves stockés de manière sécurisée');

            // Conseils automatiques de sécurité pour l'utilisateur
            $table->text('conseils_securite')->nullable()->comment('Conseils de sécurité contextuels basés sur le type de violence');

            // Indicateur pour savoir si les conseils ont été lus
            $table->boolean('conseils_lus')->default(false)->comment('Indique si l\'utilisateur a lu les conseils de sécurité');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropColumn(['preuves', 'conseils_securite', 'conseils_lus']);
        });
    }
};
