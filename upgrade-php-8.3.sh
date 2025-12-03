#!/bin/bash

# Script de mise à jour PHP 8.2 vers 8.3 sur Ubuntu
# À exécuter sur le serveur DigitalOcean

set -e

echo "🔄 Mise à jour de PHP 8.2 vers PHP 8.3..."

# Ajouter le repository Ondrej PHP
echo "📦 Ajout du repository PHP..."
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Installer PHP 8.3 et toutes les extensions nécessaires
echo "📥 Installation de PHP 8.3 et extensions..."
sudo apt install -y \
    php8.3 \
    php8.3-fpm \
    php8.3-cli \
    php8.3-common \
    php8.3-sqlite3 \
    php8.3-xml \
    php8.3-curl \
    php8.3-gd \
    php8.3-mbstring \
    php8.3-zip \
    php8.3-bcmath \
    php8.3-intl \
    php8.3-readline

# Arrêter PHP 8.2 FPM
echo "⏸️  Arrêt de PHP 8.2..."
sudo systemctl stop php8.2-fpm
sudo systemctl disable php8.2-fpm

# Démarrer PHP 8.3 FPM
echo "▶️  Démarrage de PHP 8.3..."
sudo systemctl start php8.3-fpm
sudo systemctl enable php8.3-fpm

# Mettre à jour les alternatives (CLI)
echo "🔧 Configuration de PHP 8.3 comme version par défaut..."
sudo update-alternatives --set php /usr/bin/php8.3

# Copier la configuration PHP 8.2 vers 8.3 si nécessaire
if [ -f /etc/php/8.2/fpm/php.ini ]; then
    echo "📋 Copie de la configuration PHP..."
    sudo cp /etc/php/8.2/fpm/php.ini /etc/php/8.3/fpm/php.ini.backup
fi

# Redémarrer Nginx
echo "🔄 Redémarrage de Nginx..."
sudo systemctl restart nginx

# Vérifier la version
echo "✅ Vérification de l'installation..."
php -v

echo ""
echo "✅ Mise à jour terminée avec succès!"
echo "PHP 8.3 est maintenant actif"
