<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'name' => 'Super Admin',
                'description' => 'Accès complet à toutes les fonctionnalités de l\'application, y compris les configurations sensibles',
                'status' => true,
            ],
            [
                'name' => 'Admin',
                'description' => 'Gestion complète de l\'application sauf les configurations sensibles et critiques',
                'status' => true,
            ],
            [
                'name' => 'Éditeur',
                'description' => 'Gestion des articles, rubriques, thématiques et contenus éducatifs',
                'status' => true,
            ],
            [
                'name' => 'Modérateur',
                'description' => 'Gestion des alertes VBG, modération du forum et des contenus utilisateurs',
                'status' => true,
            ],
            [
                'name' => 'Structure Sanitaire',
                'description' => 'Accès limité pour les structures de santé partenaires',
                'status' => true,
            ],
        ];

        foreach ($roles as $roleData) {
            Role::updateOrCreate(
                ['name' => $roleData['name']],
                [
                    'description' => $roleData['description'],
                    'status' => $roleData['status'],
                ]
            );
        }

        // Supprimer les anciens rôles qui ne sont plus utilisés
        Role::whereNotIn('name', array_column($roles, 'name'))->delete();

        $this->command->info('✅ '.count($roles).' rôles configurés');
    }
}
