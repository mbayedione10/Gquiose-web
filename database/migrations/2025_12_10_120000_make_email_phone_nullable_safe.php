<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $driver = Schema::getConnection()->getDriverName();
        
        if ($driver === 'mysql') {
            // Pour MySQL/MariaDB - modification directe sans recréer les index
            DB::statement('ALTER TABLE utilisateurs MODIFY email VARCHAR(255) NULL');
            DB::statement('ALTER TABLE utilisateurs MODIFY phone VARCHAR(255) NULL');
            
        } elseif ($driver === 'pgsql') {
            // Pour PostgreSQL
            DB::statement('ALTER TABLE utilisateurs ALTER COLUMN email DROP NOT NULL');
            DB::statement('ALTER TABLE utilisateurs ALTER COLUMN phone DROP NOT NULL');
            
        } elseif ($driver === 'sqlite') {
            // Pour SQLite - vérifier si déjà nullable avant de modifier
            $tableInfo = DB::select("PRAGMA table_info(utilisateurs)");
            
            $emailNullable = false;
            $phoneNullable = false;
            
            foreach ($tableInfo as $column) {
                if ($column->name === 'email' && $column->notnull == 0) {
                    $emailNullable = true;
                }
                if ($column->name === 'phone' && $column->notnull == 0) {
                    $phoneNullable = true;
                }
            }
            
            // Si déjà nullable, ne rien faire
            if ($emailNullable && $phoneNullable) {
                echo "Les colonnes email et phone sont déjà nullable. Aucune modification nécessaire.\n";
                return;
            }
            
            // Sinon, recréer la table
            Schema::disableForeignKeyConstraints();
            
            $users = DB::table('utilisateurs')->get();
            
            Schema::dropIfExists('utilisateurs_backup');
            DB::statement('ALTER TABLE utilisateurs RENAME TO utilisateurs_backup');
            
            // Obtenir toutes les colonnes de la table backup
            $columns = DB::select("PRAGMA table_info(utilisateurs_backup)");
            
            Schema::create('utilisateurs', function ($table) use ($columns) {
                foreach ($columns as $column) {
                    $name = $column->name;
                    $type = $column->type;
                    
                    // Colonnes spéciales
                    if ($name === 'id') {
                        $table->bigIncrements('id');
                        continue;
                    }
                    
                    if ($name === 'created_at' || $name === 'updated_at') {
                        continue; // Sera géré par timestamps()
                    }
                    
                    // Email et phone nullable
                    if ($name === 'email') {
                        $table->string('email')->nullable()->unique();
                        continue;
                    }
                    
                    if ($name === 'phone') {
                        $table->string('phone')->nullable()->unique();
                        continue;
                    }
                    
                    // Autres colonnes selon leur type
                    $nullable = $column->notnull == 0;
                    
                    if (stripos($type, 'INTEGER') !== false) {
                        $col = $table->integer($name);
                    } elseif (stripos($type, 'BIGINT') !== false) {
                        $col = $table->bigInteger($name);
                    } elseif (stripos($type, 'BOOLEAN') !== false || stripos($type, 'TINYINT(1)') !== false) {
                        $col = $table->boolean($name);
                    } elseif (stripos($type, 'TEXT') !== false) {
                        $col = $table->text($name);
                    } elseif (stripos($type, 'DATETIME') !== false || stripos($type, 'TIMESTAMP') !== false) {
                        $col = $table->timestamp($name);
                    } else {
                        $col = $table->string($name);
                    }
                    
                    if ($nullable) {
                        $col->nullable();
                    }
                }
                
                $table->timestamps();
            });
            
            // Restaurer les données
            foreach ($users as $user) {
                DB::table('utilisateurs')->insert((array)$user);
            }
            
            Schema::dropIfExists('utilisateurs_backup');
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
        // Pas de rollback pour SQLite car trop complexe
    }
};
