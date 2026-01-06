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
        Schema::create('categorie_conseils', function (Blueprint $table) {
            $table->id();
            $table->string('nom')->unique();
            $table->text('description')->nullable();
            $table->string('emoji', 10)->nullable();
            $table->foreignId('type_alerte_id')
                ->nullable()
                ->constrained('type_alertes')
                ->nullOnDelete();
            $table->foreignId('sous_type_violence_numerique_id')
                ->nullable()
                ->constrained('sous_types_violence_numerique')
                ->nullOnDelete();
            $table->boolean('is_default')->default(false);
            $table->integer('ordre')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Index pour optimiser les recherches
            $table->index(['type_alerte_id', 'status']);
            $table->index(['sous_type_violence_numerique_id', 'status']);
            $table->index('is_default');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('categorie_conseils');
    }
};
