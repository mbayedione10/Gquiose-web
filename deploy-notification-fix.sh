#!/bin/bash

# Script de déploiement du fix notifications push
# Résout le bug: "Filtered out=1" malgré player_id et préférences OK

echo "========================================"
echo "Déploiement Fix Notifications Push"
echo "========================================"
echo ""

# Couleurs
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m' # No Color

# Étape 1: Commit et push
echo -e "${YELLOW}Étape 1: Commit et push des changements${NC}"
git add app/Services/PushNotificationService.php
git commit -m "Fix: array vs object access in canSendToUser() - résout le bug 'Filtered out=1'"
git push origin main

if [ $? -ne 0 ]; then
    echo -e "${RED}❌ Échec du push Git${NC}"
    exit 1
fi

echo -e "${GREEN}✓ Changements pushés sur GitHub${NC}"
echo ""

# Étape 2: Instructions pour le serveur de production
echo -e "${YELLOW}Étape 2: Commandes à exécuter sur le serveur de production${NC}"
echo ""
echo "Connectez-vous au serveur et exécutez:"
echo ""
echo -e "${GREEN}cd ~/sites/test.gquiose.africa/Gquiose-web${NC}"
echo -e "${GREEN}git pull origin main${NC}"
echo -e "${GREEN}php artisan config:clear${NC}"
echo -e "${GREEN}php artisan cache:clear${NC}"
echo ""
echo "Puis testez:"
echo -e "${GREEN}php artisan notifications:test${NC}"
echo ""
echo "Vous devriez maintenant voir:"
echo "  - Envoyés: 1 (au lieu de 0)"
echo "  - Livrés: 1 (au lieu de 0)"
echo "  - ✓ Notification envoyée avec succès"
echo ""
echo -e "${YELLOW}Explication du bug:${NC}"
echo "  - getTargetedUsers() retourne une Collection Eloquent"
echo "  - ->toArray() convertit les modèles en tableaux associatifs"
echo "  - canSendToUser() utilisait \$user->property au lieu de \$user['property']"
echo "  - Résultat: tous les utilisateurs étaient filtrés"
echo ""
echo "========================================="
echo "Fin du script de déploiement"
echo "========================================="
