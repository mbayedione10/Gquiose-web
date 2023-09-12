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
        Schema::create('alertes', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('ref')->unique();
            $table->text('description');
            $table->float('latitude')->nullable();
            $table->float('longitude')->nullable();
            $table->unsignedBigInteger('type_alerte_id');
            $table->string('etat');
            $table->unsignedBigInteger('ville_id');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alertes');
    }
};
