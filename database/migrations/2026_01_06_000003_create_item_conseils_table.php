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
        Schema::create('item_conseils', function (Blueprint $table) {
            $table->id();
            $table->foreignId('section_conseil_id')
                ->constrained('section_conseils')
                ->cascadeOnDelete();
            $table->text('contenu');
            $table->integer('ordre')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();

            // Index pour optimiser le tri et filtrage
            $table->index(['section_conseil_id', 'status', 'ordre']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_conseils');
    }
};
