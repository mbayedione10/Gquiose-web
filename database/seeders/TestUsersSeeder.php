<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Utilisateur;
use App\Models\Ville;

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
                'nom' => 'Bah',
                'prenom' => 'Mamadou',
                'phone' => '+224622345678',
                'email' => 'mamadou.bah@test.gn',
                'sexe' => 'M',
                'status' => true,
                'dob' => '1992-07-22',
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
                'nom' => 'Sylla',
                'prenom' => 'Ibrahima',
                'phone' => '+224624567890',
                'email' => 'ibrahima.sylla@test.gn',
                'sexe' => 'M',
                'status' => true,
                'dob' => '1990-01-30',
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
        ];

        foreach ($users as $user) {
            Utilisateur::firstOrCreate(
                ['email' => $user['email']],
                $user
            );
        }

        $this->command->info('✅ 5 utilisateurs de test créés (mot de passe: password)');
    }
}
