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
        Schema::create('notification_schedules', function (Blueprint $table) {
            $table->id();

            // Content
            $table->string('name'); // Nom de la campagne
            $table->string('title', 65);
            $table->text('message'); // Max 240 caractères
            $table->string('icon')->nullable();
            $table->string('action')->nullable(); // Deep link
            $table->string('image')->nullable();

            // Scheduling
            $table->enum('send_type', ['immediate', 'scheduled', 'recurring'])->default('immediate');
            $table->timestamp('scheduled_at')->nullable();
            $table->string('recurring_pattern')->nullable(); // cron expression pour récurrent
            $table->timestamp('last_sent_at')->nullable();

            // Segmentation (JSON pour flexibilité)
            $table->json('target_filters')->nullable(); // {age: {min: 18, max: 35}, sexe: ['F'], villes: [1, 2]}
            $table->integer('estimated_recipients')->default(0);
            $table->integer('actual_recipients')->default(0);

            // Status
            $table->enum('status', ['draft', 'scheduled', 'sending', 'sent', 'cancelled', 'failed'])->default('draft');
            $table->timestamp('sent_at')->nullable();

            // Statistics
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('opened_count')->default(0);
            $table->integer('clicked_count')->default(0);
            $table->integer('failed_count')->default(0);

            // Metadata
            $table->enum('category', ['cycle', 'content', 'forum', 'health_tips', 'admin', 'other'])->default('other');
            $table->string('created_by')->nullable(); // Admin qui a créé
            $table->boolean('is_template')->default(false); // Template réutilisable
            $table->foreignId('duplicated_from')->nullable()->constrained('notification_schedules')->onDelete('set null');

            $table->timestamps();
            $table->softDeletes();

            // Indexes
            $table->index('status');
            $table->index('send_type');
            $table->index('scheduled_at');
            $table->index('category');
            $table->index(['status', 'scheduled_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('notification_schedules');
    }
};
