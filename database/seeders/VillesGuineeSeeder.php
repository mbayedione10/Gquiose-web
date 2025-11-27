<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Ville;

class VillesGuineeSeeder extends Seeder
{
    public function run(): void
    {
        $villes = [
            // Région de Conakry
            ['name' => 'Conakry', 'status' => true],

            // Région de Kindia
            ['name' => 'Kindia', 'status' => true],
            ['name' => 'Dubréka', 'status' => true],
            ['name' => 'Coyah', 'status' => true],
            ['name' => 'Forécariah', 'status' => true],
            ['name' => 'Télimélé', 'status' => true],

            // Région de Boké
            ['name' => 'Boké', 'status' => true],
            ['name' => 'Boffa', 'status' => true],
            ['name' => 'Fria', 'status' => true],
            ['name' => 'Gaoual', 'status' => true],
            ['name' => 'Koundara', 'status' => true],

            // Région de Labé
            ['name' => 'Labé', 'status' => true],
            ['name' => 'Koubia', 'status' => true],
            ['name' => 'Lélouma', 'status' => true],
            ['name' => 'Mali', 'status' => true],
            ['name' => 'Tougué', 'status' => true],

            // Région de Mamou
            ['name' => 'Mamou', 'status' => true],
            ['name' => 'Dalaba', 'status' => true],
            ['name' => 'Pita', 'status' => true],

            // Région de Faranah
            ['name' => 'Faranah', 'status' => true],
            ['name' => 'Dabola', 'status' => true],
            ['name' => 'Dinguiraye', 'status' => true],
            ['name' => 'Kissidougou', 'status' => true],

            // Région de Kankan
            ['name' => 'Kankan', 'status' => true],
            ['name' => 'Kérouané', 'status' => true],
            ['name' => 'Kouroussa', 'status' => true],
            ['name' => 'Mandiana', 'status' => true],
            ['name' => 'Siguiri', 'status' => true],

            // Région de Nzérékoré
            ['name' => 'Nzérékoré', 'status' => true],
            ['name' => 'Beyla', 'status' => true],
            ['name' => 'Guéckédou', 'status' => true],
            ['name' => 'Lola', 'status' => true],
            ['name' => 'Macenta', 'status' => true],
            ['name' => 'Yomou', 'status' => true],
        ];

        foreach ($villes as $ville) {
            Ville::firstOrCreate(
                ['name' => $ville['name']],
                ['status' => $ville['status']]
            );
        }

        $this->command->info('✅ ' . count($villes) . ' villes de Guinée créées');
    }
}
