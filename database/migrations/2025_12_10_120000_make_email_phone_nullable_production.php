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
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            // Pour MySQL/MariaDB
            DB::statement('ALTER TABLE utilisateurs MODIFY email VARCHAR(255) NULL');
            DB::statement('ALTER TABLE utilisateurs MODIFY phone VARCHAR(255) NULL');
        } elseif ($driver === 'pgsql') {
            // Pour PostgreSQL
            DB::statement('ALTER TABLE utilisateurs ALTER COLUMN email DROP NOT NULL');
            DB::statement('ALTER TABLE utilisateurs ALTER COLUMN phone DROP NOT NULL');
        } elseif ($driver === 'sqlite') {
            // Pour SQLite (déjà géré dans le code local)
            Schema::disableForeignKeyConstraints();
            
            $users = DB::table('utilisateurs')->get();
            
            Schema::dropIfExists('utilisateurs_temp');
            DB::statement('ALTER TABLE utilisateurs RENAME TO utilisateurs_temp');
            
            Schema::create('utilisateurs', function (Blueprint $table) {
                $table->bigIncrements('id');
                $table->string('nom');
                $table->string('prenom');
                $table->string('email')->nullable()->unique();
                $table->string('phone')->nullable()->unique();
                $table->string('sexe');
                $table->boolean('status');
                $table->timestamps();
                $table->string('password')->nullable();
                $table->string('dob')->nullable();
                $table->string('provider')->nullable();
                $table->string('provider_id')->nullable();
                $table->string('photo')->nullable();
                $table->text('fcm_token')->nullable();
                $table->string('platform')->nullable();
                $table->timestamp('email_verified_at')->nullable();
                $table->unsignedBigInteger('ville_id')->nullable();
                $table->timestamp('phone_verified_at')->nullable();
            });
            
            foreach ($users as $user) {
                DB::table('utilisateurs')->insert((array)$user);
            }
            
            Schema::dropIfExists('utilisateurs_temp');
            Schema::enableForeignKeyConstraints();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            DB::statement('ALTER TABLE utilisateurs MODIFY email VARCHAR(255) NOT NULL');
            DB::statement('ALTER TABLE utilisateurs MODIFY phone VARCHAR(255) NOT NULL');
        } elseif ($driver === 'pgsql') {
            DB::statement('ALTER TABLE utilisateurs ALTER COLUMN email SET NOT NULL');
            DB::statement('ALTER TABLE utilisateurs ALTER COLUMN phone SET NOT NULL');
        }
    }
};
