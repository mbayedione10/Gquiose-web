<?php

namespace Database\Seeders;

use App\Models\Alerte;
use Illuminate\Database\Seeder;

class AlerteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Alerte::factory()
            ->count(5)
            ->create();
    }
}
