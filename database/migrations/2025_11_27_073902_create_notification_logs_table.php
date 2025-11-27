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
        Schema::create('notification_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('utilisateur_id')->constrained('utilisateurs')->onDelete('cascade');
            $table->foreignId('notification_schedule_id')->nullable()->constrained('notification_schedules')->onDelete('set null');

            $table->string('title', 65);
            $table->text('message');
            $table->string('icon')->nullable();
            $table->string('action')->nullable();
            $table->string('image')->nullable();

            $table->enum('type', ['automatic', 'manual', 'triggered'])->default('manual');
            $table->string('category')->nullable();

            // Tracking status
            $table->enum('status', ['pending', 'sent', 'delivered', 'opened', 'clicked', 'failed'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('opened_at')->nullable();
            $table->timestamp('clicked_at')->nullable();
            $table->timestamp('failed_at')->nullable();

            $table->text('error_message')->nullable();
            $table->string('platform')->nullable(); // android, ios
            $table->string('fcm_message_id')->nullable(); // ID du message FCM/APNs

            $table->timestamps();

            // Indexes for performance
            $table->index('utilisateur_id');
            $table->index('status');
            $table->index('type');
            $table->index('category');
            $table->index('sent_at');
            $table->index(['utilisateur_id', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_logs');
    }
};
