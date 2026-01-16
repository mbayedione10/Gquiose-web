#!/bin/bash

# Script de déploiement des outils de diagnostic des notifications push
# Serveur: 83.166.133.68
# Utilisateur: 7550n6_root
# Path: sites/test.gquiose.africa/Gquiose-web/

echo "🚀 Déploiement des outils de diagnostic des notifications push"
echo ""

# Couleurs pour les messages
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Vérifier si nous sommes dans le bon répertoire
if [ ! -f "artisan" ]; then
    echo -e "${RED}❌ Erreur: Vous devez être dans le répertoire du projet Laravel${NC}"
    exit 1
fi

echo -e "${YELLOW}📁 Répertoire actuel: $(pwd)${NC}"
echo ""

# Étape 1: Copier les nouveaux fichiers
echo -e "${GREEN}📋 Étape 1: Copie des nouveaux fichiers de diagnostic${NC}"

# Vérifier que les fichiers existent
if [ ! -f "app/Console/Commands/DiagnoseUserNotifications.php" ]; then
    echo -e "${RED}❌ Fichier manquant: app/Console/Commands/DiagnoseUserNotifications.php${NC}"
    exit 1
fi

if [ ! -f "app/Console/Commands/SendTestNotification.php" ]; then
    echo -e "${RED}❌ Fichier manquant: app/Console/Commands/SendTestNotification.php${NC}"
    exit 1
fi

echo -e "${GREEN}✅ Tous les fichiers requis sont présents${NC}"
echo ""

# Étape 2: Vider les caches Laravel
echo -e "${GREEN}🗑️  Étape 2: Vidage des caches${NC}"
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
echo -e "${GREEN}✅ Caches vidés${NC}"
echo ""

# Étape 3: Recharger les autoloaders
echo -e "${GREEN}🔄 Étape 3: Rechargement des autoloaders${NC}"
composer dump-autoload
echo -e "${GREEN}✅ Autoloaders rechargés${NC}"
echo ""

# Étape 4: Vérifier que les commandes sont disponibles
echo -e "${GREEN}🔍 Étape 4: Vérification des commandes${NC}"
if php artisan list | grep -q "notifications:diagnose"; then
    echo -e "${GREEN}✅ Commande 'notifications:diagnose' disponible${NC}"
else
    echo -e "${RED}❌ Commande 'notifications:diagnose' non trouvée${NC}"
fi

if php artisan list | grep -q "notifications:test"; then
    echo -e "${GREEN}✅ Commande 'notifications:test' disponible${NC}"
else
    echo -e "${RED}❌ Commande 'notifications:test' non trouvée${NC}"
fi
echo ""

# Étape 5: Diagnostic de l'utilisateur 1042
echo -e "${YELLOW}🔍 Prêt pour le diagnostic de l'utilisateur 1042${NC}"
echo ""
echo "Pour diagnostiquer l'utilisateur 1042, exécutez:"
echo -e "${GREEN}php artisan notifications:diagnose 1042${NC}"
echo ""
echo "Pour envoyer un test de notification:"
echo -e "${GREEN}php artisan notifications:test 1042 --bypass-preferences${NC}"
echo ""
echo "Pour voir les logs en temps réel:"
echo -e "${GREEN}tail -f storage/logs/laravel.log | grep -E '1042|OneSignal|notification'${NC}"
echo ""

echo -e "${GREEN}✅ Déploiement terminé avec succès!${NC}"
