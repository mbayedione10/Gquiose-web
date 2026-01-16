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
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->string('related_type')->nullable()->after('action')
                ->comment('Type de contenu lié pour deep linking: article, forum_reply, cycle, etc.');
            $table->unsignedBigInteger('related_id')->nullable()->after('related_type')
                ->comment('ID du contenu lié pour deep linking');
            
            $table->index(['related_type', 'related_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('notification_templates', function (Blueprint $table) {
            $table->dropIndex(['related_type', 'related_id']);
            $table->dropColumn(['related_type', 'related_id']);
        });
    }
};
