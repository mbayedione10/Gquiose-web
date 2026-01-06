#!/usr/bin/env php
<?php

/**
 * Script de test du système de rôles et permissions
 * Usage: php test_roles.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use App\Filament\Resources\UserResource;
use App\Filament\Resources\RoleResource;
use App\Filament\Resources\PermissionResource;
use App\Filament\Resources\ArticleResource;
use App\Filament\Resources\AlerteResource;

echo "\n🧪 TEST DU SYSTÈME DE RÔLES ET PERMISSIONS\n";
echo "==========================================\n\n";

// Test avec le premier utilisateur
$user = User::first();
if (!$user) {
    echo "❌ Aucun utilisateur trouvé\n";
    exit(1);
}

echo "👤 Utilisateur testé: {$user->name}\n";
echo "📧 Email: {$user->email}\n";
echo "🔐 Rôle: " . ($user->role->name ?? 'Aucun') . "\n";
echo "⭐ Super Admin: " . ($user->isSuperAdmin() ? 'Oui' : 'Non') . "\n\n";

// Simuler l'authentification
Auth::login($user);

echo "📋 Tests de permissions individuelles:\n";
echo "---------------------------------------\n";

$permissions = [
    'manage_users' => 'Gérer les utilisateurs',
    'manage_roles' => 'Gérer les rôles',
    'manage_articles' => 'Gérer les articles',
    'manage_alerts' => 'Gérer les alertes',
    'view_stats' => 'Voir les statistiques',
];

foreach ($permissions as $perm => $label) {
    $hasPermission = $user->hasPermission($perm);
    $icon = $hasPermission ? '✅' : '❌';
    echo "  {$icon} {$label} ({$perm})\n";
}

echo "\n📊 Tests d'accès aux ressources Filament:\n";
echo "-----------------------------------------\n";

$resources = [
    'UserResource' => 'Administrateurs',
    'RoleResource' => 'Rôles',
    'PermissionResource' => 'Permissions',
];

foreach ($resources as $class => $label) {
    $fullClass = "App\\Filament\\Resources\\{$class}";
    if (class_exists($fullClass)) {
        try {
            $canView = $fullClass::canViewAny();
            $canCreate = $fullClass::canCreate();
            $icon = $canView ? '✅' : '❌';
            echo "  {$icon} {$label}\n";
            echo "      - Voir: " . ($canView ? 'Oui' : 'Non') . "\n";
            echo "      - Créer: " . ($canCreate ? 'Oui' : 'Non') . "\n";
        } catch (Exception $e) {
            echo "  ⚠️  {$label}: Erreur - {$e->getMessage()}\n";
        }
    }
}

echo "\n🔑 Liste complète des permissions de l'utilisateur:\n";
echo "---------------------------------------------------\n";
$userPermissions = $user->getPermissionNames();
if (empty($userPermissions)) {
    echo "  ⚠️  Aucune permission trouvée\n";
} else {
    foreach ($userPermissions as $perm) {
        echo "  • {$perm}\n";
    }
}

echo "\n📈 Résumé des rôles:\n";
echo "-------------------\n";
$roles = Role::withCount('permissions', 'users')->get();
foreach ($roles as $role) {
    echo "\n🔐 {$role->name}\n";
    echo "   👥 {$role->users_count} utilisateur(s)\n";
    echo "   🔑 {$role->permissions_count} permission(s)\n";
    if ($role->description) {
        echo "   📝 {$role->description}\n";
    }
}

echo "\n\n✅ Tests terminés!\n\n";
