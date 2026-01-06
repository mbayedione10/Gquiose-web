#!/usr/bin/env php
<?php

/**
 * Script de réinitialisation du compte admin
 * Usage: php reset_admin.php
 */

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

echo "\n🔧 RÉINITIALISATION DU COMPTE ADMIN\n";
echo "===================================\n\n";

// Chercher le compte admin
$admin = User::where('email', 'admin@admin.com')->first();

if (!$admin) {
    echo "❌ Compte admin@admin.com non trouvé\n";
    echo "Création d'un nouveau compte...\n";
    
    $superAdminRole = Role::where('name', 'Super Admin')->first();
    if (!$superAdminRole) {
        $superAdminRole = Role::where('name', 'Admin')->first();
    }
    
    if (!$superAdminRole) {
        echo "❌ Erreur: Aucun rôle Admin trouvé\n";
        exit(1);
    }
    
    $admin = User::create([
        'name' => 'Super Admin',
        'email' => 'admin@admin.com',
        'phone' => '+224000000000',
        'password' => Hash::make('password'),
        'role_id' => $superAdminRole->id,
        'email_verified_at' => now(),
    ]);
    
    echo "✅ Compte créé avec succès\n";
} else {
    echo "👤 Compte trouvé: " . $admin->name . "\n";
}

// Vérifier et corriger le rôle
$superAdminRole = Role::where('name', 'Super Admin')->first();
if (!$superAdminRole) {
    $superAdminRole = Role::where('name', 'Admin')->first();
}

if ($admin->role_id != $superAdminRole->id) {
    echo "\n⚠️  Rôle incorrect détecté\n";
    echo "   Ancien rôle: " . ($admin->role ? $admin->role->name : 'Aucun') . "\n";
    $admin->role_id = $superAdminRole->id;
    $admin->save();
    echo "   Nouveau rôle: " . $superAdminRole->name . "\n";
}

// Réinitialiser le mot de passe
$admin->password = Hash::make('admin');
$admin->save();

echo "\n✅ COMPTE ADMIN RÉINITIALISÉ\n";
echo "============================\n";
echo "📧 Email: admin@admin.com\n";
echo "🔑 Mot de passe: admin\n";
echo "🔐 Rôle: " . $admin->role->name . "\n";
echo "⭐ Super Admin: " . ($admin->isSuperAdmin() ? 'Oui' : 'Non') . "\n";
echo "👥 Permissions: " . $admin->getPermissions()->count() . "\n";

// Test d'accès Filament
try {
    $canAccess = $admin->canAccessPanel(null);
    echo "🚪 Accès Filament: " . ($canAccess ? '✅ OUI' : '❌ NON') . "\n";
} catch (Exception $e) {
    echo "⚠️  Erreur lors du test d'accès: " . $e->getMessage() . "\n";
}

echo "\n✅ Vous pouvez maintenant vous connecter avec ces identifiants\n\n";
