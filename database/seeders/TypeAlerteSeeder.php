<?php

namespace Database\Seeders;

use App\Models\TypeAlerte;
use Illuminate\Database\Seeder;

class TypeAlerteSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Types d'alertes VBG
            ['name' => 'Violence Conjugale', 'status' => true],
            ['name' => 'Harcèlement Sexuel', 'status' => true],
            ['name' => 'Agression Sexuelle', 'status' => true],
            ['name' => 'Mariage Forcé', 'status' => true],
            ['name' => 'MGF (Excision)', 'status' => true],
            ['name' => 'Cyberharcèlement', 'status' => true],
            ['name' => 'Violence Scolaire', 'status' => true],
            ['name' => 'Exploitation Sexuelle', 'status' => true],
            ['name' => 'Autres Violences', 'status' => true],
        ];

        foreach ($types as $type) {
            TypeAlerte::firstOrCreate(
                ['name' => $type['name']],
                ['status' => $type['status']]
            );
        }

        $this->command->info('✅ ' . count($types) . ' types d\'alertes VBG créés');
    }
}
