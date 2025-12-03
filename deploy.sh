#!/bin/bash

# Script de déploiement pour Gquiose (SQLite)
# Usage: ./deploy.sh

set -e

echo "🚀 Début du déploiement..."

# Pull latest changes
echo "📥 Récupération des dernières modifications..."
git pull origin main

# Install/Update Dependencies
echo "📦 Installation des dépendances..."
composer install --no-dev --optimize-autoloader

# Clear and cache config
echo "🔧 Configuration de l'application..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Run migrations
echo "📊 Exécution des migrations..."
php artisan migrate --force

# Optimize
echo "⚡ Optimisation de l'application..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Storage link
echo "🔗 Création du lien symbolique storage..."
php artisan storage:link || true

# Set permissions (including SQLite database)
echo "🔐 Configuration des permissions..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 database
chown -R www-data:www-data database
chmod 664 database/database.sqlite

# Restart queue worker if exists
echo "🔄 Redémarrage du queue worker..."
systemctl restart gquiose-worker 2>/dev/null || echo "⚠️  Queue worker non configuré"

echo "✅ Déploiement terminé avec succès!"
