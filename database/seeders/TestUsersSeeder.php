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
                'dob' => '1995-03-15',
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
                'dob' => '1998-11-08',
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
                'dob' => '1997-05-19',
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
                'dob' => '1999-07-22',
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
                'dob' => '2000-01-30',
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
