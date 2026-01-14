<?php

namespace Database\Seeders;

use App\Models\Rubrique;
use Illuminate\Database\Seeder;

class RubriqueSeeder extends Seeder
{
    public function run(): void
    {
        $rubriques = [
            ['name' => 'Je Découvre Mon Corps', 'status' => true],
            ['name' => 'Sexualité et Relations', 'status' => true],
            ['name' => 'Ma Santé Reproductive', 'status' => true],
            ['name' => 'Contraception : Mes Options', 'status' => true],
            ['name' => 'Prévention IST/VIH', 'status' => true],
            ['name' => 'Dire Non aux Violences', 'status' => true],
            ['name' => 'Mes Droits, Mon Pouvoir', 'status' => true],
            ['name' => 'Témoignages de Jeunes', 'status' => true],
            ['name' => 'Questions Sans Tabou', 'status' => true],
            ['name' => 'Où Trouver de l\'Aide ?', 'status' => true],
        ];

        foreach ($rubriques as $rubrique) {
            Rubrique::firstOrCreate(
                ['name' => $rubrique['name']],
                ['status' => $rubrique['status']]
            );
        }

        $this->command->info('✅ '.count($rubriques).' rubriques pour jeunes créées');
    }
}
