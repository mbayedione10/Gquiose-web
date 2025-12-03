
<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    public function up(): void
    {
        // Table des questions d'évaluation
        Schema::create('question_evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('question');
            $table->enum('type', ['text', 'rating', 'yesno', 'multiple_choice', 'scale']);
            $table->enum('formulaire_type', ['satisfaction_quiz', 'satisfaction_article', 'satisfaction_structure', 'satisfaction_alerte', 'generale'])->default('generale');
            $table->json('options')->nullable(); // Pour les choix multiples
            $table->integer('ordre')->default(0);
            $table->boolean('obligatoire')->default(false);
            $table->boolean('status')->default(true);
            
            // Logique conditionnelle
            $table->unsignedBigInteger('condition_question_id')->nullable();
            $table->string('condition_operator')->nullable(); // equals, not_equals, greater_than, etc.
            $table->text('condition_value')->nullable();
            $table->boolean('show_if_condition_met')->default(true);
            
            $table->timestamps();
            
            $table->foreign('condition_question_id')
                ->references('id')
                ->on('question_evaluations')
                ->onDelete('set null');
        });

        // Table des évaluations soumises
        Schema::create('evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('utilisateur_id');
            $table->string('contexte')->nullable(); // ex: "quiz", "article", "structure", "generale"
            $table->unsignedBigInteger('contexte_id')->nullable(); // ID de l'élément évalué
            $table->json('reponses'); // Stockage des réponses
            $table->decimal('score_global', 3, 2)->nullable(); // Score moyen si applicable
            $table->text('commentaire')->nullable();
            $table->timestamps();

            $table->foreign('utilisateur_id')
                ->references('id')
                ->on('utilisateurs')
                ->onDelete('cascade');
        });

        // Table des réponses individuelles
        Schema::create('reponse_evaluations', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('evaluation_id');
            $table->unsignedBigInteger('question_evaluation_id');
            $table->text('reponse');
            $table->integer('valeur_numerique')->nullable(); // Pour les ratings/scales
            $table->timestamps();

            $table->foreign('evaluation_id')
                ->references('id')
                ->on('evaluations')
                ->onDelete('cascade');
            
            $table->foreign('question_evaluation_id')
                ->references('id')
                ->on('question_evaluations')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('reponse_evaluations');
        Schema::dropIfExists('evaluations');
        Schema::dropIfExists('question_evaluations');
    }
};
