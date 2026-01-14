<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SuperAdminSeeder extends Seeder
{
    public function run(): void
    {
        // Créer le rôle Admin s'il n'existe pas
        $adminRole = Role::firstOrCreate(
            ['name' => 'Admin'],
            ['status' => true]
        );

        // Créer le Super Admin s'il n'existe pas
        $superAdmin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            [
                'name' => 'Super Admin',
                'phone' => '+224000000000',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'role_id' => $adminRole->id,
            ]
        );

        if ($superAdmin->wasRecentlyCreated) {
            $this->command->info('✅ Super Admin créé: admin@admin.com / password');
        } else {
            $this->command->info('ℹ️  Super Admin existe déjà: admin@admin.com');
        }
    }
}
