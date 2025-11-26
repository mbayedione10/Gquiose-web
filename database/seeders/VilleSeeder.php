<?php

namespace Database\Seeders;

use App\Models\Ville;
use Illuminate\Database\Seeder;

class VilleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $villesGuinee = [
            ['name' => 'Conakry', 'status' => true],
            ['name' => 'Nzérékoré', 'status' => true],
            ['name' => 'Kankan', 'status' => true],
            ['name' => 'Labé', 'status' => true],
            ['name' => 'Kindia', 'status' => true],
            ['name' => 'Mamou', 'status' => true],
            ['name' => 'Boké', 'status' => true],
            ['name' => 'Faranah', 'status' => true],
            ['name' => 'Guékédou', 'status' => true],
            ['name' => 'Kissidougou', 'status' => true],
            ['name' => 'Macenta', 'status' => true],
            ['name' => 'Dabola', 'status' => true],
            ['name' => 'Siguiri', 'status' => true],
            ['name' => 'Pita', 'status' => true],
            ['name' => 'Télimélé', 'status' => true],
            ['name' => 'Fria', 'status' => true],
            ['name' => 'Kamsar', 'status' => true],
            ['name' => 'Coyah', 'status' => true],
            ['name' => 'Dubréka', 'status' => true],
            ['name' => 'Forécariah', 'status' => true],
        ];

        foreach ($villesGuinee as $ville) {
            Ville::create($ville);
        }
    }
}
