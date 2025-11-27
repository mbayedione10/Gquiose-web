<?php

namespace Database\Seeders;

use App\Models\TypeAlerte;
use Illuminate\Database\Seeder;

class TypeAlerteSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Épidémie', 'status' => true],
            ['name' => 'Urgence Sanitaire', 'status' => true],
            ['name' => 'Pénurie de Médicaments', 'status' => true],
            ['name' => 'Fermeture de Structure', 'status' => true],
            ['name' => 'Campagne de Vaccination', 'status' => true],
            ['name' => 'Information Sanitaire', 'status' => true],
            ['name' => 'Prévention', 'status' => true],
            ['name' => 'Sensibilisation', 'status' => true],
        ];

        foreach ($types as $type) {
            TypeAlerte::firstOrCreate(
                ['name' => $type['name']],
                ['status' => $type['status']]
            );
        }

        $this->command->info('✅ ' . count($types) . ' types d\'alertes créés');
    }
}
