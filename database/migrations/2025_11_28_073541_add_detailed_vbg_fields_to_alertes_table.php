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
            // === Champs spécifiques aux violences numériques ===

            // Plateformes concernées (Facebook, WhatsApp, Instagram, TikTok, etc.)
            $table->json('plateformes')->nullable()->comment('Liste des plateformes où la violence a eu lieu');

            // Nature du contenu problématique (messages, images, vidéos, etc.)
            $table->json('nature_contenu')->nullable()->comment('Types de contenu problématique (messages, images, vidéos)');

            // URLs des contenus problématiques
            $table->text('urls_problematiques')->nullable()->comment('URLs des posts, profils ou contenus problématiques');

            // Comptes/pseudonymes des agresseurs
            $table->text('comptes_impliques')->nullable()->comment('Pseudonymes, noms de profils des agresseurs');

            // Fréquence des incidents
            $table->enum('frequence_incidents', [
                'unique',
                'quotidien',
                'hebdomadaire',
                'mensuel',
                'continu'
            ])->nullable()->comment('Fréquence des incidents de violence');

            // === Informations générales sur l\'incident ===

            // Date approximative de l'incident
            $table->date('date_incident')->nullable()->comment('Date approximative de l\'incident');

            // Heure approximative de l'incident
            $table->time('heure_incident')->nullable()->comment('Heure approximative de l\'incident');

            // Relation avec l'agresseur
            $table->string('relation_agresseur')->nullable()->comment('Relation avec l\'agresseur (conjoint, ex, inconnu, etc.)');

            // Impact sur la victime (stress, peur, dépression, etc.)
            $table->json('impact')->nullable()->comment('Impact psychologique et physique sur la victime');

            // === Consentement et anonymat ===

            // Anonymat souhaité
            $table->boolean('anonymat_souhaite')->default(false)->comment('La victime souhaite rester anonyme');

            // Consentement pour transmission au système national VBG
            $table->boolean('consentement_transmission')->default(true)->comment('Autorisation de transmettre au système national VBG');

            // Numéro de suivi unique
            $table->string('numero_suivi')->unique()->nullable()->comment('Numéro unique de suivi du signalement (ex: VBG-2025-00123)');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropColumn([
                'plateformes',
                'nature_contenu',
                'urls_problematiques',
                'comptes_impliques',
                'frequence_incidents',
                'date_incident',
                'heure_incident',
                'relation_agresseur',
                'impact',
                'anonymat_souhaite',
                'consentement_transmission',
                'numero_suivi',
            ]);
        });
    }
};
