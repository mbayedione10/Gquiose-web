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
            SousTypeViolenceNumeriqueSeeder::class,
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
            ReponseEvaluationSeeder::class,
            NotificationTemplateSeeder::class,
        ]);

        // Nouvelles tables
        $this->call([
            PlateformeSeeder::class,
            NatureContenuSeeder::class,
        ]);

        // Données du cycle menstruel
        $this->call([
            MenstrualCycleSeeder::class,
        ]);

        // Notifications de test
        $this->call([
            NotificationLogSeeder::class,
            PushNotificationSeeder::class,
        ]);
    }
}