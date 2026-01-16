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
        Schema::table('push_notifications', function (Blueprint $table) {
            // Deep linking: Type de ressource liée (article, forum_reply, cycle, etc.)
            $table->string('related_type')->nullable()->after('action');
            // Deep linking: ID de la ressource liée
            $table->unsignedBigInteger('related_id')->nullable()->after('related_type');
            // Catégorie de notification pour filtrage
            $table->string('category')->nullable()->after('related_id');
            
            // Index pour améliorer les performances des requêtes
            $table->index(['related_type', 'related_id']);
            $table->index('category');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('push_notifications', function (Blueprint $table) {
            $table->dropIndex(['related_type', 'related_id']);
            $table->dropIndex(['category']);
            $table->dropColumn(['related_type', 'related_id', 'category']);
        });
    }
};
