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
        // Seeders essentiels
        $this->call([
            SuperAdminSeeder::class,
            VillesGuineeSeeder::class,
            TestUsersSeeder::class,
        ]);

        /*$this->call(AlerteSeeder::class);
        $this->call(ArticleSeeder::class);
        $this->call(PermissionSeeder::class);
        $this->call(QuestionSeeder::class);
        $this->call(ResponseSeeder::class);
        $this->call(RoleSeeder::class);
        $this->call(RubriqueSeeder::class);
        $this->call(StructureSeeder::class);
        $this->call(SuiviSeeder::class);
        $this->call(ThematiqueSeeder::class);
        $this->call(TypeAlerteSeeder::class);
        $this->call(TypeStructureSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(UtilisateurSeeder::class);
        $this->call(VilleSeeder::class);*/
    }
}
