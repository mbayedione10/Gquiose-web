<?php

namespace Database\Seeders;

use App\Models\Theme;
use Illuminate\Database\Seeder;

class ThemeSeeder extends Seeder
{
    public function run(): void
    {
        $themes = [
            // Thèmes SSR
            ['name' => 'Puberté et Questions Intimes', 'status' => true],
            ['name' => 'Première Fois et Sexualité', 'status' => true],
            ['name' => 'Contraception et Préservatifs', 'status' => true],
            ['name' => 'Règles et Cycle Menstruel', 'status' => true],
            ['name' => 'Grossesse et IVG', 'status' => true],
            ['name' => 'IST, VIH et Dépistage', 'status' => true],
            
            // Thèmes VBG et Relations
            ['name' => 'Relations Amoureuses Saines', 'status' => true],
            ['name' => 'Consentement et Respect', 'status' => true],
            ['name' => 'J\'ai Subi des Violences', 'status' => true],
            ['name' => 'Harcèlement à l\'École', 'status' => true],
            ['name' => 'Violence en Ligne', 'status' => true],
            
            // Thèmes Droits et Soutien
            ['name' => 'Mes Droits en Tant que Jeune', 'status' => true],
            ['name' => 'Où Trouver de l\'Aide ?', 'status' => true],
            ['name' => 'Parler à mes Parents', 'status' => true],
        ];

        foreach ($themes as $theme) {
            Theme::firstOrCreate(
                ['name' => $theme['name']],
                ['status' => $theme['status']]
            );
        }

        $this->command->info('✅ ' . count($themes) . ' thèmes de forum pour jeunes créés');
    }
}
