<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            ['name' => 'manage_users', 'label' => 'Gérer les utilisateurs', 'type' => 'admin'],
            ['name' => 'manage_structures', 'label' => 'Gérer les structures', 'type' => 'admin'],
            ['name' => 'manage_alerts', 'label' => 'Gérer les alertes', 'type' => 'content'],
            ['name' => 'manage_articles', 'label' => 'Gérer les articles', 'type' => 'content'],
            ['name' => 'manage_rubriques', 'label' => 'Gérer les rubriques', 'type' => 'content'],
            ['name' => 'manage_thematiques', 'label' => 'Gérer les thématiques', 'type' => 'content'],
            ['name' => 'moderate_content', 'label' => 'Modérer les contenus', 'type' => 'moderation'],
            ['name' => 'view_stats', 'label' => 'Voir les statistiques', 'type' => 'analytics'],
            ['name' => 'manage_settings', 'label' => 'Gérer les paramètres', 'type' => 'admin'],
            ['name' => 'manage_notifications', 'label' => 'Gérer les notifications', 'type' => 'admin'],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name']],
                [
                    'label' => $permission['label'],
                    'type' => $permission['type']
                ]
            );
        }

        $this->command->info('✅ ' . count($permissions) . ' permissions créées');
    }
}
