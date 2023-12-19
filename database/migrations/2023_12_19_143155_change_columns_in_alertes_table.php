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
        Schema::table('alertes', function (Blueprint $table) {
            $table->string("description")->nullable()->change();
            $table->unsignedBigInteger("ville_id")->nullable()->change();
            $table->unsignedBigInteger("type_alerte_id")->nullable()->change();

            $table->unsignedBigInteger("utilisateur_id");
            $table->foreign("utilisateur_id")
                ->references("id")
                ->on("utilisateurs")
                ->cascadeOnDelete()
                ->cascadeOnUpdate();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('alertes', function (Blueprint $table) {
            //
        });
    }
};
