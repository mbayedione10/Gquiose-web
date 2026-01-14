<?php

namespace Database\Seeders;

use App\Models\Thematique;
use Illuminate\Database\Seeder;

class ThematiqueSeeder extends Seeder
{
    public function run(): void
    {
        $thematiques = [
            // SSR - Santé Sexuelle et Reproductive
            ['name' => 'Puberté et Changements Corporels', 'status' => true],
            ['name' => 'Contraception et Planning Familial', 'status' => true],
            ['name' => 'IST et VIH/SIDA', 'status' => true],
            ['name' => 'Grossesse et Maternité Précoce', 'status' => true],
            ['name' => 'Cycle Menstruel et Hygiène Menstruelle', 'status' => true],
            ['name' => 'Santé Reproductive des Jeunes', 'status' => true],
            ['name' => 'Éducation Sexuelle Complète', 'status' => true],

            // VBG - Violences Basées sur le Genre
            ['name' => 'Violences Conjugales et Domestiques', 'status' => true],
            ['name' => 'Harcèlement et Abus Sexuels', 'status' => true],
            ['name' => 'Mariages Précoces et Forcés', 'status' => true],
            ['name' => 'Mutilations Génitales Féminines (MGF)', 'status' => true],
            ['name' => 'Cyberharcèlement et Violences en Ligne', 'status' => true],

            // Droits et Autonomisation
            ['name' => 'Droits Sexuels et Reproductifs', 'status' => true],
            ['name' => 'Égalité des Genres', 'status' => true],
            ['name' => 'Autonomisation des Jeunes Filles', 'status' => true],
        ];

        foreach ($thematiques as $thematique) {
            Thematique::firstOrCreate(
                ['name' => $thematique['name']],
                ['status' => $thematique['status']]
            );
        }

        $this->command->info('✅ '.count($thematiques).' thématiques SSR/VBG créées');
    }
}
