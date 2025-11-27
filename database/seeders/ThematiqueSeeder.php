<?php

namespace Database\Seeders;

use App\Models\Thematique;
use Illuminate\Database\Seeder;

class ThematiqueSeeder extends Seeder
{
    public function run(): void
    {
        $thematiques = [
            ['name' => 'Santé Maternelle et Infantile', 'status' => true],
            ['name' => 'Maladies Infectieuses', 'status' => true],
            ['name' => 'Nutrition', 'status' => true],
            ['name' => 'Vaccination', 'status' => true],
            ['name' => 'Paludisme', 'status' => true],
            ['name' => 'VIH/SIDA', 'status' => true],
            ['name' => 'Tuberculose', 'status' => true],
            ['name' => 'Hygiène et Assainissement', 'status' => true],
            ['name' => 'Santé Reproductive', 'status' => true],
            ['name' => 'Maladies Non Transmissibles', 'status' => true],
            ['name' => 'Santé Mentale', 'status' => true],
            ['name' => 'Premiers Secours', 'status' => true],
        ];

        foreach ($thematiques as $thematique) {
            Thematique::firstOrCreate(
                ['name' => $thematique['name']],
                ['status' => $thematique['status']]
            );
        }

        $this->command->info('✅ ' . count($thematiques) . ' thématiques créées');
    }
}
