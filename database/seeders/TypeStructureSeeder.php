<?php

namespace Database\Seeders;

use App\Models\TypeStructure;
use Illuminate\Database\Seeder;

class TypeStructureSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            // Structures spécifiques SSR/VBG pour jeunes
            ['name' => 'Centre de Santé pour Jeunes', 'status' => true],
            ['name' => 'Centre de Planning Familial', 'status' => true],
            ['name' => 'Centre d\'Écoute VBG', 'status' => true],
            ['name' => 'Point de Service Jeunes', 'status' => true],
            ['name' => 'Maison des Jeunes', 'status' => true],
            ['name' => 'Centre de Prise en Charge VBG', 'status' => true],
            ['name' => 'Clinique Amie des Jeunes', 'status' => true],
            ['name' => 'Centre de Conseil et Dépistage', 'status' => true],
            ['name' => 'Association de Lutte contre VBG', 'status' => true],
            ['name' => 'Service Social', 'status' => true],
        ];

        foreach ($types as $type) {
            TypeStructure::firstOrCreate(
                ['name' => $type['name']],
                ['status' => $type['status']]
            );
        }

        $this->command->info('✅ '.count($types).' types de structures créés');
    }
}
