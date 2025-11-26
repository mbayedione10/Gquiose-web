<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Utilisateur;

echo "🔧 Création d'utilisateurs de test...\n\n";

// Créer 5 utilisateurs de test
$users = [
    [
        'nom' => 'Test',
        'prenom' => 'User1',
        'email' => 'user1@test.com',
        'phone' => '+224600000001',
        'sexe' => 'F',
        'status' => true,
        'dob' => '1995-05-15',
        'password' => bcrypt('password123'),
        // Simuler un token FCM (pas réel, juste pour test)
        'fcm_token' => 'fake_android_token_' . uniqid(),
        'platform' => 'android',
    ],
    [
        'nom' => 'Test',
        'prenom' => 'User2',
        'email' => 'user2@test.com',
        'phone' => '+224600000002',
        'sexe' => 'M',
        'status' => true,
        'dob' => '1990-08-22',
        'password' => bcrypt('password123'),
        'fcm_token' => 'fake_android_token_' . uniqid(),
        'platform' => 'android',
    ],
    [
        'nom' => 'Test',
        'prenom' => 'User3',
        'email' => 'user3@test.com',
        'phone' => '+224600000003',
        'sexe' => 'F',
        'status' => true,
        'dob' => '2000-03-10',
        'password' => bcrypt('password123'),
        'fcm_token' => 'fake_ios_token_' . uniqid(),
        'platform' => 'ios',
    ],
    [
        'nom' => 'Test',
        'prenom' => 'User4',
        'email' => 'user4@test.com',
        'phone' => '+224600000004',
        'sexe' => 'M',
        'status' => true,
        'dob' => '1988-12-01',
        'password' => bcrypt('password123'),
        'fcm_token' => 'fake_ios_token_' . uniqid(),
        'platform' => 'ios',
    ],
    [
        'nom' => 'Test',
        'prenom' => 'User5',
        'email' => 'user5@test.com',
        'phone' => '+224600000005',
        'sexe' => 'F',
        'status' => true,
        'dob' => '1992-07-18',
        'password' => bcrypt('password123'),
        'fcm_token' => 'fake_android_token_' . uniqid(),
        'platform' => 'android',
    ],
];

$count = 0;
foreach ($users as $userData) {
    $user = Utilisateur::create($userData);
    $count++;
    echo "✅ Créé: {$user->prenom} {$user->nom} ({$user->platform})\n";
}

echo "\n✅ $count utilisateurs de test créés avec succès!\n";
echo "\n📋 Informations importantes:\n";
echo "- Email: user1@test.com à user5@test.com\n";
echo "- Mot de passe: password123\n";
echo "- Plateformes: 3 Android, 2 iOS\n";
echo "- Tokens FCM: Simulés (fake tokens)\n";
echo "\n⚠️  IMPORTANT: Ces utilisateurs ont des tokens FAKE.\n";
echo "   L'envoi de notifications échouera car les tokens ne sont pas réels.\n";
echo "   Mais vous pouvez voir le processus fonctionner dans l'admin!\n\n";
