<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            ['name' => 'Admin', 'status' => true],
            ['name' => 'Gestionnaire', 'status' => true],
            ['name' => 'Modérateur', 'status' => true],
            ['name' => 'Structure Sanitaire', 'status' => true],
            ['name' => 'Utilisateur', 'status' => true],
        ];

        foreach ($roles as $role) {
            Role::firstOrCreate(
                ['name' => $role['name']],
                ['status' => $role['status']]
            );
        }

        $this->command->info('✅ ' . count($roles) . ' rôles créés');
    }
}
