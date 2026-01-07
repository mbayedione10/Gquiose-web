<?php

namespace Database\Seeders;

use App\Models\Utilisateur;
use App\Models\Ville;
use Illuminate\Database\Seeder;

class TestUsersSeeder extends Seeder
{
    public function run(): void
    {
        $conakry = Ville::where('name', 'Conakry')->first();

        $users = [
            [
                'nom' => 'Diallo',
                'prenom' => 'Fatou',
                'phone' => '+224621234567',
                'email' => 'fatou.diallo@test.gn',
                'sexe' => 'F',
                'status' => true,
                'anneedenaissance' => 1995,
                'ville_id' => $conakry?->id,
                'password' => bcrypt('password'),
                'phone_verified_at' => now(),
            ],
            [
                'nom' => 'Camara',
                'prenom' => 'Aissatou',
                'phone' => '+224623456789',
                'email' => 'aissatou.camara@test.gn',
                'sexe' => 'F',
                'status' => true,
                'anneedenaissance' => 1998,
                'ville_id' => $conakry?->id,
                'password' => bcrypt('password'),
                'phone_verified_at' => now(),
            ],
            [
                'nom' => 'Condé',
                'prenom' => 'Mariama',
                'phone' => '+224625678901',
                'email' => 'mariama.conde@test.gn',
                'sexe' => 'F',
                'status' => true,
                'anneedenaissance' => 1997,
                'ville_id' => $conakry?->id,
                'password' => bcrypt('password'),
                'phone_verified_at' => now(),
            ],
            [
                'nom' => 'Bah',
                'prenom' => 'Kadiatou',
                'phone' => '+224622345678',
                'email' => 'kadiatou.bah@test.gn',
                'sexe' => 'F',
                'status' => true,
                'anneedenaissance' => 1999,
                'ville_id' => $conakry?->id,
                'password' => bcrypt('password'),
                'phone_verified_at' => now(),
            ],
            [
                'nom' => 'Sylla',
                'prenom' => 'Fatoumata',
                'phone' => '+224624567890',
                'email' => 'fatoumata.sylla@test.gn',
                'sexe' => 'F',
                'status' => true,
                'anneedenaissance' => 2000,
                'ville_id' => $conakry?->id,
                'password' => bcrypt('password'),
                'phone_verified_at' => now(),
            ],
        ];

        foreach ($users as $userData) {
            Utilisateur::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
        }

        $this->command->info('✅ 5 utilisatrices de test créées avec succès.');
    }
}
