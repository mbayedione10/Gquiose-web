<?php
/**
 * Script de diagnostic pour vérifier l'état d'un utilisateur
 * Usage: php diagnose-user.php <user_id|email>
 */

require __DIR__.'/vendor/autoload.php';

$app = require_once __DIR__.'/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Utilisateur;
use App\Models\Code;

// Récupérer l'identifiant depuis les arguments
$identifier = $argv[1] ?? null;

if (!$identifier) {
    echo "❌ Usage: php diagnose-user.php <user_id|email>\n";
    echo "   Exemple: php diagnose-user.php 2\n";
    echo "   Exemple: php diagnose-user.php user@example.com\n";
    exit(1);
}

// Rechercher l'utilisateur
$user = is_numeric($identifier) 
    ? Utilisateur::find($identifier)
    : Utilisateur::where('email', $identifier)->first();

if (!$user) {
    echo "❌ Utilisateur introuvable: {$identifier}\n";
    exit(1);
}

echo "\n";
echo "═══════════════════════════════════════════════════════════\n";
echo "   DIAGNOSTIC UTILISATEUR\n";
echo "═══════════════════════════════════════════════════════════\n\n";

echo "📋 INFORMATIONS GÉNÉRALES\n";
echo "   ID              : {$user->id}\n";
echo "   Email           : " . ($user->email ?: 'N/A') . "\n";
echo "   Téléphone       : " . ($user->phone ?: 'N/A') . "\n";
echo "   Nom complet     : {$user->prenom} {$user->nom}\n";
echo "   Compte actif    : " . ($user->statut ? '✅ OUI' : '❌ NON') . "\n";
echo "   Email vérifié   : " . ($user->email_verified_at ? '✅ OUI (' . $user->email_verified_at . ')' : '❌ NON') . "\n";
echo "   Créé le         : {$user->created_at}\n";
echo "\n";

echo "🔐 AUTHENTIFICATION\n";
echo "   Mot de passe    : " . (strlen($user->password) > 0 ? '✅ DÉFINI' : '❌ VIDE') . "\n";
echo "   Hash longueur   : " . strlen($user->password) . " caractères\n";
echo "   Tokens actifs   : " . $user->tokens()->count() . "\n";
echo "\n";

// Vérifier les codes en attente
$activeCodes = Code::where('utilisateur_id', $user->id)
    ->where('created_at', '>=', now()->subMinutes(10))
    ->get();

echo "📨 CODES DE VÉRIFICATION (< 10 min)\n";
if ($activeCodes->isEmpty()) {
    echo "   ✅ Aucun code en attente\n";
} else {
    foreach ($activeCodes as $code) {
        $type = $code->email ? 'Email' : 'SMS';
        $dest = $code->email ?: $code->phone;
        $age = now()->diffInMinutes($code->created_at);
        echo "   🔑 Code: {$code->code} | Type: {$type} | Dest: {$dest} | Âge: {$age} min\n";
    }
}

// Codes expirés
$expiredCodes = Code::where('utilisateur_id', $user->id)
    ->where('created_at', '<', now()->subMinutes(10))
    ->count();

if ($expiredCodes > 0) {
    echo "   ⏰ Codes expirés: {$expiredCodes}\n";
}
echo "\n";

echo "📱 ONESIGNAL\n";
echo "   Player ID       : " . ($user->onesignal_player_id ?: '❌ Non enregistré') . "\n";
echo "   Plateforme      : " . ($user->platform ?: 'N/A') . "\n";
echo "\n";

echo "═══════════════════════════════════════════════════════════\n";
echo "   TESTS DE CONNEXION\n";
echo "═══════════════════════════════════════════════════════════\n\n";

// Tester le hash du mot de passe
$testPassword = 'test123456';
$hashCheck = \Hash::check($testPassword, $user->password);
echo "Test mot de passe 'test123456': " . ($hashCheck ? '✅ MATCH' : '❌ NO MATCH') . "\n";
echo "\n";

echo "💡 DIAGNOSTIC:\n";
if (strlen($user->password) === 0) {
    echo "   ⚠️  L'utilisateur n'a PAS de mot de passe défini\n";
    echo "   → Action: L'utilisateur doit finaliser l'inscription ou faire un reset\n";
} elseif (!$activeCodes->isEmpty()) {
    echo "   ⚠️  Des codes de vérification sont en attente\n";
    echo "   → Action: L'utilisateur doit saisir le code pour finaliser le reset\n";
} elseif (!$user->email_verified_at) {
    echo "   ⚠️  Email non vérifié\n";
    echo "   → Action: L'utilisateur doit vérifier son email\n";
} else {
    echo "   ✅ Le compte semble OK\n";
    echo "   → Si connexion échoue: vérifier que le mot de passe saisi est correct\n";
}
echo "\n";

echo "═══════════════════════════════════════════════════════════\n\n";
