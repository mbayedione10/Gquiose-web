#!/bin/bash

# Script de diagnostic des notifications push
# Usage: ./debug-notifications.sh

echo "======================================"
echo "Diagnostic Notifications Push"
echo "======================================"
echo ""

# Vérifier les utilisateurs avec OneSignal player_id
echo "1. Utilisateurs avec OneSignal player_id:"
php artisan tinker --execute="
\$count = \App\Models\Utilisateur::whereNotNull('onesignal_player_id')->where('status', true)->count();
echo \"Total: \$count utilisateurs\n\";
\$sample = \App\Models\Utilisateur::whereNotNull('onesignal_player_id')->where('status', true)->take(3)->get(['id', 'nom', 'prenom', 'onesignal_player_id']);
foreach (\$sample as \$user) {
    echo \"- User #{\$user->id}: {\$user->nom} {\$user->prenom} - Player ID: {\$user->onesignal_player_id}\n\";
}
"

echo ""
echo "2. Vérifier les préférences de notification:"
php artisan tinker --execute="
\$users = \App\Models\Utilisateur::whereNotNull('onesignal_player_id')->where('status', true)->take(5)->get();
foreach (\$users as \$user) {
    \$prefs = \$user->notificationPreferences;
    if (\$prefs) {
        \$enabled = \$prefs->notifications_enabled ? 'OUI' : 'NON';
        \$dnd = \$prefs->do_not_disturb ? 'OUI' : 'NON';
        echo \"User #{\$user->id}: Notifs=\$enabled, DND=\$dnd\n\";
    } else {
        echo \"User #{\$user->id}: AUCUNE PRÉFÉRENCE (par défaut = activé)\n\";
    }
}
"

echo ""
echo "3. Dernières notifications envoyées:"
php artisan tinker --execute="
\$notifications = \App\Models\PushNotification::orderBy('created_at', 'desc')->take(5)->get(['id', 'title', 'status', 'sent_count', 'target_audience', 'created_at']);
foreach (\$notifications as \$notif) {
    echo \"#{\$notif->id}: {\$notif->title} - Status: {\$notif->status} - Envoyés: {\$notif->sent_count} - Audience: {\$notif->target_audience} - Créé: {\$notif->created_at}\n\";
}
"

echo ""
echo "4. Vérifier la configuration OneSignal:"
php artisan tinker --execute="
\$appId = config('onesignal.app_id');
\$apiKey = config('onesignal.rest_api_key');
echo \"App ID: \" . (\$appId ? 'Configuré (' . substr(\$appId, 0, 10) . '...)' : 'NON CONFIGURÉ') . \"\n\";
echo \"API Key: \" . (\$apiKey ? 'Configuré (' . substr(\$apiKey, 0, 10) . '...)' : 'NON CONFIGURÉ') . \"\n\";
"

echo ""
echo "5. Test d'envoi à un utilisateur:"
echo "Pour tester, exécutez dans tinker:"
echo "php artisan tinker"
echo "Puis:"
echo "\$user = App\Models\Utilisateur::whereNotNull('onesignal_player_id')->first();"
echo "\$notif = App\Models\PushNotification::create(['title' => 'Test', 'message' => 'Test message', 'type' => 'manual', 'target_audience' => 'all', 'status' => 'pending']);"
echo "\$service = app(App\Services\PushNotificationService::class);"
echo "\$service->sendNotification(\$notif);"
echo "\$notif->refresh();"
echo "echo \"Envoyés: {\$notif->sent_count}\";"

echo ""
echo "======================================"
echo "Fin du diagnostic"
echo "======================================"
