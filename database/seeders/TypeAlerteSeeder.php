<?php

namespace Database\Seeders;

use App\Models\TypeAlerte;
use Illuminate\Database\Seeder;

class TypeAlerteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeAlerte::factory()
            ->count(5)
            ->create();
    }
}
