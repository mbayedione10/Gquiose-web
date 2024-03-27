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
        Schema::create('chats', function (Blueprint $table) {
            $table->id();

            $table->text('message');

            $table->unsignedBigInteger('utilisateur_id');
            $table->foreign('utilisateur_id')
                ->on('utilisateurs')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->unsignedBigInteger('message_id');
            $table->foreign('message_id')
                ->on('messages')
                ->references('id')
                ->cascadeOnDelete()
                ->cascadeOnUpdate();

            $table->string('censure')->default(false);
            $table->boolean('status');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('chats');
    }
};
