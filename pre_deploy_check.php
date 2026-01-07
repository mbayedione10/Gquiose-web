<?php
/**
 * Script de vérification avant déploiement
 * Exécutez ce script sur le serveur AVANT de lancer les migrations
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

echo "=== VÉRIFICATION PRÉ-DÉPLOIEMENT ===\n\n";

// 1. Vérifier la structure actuelle
echo "1. Vérification de la structure de la table utilisateurs...\n";
$hasAnnee = Schema::hasColumn('utilisateurs', 'anneedenaissance');
$hasDob = Schema::hasColumn('utilisateurs', 'dob');

echo "   - Champ 'anneedenaissance': " . ($hasAnnee ? "✅ Présent" : "❌ MANQUANT") . "\n";
echo "   - Champ 'dob': " . ($hasDob ? "✅ Présent" : "❌ MANQUANT") . "\n\n";

// 2. Compter les utilisateurs
$totalUsers = DB::table('utilisateurs')->count();
echo "2. Total utilisateurs: {$totalUsers}\n\n";

// 3. Analyser les données dob actuelles
if ($hasDob && $totalUsers > 0) {
    echo "3. Analyse des données 'dob' actuelles:\n";
    
    $dobStats = DB::table('utilisateurs')
        ->select('dob', DB::raw('COUNT(*) as count'))
        ->groupBy('dob')
        ->orderByDesc('count')
        ->limit(20)
        ->get();
    
    $needsNormalization = 0;
    $alreadyNormalized = 0;
    $nullValues = 0;
    
    $validRanges = ['-15 ans', '15-17 ans', '18-24 ans', '25-29 ans', '30-35 ans', '+35 ans'];
    
    foreach ($dobStats as $stat) {
        if ($stat->dob === null) {
            $nullValues = $stat->count;
            echo "   - NULL: {$stat->count} utilisateurs\n";
        } elseif (in_array($stat->dob, $validRanges)) {
            $alreadyNormalized += $stat->count;
            echo "   ✅ '{$stat->dob}': {$stat->count} utilisateurs (déjà normalisé)\n";
        } else {
            $needsNormalization += $stat->count;
            echo "   ⚠️  '{$stat->dob}': {$stat->count} utilisateurs (NÉCESSITE normalisation)\n";
        }
    }
    
    echo "\n   Résumé:\n";
    echo "   - Déjà normalisés: {$alreadyNormalized}\n";
    echo "   - À normaliser: {$needsNormalization}\n";
    echo "   - NULL: {$nullValues}\n\n";
}

// 4. Analyser les années de naissance
if ($hasAnnee && $totalUsers > 0) {
    echo "4. Analyse des années de naissance:\n";
    
    $withYear = DB::table('utilisateurs')->whereNotNull('anneedenaissance')->count();
    $withoutYear = $totalUsers - $withYear;
    
    echo "   - Avec année: {$withYear}\n";
    echo "   - Sans année: {$withoutYear}\n";
    
    if ($withYear > 0) {
        $yearRange = DB::table('utilisateurs')
            ->whereNotNull('anneedenaissance')
            ->selectRaw('MIN(anneedenaissance) as min_year, MAX(anneedenaissance) as max_year')
            ->first();
        
        echo "   - Plage: {$yearRange->min_year} - {$yearRange->max_year}\n";
    }
    echo "\n";
}

// 5. Recommandations
echo "=== RECOMMANDATIONS ===\n\n";

if (!$hasAnnee && !$hasDob) {
    echo "⚠️  ATTENTION: Les champs n'existent pas encore.\n";
    echo "   Action: Les migrations vont les créer.\n\n";
} elseif ($needsNormalization > 0) {
    echo "✅ La migration de normalisation va traiter {$needsNormalization} utilisateurs.\n\n";
}

echo "Commandes à exécuter sur le serveur:\n";
echo "1. php artisan migrate --pretend     # Prévisualiser\n";
echo "2. php artisan migrate                # Exécuter\n";
echo "3. php artisan optimize:clear         # Nettoyer le cache\n\n";

echo "✅ Vérification terminée.\n";
