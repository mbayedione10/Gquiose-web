
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Table principale du cycle
        Schema::create('menstrual_cycles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->date('period_start_date'); // Date de début des règles
            $table->date('period_end_date')->nullable(); // Date de fin des règles
            $table->integer('cycle_length')->nullable(); // Durée du cycle (en jours)
            $table->integer('period_length')->nullable(); // Durée des règles (en jours)
            $table->enum('flow_intensity', ['leger', 'modere', 'abondant'])->nullable();
            $table->date('next_period_prediction')->nullable(); // Prédiction prochaines règles
            $table->date('ovulation_prediction')->nullable(); // Prédiction ovulation
            $table->date('fertile_window_start')->nullable(); // Début fenêtre fertilité
            $table->date('fertile_window_end')->nullable(); // Fin fenêtre fertilité
            $table->boolean('is_active')->default(true); // Cycle actif ou non
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['utilisateur_id', 'period_start_date']);
            $table->index(['utilisateur_id', 'is_active']);
        });

        // Table des symptômes quotidiens
        Schema::create('cycle_symptoms', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('menstrual_cycle_id')->nullable()->constrained('menstrual_cycles')->onDelete('set null');
            $table->date('symptom_date');
            
            // Symptômes physiques
            $table->json('physical_symptoms')->nullable(); // ['crampes', 'fatigue', 'maux_tete', 'nausee', 'sensibilite_seins', 'ballonnements', 'douleurs_dos']
            $table->integer('pain_level')->nullable(); // 0-10
            
            // Symptômes émotionnels/humeur
            $table->json('mood')->nullable(); // ['joyeuse', 'triste', 'irritable', 'anxieuse', 'calme', 'energique']
            
            // Autres observations
            $table->enum('discharge_type', ['aucune', 'creamy', 'sticky', 'watery', 'egg_white'])->nullable();
            $table->decimal('temperature', 4, 2)->nullable(); // Température basale
            $table->boolean('sexual_activity')->default(false);
            $table->boolean('contraception_used')->default(false);
            
            $table->text('notes')->nullable();
            $table->timestamps();

            $table->index(['utilisateur_id', 'symptom_date']);
            $table->unique(['utilisateur_id', 'symptom_date']);
        });

        // Table des rappels/notifications cycle
        Schema::create('cycle_reminders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->enum('reminder_type', [
                'period_approaching', // Règles qui approchent (3 jours avant)
                'period_today', // Règles prévues aujourd'hui
                'ovulation_approaching', // Ovulation qui approche
                'fertile_window', // Fenêtre de fertilité
                'log_symptoms', // Rappel de noter symptômes
                'pill_reminder' // Rappel pilule contraceptive
            ]);
            $table->time('reminder_time')->default('09:00:00'); // Heure du rappel
            $table->boolean('enabled')->default(true);
            $table->json('days_before')->nullable(); // [3, 1] pour rappel X jours avant
            $table->timestamps();

            $table->index(['utilisateur_id', 'enabled']);
        });

        // Table des paramètres du cycle
        Schema::create('cycle_settings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->integer('average_cycle_length')->default(28); // Durée moyenne du cycle
            $table->integer('average_period_length')->default(5); // Durée moyenne des règles
            $table->boolean('track_temperature')->default(false);
            $table->boolean('track_symptoms')->default(true);
            $table->boolean('track_mood')->default(true);
            $table->boolean('track_sexual_activity')->default(false);
            $table->boolean('notifications_enabled')->default(true);
            $table->timestamps();

            $table->unique('utilisateur_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cycle_reminders');
        Schema::dropIfExists('cycle_symptoms');
        Schema::dropIfExists('cycle_settings');
        Schema::dropIfExists('menstrual_cycles');
    }
};
