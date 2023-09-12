<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table
                ->foreign('type_alerte_id')
                ->references('id')
                ->on('type_alertes')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');

            $table
                ->foreign('ville_id')
                ->references('id')
                ->on('villes')
                ->onUpdate('CASCADE')
                ->onDelete('CASCADE');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            $table->dropForeign(['type_alerte_id']);
            $table->dropForeign(['ville_id']);
        });
    }
};
