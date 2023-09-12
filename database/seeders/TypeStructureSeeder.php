<?php

namespace Database\Seeders;

use App\Models\TypeStructure;
use Illuminate\Database\Seeder;

class TypeStructureSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        TypeStructure::factory()
            ->count(5)
            ->create();
    }
}
