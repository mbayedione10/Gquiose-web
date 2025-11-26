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
        Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name'); // Nom du template
            $table->text('description')->nullable(); // Description du template
            $table->string('title', 65); // Titre de la notification
            $table->text('message'); // Message (max 240 chars)
            $table->string('icon')->nullable(); // Emoji
            $table->string('action')->nullable(); // Action/route
            $table->string('image')->nullable(); // Image optionnelle
            $table->enum('category', ['cycle', 'content', 'forum', 'health_tips', 'admin', 'other'])->default('other');
            $table->timestamps();

            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_templates');
    }
};
