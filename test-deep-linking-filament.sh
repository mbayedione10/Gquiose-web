#!/bin/bash

# Script de test des fonctionnalités Deep Linking dans Filament
# Usage: ./test-deep-linking-filament.sh

echo "======================================"
echo "Test des fonctionnalités Deep Linking"
echo "======================================"
echo ""

# Couleurs
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# 1. Vérifier que la migration existe
echo -e "${YELLOW}[1/6]${NC} Vérification de la migration..."
if [ -f "database/migrations/2026_01_16_000001_add_deep_linking_fields_to_notification_templates_table.php" ]; then
    echo -e "${GREEN}✓${NC} Migration trouvée"
else
    echo -e "${RED}✗${NC} Migration manquante"
    exit 1
fi

# 2. Vérifier le modèle NotificationTemplate
echo -e "${YELLOW}[2/6]${NC} Vérification du modèle NotificationTemplate..."
if grep -q "related_type" app/Models/NotificationTemplate.php && grep -q "related_id" app/Models/NotificationTemplate.php; then
    echo -e "${GREEN}✓${NC} Modèle mis à jour avec related_type et related_id"
else
    echo -e "${RED}✗${NC} Modèle non mis à jour"
    exit 1
fi

# 3. Vérifier la ressource NotificationTemplateResource
echo -e "${YELLOW}[3/6]${NC} Vérification de NotificationTemplateResource..."
if grep -q "Deep Linking" app/Filament/Resources/NotificationTemplateResource.php; then
    echo -e "${GREEN}✓${NC} Section Deep Linking ajoutée"
else
    echo -e "${RED}✗${NC} Section Deep Linking manquante"
    exit 1
fi

# 4. Vérifier la ressource PushNotificationResource
echo -e "${YELLOW}[4/6]${NC} Vérification de PushNotificationResource..."
if grep -q "Deep Linking OneSignal" app/Filament/Resources/PushNotificationResource.php; then
    echo -e "${GREEN}✓${NC} Section Deep Linking OneSignal ajoutée"
else
    echo -e "${RED}✗${NC} Section Deep Linking OneSignal manquante"
    exit 1
fi

# 5. Vérifier l'aperçu du payload
echo -e "${YELLOW}[5/6]${NC} Vérification de l'aperçu du payload..."
if grep -q "deep_linking_preview" app/Filament/Resources/PushNotificationResource.php; then
    echo -e "${GREEN}✓${NC} Aperçu du payload ajouté"
else
    echo -e "${RED}✗${NC} Aperçu du payload manquant"
    exit 1
fi

# 6. Vérifier les guides
echo -e "${YELLOW}[6/6]${NC} Vérification des guides..."
if [ -f "GUIDE_ADMIN_DEEP_LINKING.md" ] && [ -f "CHANGELOG_DEEP_LINKING_FILAMENT.md" ]; then
    echo -e "${GREEN}✓${NC} Guides créés"
else
    echo -e "${RED}✗${NC} Guides manquants"
    exit 1
fi

echo ""
echo -e "${GREEN}======================================"
echo "Tous les tests sont passés ✓"
echo "======================================${NC}"
echo ""
echo "Prochaines étapes:"
echo "1. Exécuter la migration: php artisan migrate"
echo "2. Tester dans l'admin Filament: php artisan serve"
echo "3. Créer un template de test avec deep linking"
echo "4. Créer une notification de test"
echo "5. Vérifier l'aperçu du payload OneSignal"
echo ""
