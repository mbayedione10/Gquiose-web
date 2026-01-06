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
        Schema::create('section_conseils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('categorie_conseil_id')
                ->constrained('categorie_conseils')
                ->cascadeOnDelete();
            $table->string('titre');
            $table->string('emoji', 10)->nullable();
            $table->integer('ordre')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Index pour optimiser le tri et filtrage
            $table->index(['categorie_conseil_id', 'status', 'ordre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('section_conseils');
    }
};
