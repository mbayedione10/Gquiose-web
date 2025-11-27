<?php

namespace Database\Seeders;

use App\Models\TypeStructure;
use Illuminate\Database\Seeder;

class TypeStructureSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Hôpital National', 'status' => true],
            ['name' => 'Hôpital Régional', 'status' => true],
            ['name' => 'Hôpital Préfectoral', 'status' => true],
            ['name' => 'Centre de Santé', 'status' => true],
            ['name' => 'Poste de Santé', 'status' => true],
            ['name' => 'Clinique Privée', 'status' => true],
            ['name' => 'Pharmacie', 'status' => true],
            ['name' => 'Laboratoire', 'status' => true],
            ['name' => 'Maternité', 'status' => true],
            ['name' => 'Dispensaire', 'status' => true],
        ];

        foreach ($types as $type) {
            TypeStructure::firstOrCreate(
                ['name' => $type['name']],
                ['status' => $type['status']]
            );
        }

        $this->command->info('✅ ' . count($types) . ' types de structures créés');
    }
}
