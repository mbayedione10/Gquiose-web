<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('push_notifications', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('message');
            $table->string('icon')->nullable();
            $table->string('action')->nullable();
            $table->string('image')->nullable();
            $table->enum('type', ['automatic', 'manual', 'scheduled'])->default('manual');
            $table->string('target_audience')->default('all');
            $table->json('filters')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->timestamp('sent_at')->nullable();
            $table->enum('status', ['pending', 'sent', 'failed'])->default('pending');
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->timestamps();

            $table->index(['status', 'scheduled_at']);
            $table->index('type');
        });

        Schema::create('user_notification_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->boolean('notifications_enabled')->default(true);
            $table->boolean('cycle_notifications')->default(true);
            $table->boolean('content_notifications')->default(true);
            $table->boolean('forum_notifications')->default(true);
            $table->boolean('health_tips_notifications')->default(true);
            $table->boolean('admin_notifications')->default(true);
            $table->time('quiet_start')->nullable();
            $table->time('quiet_end')->nullable();
            $table->boolean('do_not_disturb')->default(false);
            $table->timestamps();

            $table->unique('utilisateur_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('user_notification_preferences');
        Schema::dropIfExists('push_notifications');
    }
};
