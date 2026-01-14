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
        Schema::create('messages', function (Blueprint $table) {
            $table->id();
            $table->text('question');

            $table->unsignedBigInteger('utilisateur_id');
            $table->foreign('utilisateur_id')
                ->on('utilisateurs')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('theme_id');
            $table->foreign('theme_id')
                ->on('themes')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->boolean('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('messages');
    }
};
