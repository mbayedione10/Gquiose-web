<?php

namespace Database\Seeders;

use App\Models\Thematique;
use Illuminate\Database\Seeder;

class ThematiqueSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Thematique::factory()
            ->count(5)
            ->create();
    }
}
