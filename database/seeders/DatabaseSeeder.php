<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Données de base
        $this->call([
            RoleSeeder::class,
            PermissionSeeder::class,
            VillesGuineeSeeder::class,
        ]);

        // Données de référence
        $this->call([
            TypeStructureSeeder::class,
            TypeAlerteSeeder::class,
            ThematiqueSeeder::class,
            RubriqueSeeder::class,
        ]);

        // Utilisateurs
        $this->call([
            SuperAdminSeeder::class,
            TestUsersSeeder::class,
        ]);
    }
}
