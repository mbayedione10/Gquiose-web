<?php

namespace Database\Seeders;

use App\Models\Rubrique;
use Illuminate\Database\Seeder;

class RubriqueSeeder extends Seeder
{
    public function run(): void
    {
        $rubriques = [
            ['name' => 'Actualités Santé', 'status' => true],
            ['name' => 'Conseils Pratiques', 'status' => true],
            ['name' => 'Prévention', 'status' => true],
            ['name' => 'Santé de la Femme', 'status' => true],
            ['name' => 'Santé de l\'Enfant', 'status' => true],
            ['name' => 'Alimentation', 'status' => true],
            ['name' => 'Activité Physique', 'status' => true],
            ['name' => 'Bien-être', 'status' => true],
            ['name' => 'Questions Fréquentes', 'status' => true],
            ['name' => 'Guides et Tutoriels', 'status' => true],
        ];

        foreach ($rubriques as $rubrique) {
            Rubrique::firstOrCreate(
                ['name' => $rubrique['name']],
                ['status' => $rubrique['status']]
            );
        }

        $this->command->info('✅ ' . count($rubriques) . ' rubriques créées');
    }
}
