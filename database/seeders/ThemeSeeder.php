<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            ['name' => 'Questions générales de santé', 'status' => true],
            ['name' => 'Santé de la mère et de l\'enfant', 'status' => true],
            ['name' => 'Maladies infectieuses', 'status' => true],
            ['name' => 'Nutrition et alimentation', 'status' => true],
            ['name' => 'Vaccination', 'status' => true],
            ['name' => 'Planning familial', 'status' => true],
            ['name' => 'Hygiène et assainissement', 'status' => true],
            ['name' => 'Premiers secours', 'status' => true],
            ['name' => 'VIH/SIDA et IST', 'status' => true],
            ['name' => 'Paludisme', 'status' => true],
            ['name' => 'Diabète et hypertension', 'status' => true],
            ['name' => 'Santé mentale', 'status' => true],
        ];

        foreach ($themes as $theme) {
            Theme::firstOrCreate(
                ['name' => $theme['name']],
                ['status' => $theme['status']]
            );
        }

        $this->command->info('✅ ' . count($themes) . ' thèmes de forum créés');
    }
}
