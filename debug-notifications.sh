#!/bin/bash

# Script de diagnostic des notifications push (Production-ready)
# Usage: ./debug-notifications.sh

echo "======================================"
echo "Diagnostic Notifications Push"
echo "======================================"
echo ""

# Utiliser les commandes artisan au lieu de tinker (compatible production)
php artisan notifications:diagnose-system

echo ""
echo "======================================"
echo "Commandes disponibles:"
echo "======================================"
echo ""
echo "1. Diagnostic complet:"
echo "   php artisan notifications:diagnose-system"
echo ""
echo "2. Créer préférences par défaut:"
echo "   php artisan notifications:create-default-preferences"
echo ""
echo "3. Test d'envoi:"
echo "   php artisan notifications:test"
echo "   php artisan notifications:test 123  # Tester avec user ID 123"
echo ""
echo "4. Voir les logs:"
echo "   tail -f storage/logs/laravel.log | grep -i notification"
echo ""
