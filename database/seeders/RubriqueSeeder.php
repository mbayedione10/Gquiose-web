<?php

namespace Database\Seeders;

use App\Models\Rubrique;
use Illuminate\Database\Seeder;

class RubriqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Rubrique::factory()
            ->count(5)
            ->create();
    }
}
