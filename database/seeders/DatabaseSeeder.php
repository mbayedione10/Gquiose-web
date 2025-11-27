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
            ThemeSeeder::class,
        ]);

        // Utilisateurs
        $this->call([
            SuperAdminSeeder::class,
            TestUsersSeeder::class,
        ]);

        // Contenu principal
        $this->call([
            StructureSeeder::class,
            ArticleSeeder::class,
            AlerteSeeder::class,
            SuiviSeeder::class,
        ]);

        // Ressources éducatives
        $this->call([
            ConseilSeeder::class,
            FaqSeeder::class,
            VideoSeeder::class,
        ]);

        // Système
        $this->call([
            QuestionEvaluationSeeder::class,
            NotificationTemplateSeeder::class,
        ]);
    }
}
