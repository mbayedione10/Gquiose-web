<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // D'abord, convertir les chaînes vides en NULL
        DB::statement("UPDATE utilisateurs SET phone = NULL WHERE phone = ''");

        // Ensuite, modifier la colonne pour accepter NULL
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->string('phone')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('utilisateurs', function (Blueprint $table) {
            $table->string('phone')->nullable(false)->change();
        });
    }
};
