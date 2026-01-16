#!/bin/bash

# Script de vérification du statut OneSignal d'un utilisateur
# Usage: ./check-onesignal-player.sh <user_id>

USER_ID=${1:-1042}

echo "🔍 Vérification du Player ID OneSignal pour l'utilisateur #${USER_ID}"
echo ""

# Récupérer les informations de l'utilisateur
php artisan tinker --execute="
\$user = App\Models\Utilisateur::find(${USER_ID});
if (!\$user) {
    echo '❌ Utilisateur non trouvé';
    exit;
}

echo '👤 Utilisateur: ' . \$user->name . PHP_EOL;
echo '📱 Player ID: ' . (\$user->onesignal_player_id ?? 'NON DEFINI') . PHP_EOL;
echo '📲 Platform: ' . (\$user->platform ?? 'NON DEFINI') . PHP_EOL;
echo '' . PHP_EOL;

if (!\$user->onesignal_player_id) {
    echo '❌ Aucun Player ID enregistré' . PHP_EOL;
    exit;
}

// Vérifier le statut sur OneSignal via leur API
\$appId = config('onesignal.app_id');
\$apiKey = config('onesignal.rest_api_key');
\$playerId = \$user->onesignal_player_id;

echo '🌐 Vérification sur OneSignal...' . PHP_EOL;

\$client = new \GuzzleHttp\Client();
try {
    \$response = \$client->request('GET', \"https://onesignal.com/api/v1/players/{\$playerId}?app_id={\$appId}\", [
        'headers' => [
            'Authorization' => 'Basic ' . \$apiKey,
        ],
    ]);
    
    \$data = json_decode(\$response->getBody(), true);
    
    echo '📊 Informations OneSignal:' . PHP_EOL;
    echo '   ID: ' . (\$data['id'] ?? 'N/A') . PHP_EOL;
    echo '   Device Type: ' . (\$data['device_type'] ?? 'N/A') . ' (' . (\$data['device_model'] ?? 'N/A') . ')' . PHP_EOL;
    echo '   SDK Version: ' . (\$data['sdk'] ?? 'N/A') . PHP_EOL;
    echo '   App Version: ' . (\$data['game_version'] ?? 'N/A') . PHP_EOL;
    echo '   Invalid Player: ' . (isset(\$data['invalid_identifier']) && \$data['invalid_identifier'] ? '❌ OUI' : '✅ NON') . PHP_EOL;
    echo '   Session Count: ' . (\$data['session_count'] ?? 0) . PHP_EOL;
    echo '   Last Active: ' . (\$data['last_active'] ?? 'Jamais') . PHP_EOL;
    echo '   Created At: ' . (\$data['created_at'] ?? 'N/A') . PHP_EOL;
    echo '' . PHP_EOL;
    
    // Vérifier si le player est toujours valide
    if (isset(\$data['invalid_identifier']) && \$data['invalid_identifier']) {
        echo '❌ Ce Player ID est INVALIDE sur OneSignal' . PHP_EOL;
        echo '   Raison: L\'utilisateur a probablement désinstallé l\'app ou désactivé les notifications' . PHP_EOL;
        echo '' . PHP_EOL;
        echo '💡 Solution: Demander à l\'utilisateur de:' . PHP_EOL;
        echo '   1. Désinstaller et réinstaller l\'application' . PHP_EOL;
        echo '   2. Ou activer les notifications dans Paramètres → Apps → Gquiose' . PHP_EOL;
    } else {
        echo '✅ Player ID valide sur OneSignal' . PHP_EOL;
        
        // Vérifier la dernière activité
        if (isset(\$data['last_active'])) {
            \$lastActive = \$data['last_active'];
            \$lastActiveTime = strtotime(\$lastActive);
            \$daysSinceActive = floor((time() - \$lastActiveTime) / 86400);
            
            echo \"   Dernière activité: il y a {\$daysSinceActive} jour(s)\" . PHP_EOL;
            
            if (\$daysSinceActive > 30) {
                echo '   ⚠️  L\'utilisateur n\'a pas ouvert l\'app depuis plus de 30 jours' . PHP_EOL;
            }
        }
    }
    
} catch (\Exception \$e) {
    echo '❌ Erreur lors de la vérification: ' . \$e->getMessage() . PHP_EOL;
    if (str_contains(\$e->getMessage(), '404')) {
        echo '   → Ce Player ID n\'existe plus sur OneSignal' . PHP_EOL;
        echo '   → L\'utilisateur doit se reconnecter à l\'application' . PHP_EOL;
    }
}
"

echo ""
echo "✅ Vérification terminée"
