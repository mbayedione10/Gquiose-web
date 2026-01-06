<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // Permissions Admin
            ['name' => 'manage_users', 'label' => 'Gérer les utilisateurs administrateurs', 'type' => 'admin'],
            ['name' => 'manage_roles', 'label' => 'Gérer les rôles et permissions', 'type' => 'admin'],
            ['name' => 'manage_structures', 'label' => 'Gérer les structures partenaires', 'type' => 'admin'],
            ['name' => 'manage_settings', 'label' => 'Gérer les paramètres système', 'type' => 'admin'],
            ['name' => 'manage_notifications', 'label' => 'Gérer les notifications push', 'type' => 'admin'],
            ['name' => 'view_all_stats', 'label' => 'Voir toutes les statistiques', 'type' => 'admin'],
            
            // Permissions Contenu
            ['name' => 'manage_articles', 'label' => 'Gérer les articles', 'type' => 'content'],
            ['name' => 'manage_rubriques', 'label' => 'Gérer les rubriques', 'type' => 'content'],
            ['name' => 'manage_thematiques', 'label' => 'Gérer les thématiques', 'type' => 'content'],
            ['name' => 'manage_faqs', 'label' => 'Gérer les FAQs', 'type' => 'content'],
            ['name' => 'manage_videos', 'label' => 'Gérer les vidéos', 'type' => 'content'],
            ['name' => 'manage_conseils', 'label' => 'Gérer les conseils de sécurité', 'type' => 'content'],
            
            // Permissions Modération
            ['name' => 'manage_alerts', 'label' => 'Gérer les alertes VBG', 'type' => 'moderation'],
            ['name' => 'view_alerts', 'label' => 'Voir les alertes VBG', 'type' => 'moderation'],
            ['name' => 'moderate_forum', 'label' => 'Modérer le forum', 'type' => 'moderation'],
            ['name' => 'moderate_responses', 'label' => 'Modérer les réponses', 'type' => 'moderation'],
            ['name' => 'view_utilisateurs', 'label' => 'Voir les utilisateurs mobiles', 'type' => 'moderation'],
            
            // Permissions Statistiques
            ['name' => 'view_stats', 'label' => 'Voir les statistiques de base', 'type' => 'analytics'],
            ['name' => 'export_data', 'label' => 'Exporter les données', 'type' => 'analytics'],
        ];

        foreach ($permissions as $permission) {
            Permission::updateOrCreate(
                ['name' => $permission['name']],
                [
                    'label' => $permission['label'],
                    'type' => $permission['type']
                ]
            );
        }

        $this->command->info('✅ ' . count($permissions) . ' permissions configurées');
        
        // Associer les permissions aux rôles
        $this->assignPermissionsToRoles();
    }
    
    private function assignPermissionsToRoles(): void
    {
        $rolePermissions = [
            'Super Admin' => [
                // Toutes les permissions
                'manage_users', 'manage_roles', 'manage_structures', 'manage_settings',
                'manage_notifications', 'view_all_stats', 'manage_articles', 'manage_rubriques',
                'manage_thematiques', 'manage_faqs', 'manage_videos', 'manage_conseils',
                'manage_alerts', 'view_alerts', 'moderate_forum', 'moderate_responses',
                'view_utilisateurs', 'view_stats', 'export_data'
            ],
            'Admin' => [
                // Toutes sauf manage_roles et manage_settings
                'manage_users', 'manage_structures', 'manage_notifications', 'view_all_stats',
                'manage_articles', 'manage_rubriques', 'manage_thematiques', 'manage_faqs',
                'manage_videos', 'manage_conseils', 'manage_alerts', 'view_alerts',
                'moderate_forum', 'moderate_responses', 'view_utilisateurs', 'view_stats', 'export_data'
            ],
            'Éditeur' => [
                // Gestion de contenu uniquement
                'manage_articles', 'manage_rubriques', 'manage_thematiques', 'manage_faqs',
                'manage_videos', 'manage_conseils', 'view_stats'
            ],
            'Modérateur' => [
                // Modération et alertes
                'manage_alerts', 'view_alerts', 'moderate_forum', 'moderate_responses',
                'view_utilisateurs', 'view_stats'
            ],
            'Structure Sanitaire' => [
                // Vue limitée
                'view_alerts', 'view_stats'
            ],
        ];

        foreach ($rolePermissions as $roleName => $permissionNames) {
            $role = \App\Models\Role::where('name', $roleName)->first();
            if ($role) {
                $permissions = Permission::whereIn('name', $permissionNames)->get();
                $role->permissions()->sync($permissions->pluck('id'));
                $this->command->info("✅ Permissions assignées au rôle: {$roleName}");
            }
        }
    }
}
