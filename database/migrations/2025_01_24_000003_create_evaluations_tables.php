
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
            $table->json('options')->nullable(); // Pour les choix multiples
            $table->integer('ordre')->default(0);
            $table->boolean('obligatoire')->default(false);
            $table->boolean('status')->default(true);
            $table->timestamps();
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
