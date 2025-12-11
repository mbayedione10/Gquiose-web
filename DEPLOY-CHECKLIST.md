# Checklist de Déploiement - Gquiose

## Prérequis
- ✅ Droplet Ubuntu 20.04/22.04
- ✅ Accès SSH root
- ✅ Nom de domaine configuré (ex: gquiose.mbayedione.xyz)

---

## Déploiement Complet - Guide Étape par Étape

### 1. Installer PHP 8.3 et Extensions

```bash
# Se connecter au serveur
ssh root@votre-droplet-ip

# Ajouter le repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Installer PHP 8.3 et extensions nécessaires
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

# Si PHP 8.2 est installé, l'arrêter
sudo systemctl stop php8.2-fpm 2>/dev/null || true
sudo systemctl disable php8.2-fpm 2>/dev/null || true

# Démarrer PHP 8.3
sudo systemctl start php8.3-fpm
sudo systemctl enable php8.3-fpm

# Définir PHP 8.3 par défaut
sudo update-alternatives --set php /usr/bin/php8.3

# Vérifier l'installation
php -v
# Doit afficher: PHP 8.3.x
```

### 2. Installer Nginx

```bash
# Installer Nginx
sudo apt install nginx -y

# Démarrer et activer Nginx
sudo systemctl start nginx
sudo systemctl enable nginx

# Vérifier le statut
sudo systemctl status nginx
```

### 3. Installer Composer

```bash
# Télécharger et installer Composer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php

# Vérifier
composer --version
```

### 4. Installer Node.js 20

```bash
# Installer Node.js 20 (nécessaire pour compiler les assets)
curl -fsSL https://deb.nodesource.com/setup_20.x | sudo -E bash -
sudo apt-get install -y nodejs

# Vérifier
node --version
npm --version
```

### 5. Cloner le Repository

```bash
# Créer le répertoire
sudo mkdir -p /var/www
cd /var/www

# Cloner le repository (remplacer par votre URL)
sudo git clone https://github.com/mbayedione10/Gquiose-web.git gquiose
cd gquiose

# Définir les permissions initiales
sudo chown -R www-data:www-data /var/www/gquiose
```

### 6. Installer les Dépendances PHP

```bash
cd /var/www/gquiose

# Installer les dépendances PHP (ignorer ext-http si manquant)
composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-http
```

### 7. Configurer l'Environnement (.env)

```bash
# Créer le fichier .env
cp .env.example .env
nano .env
```


### 8. Configurer la Base de Données SQLite

```bash
cd /var/www/gquiose

# Créer la base de données SQLite
mkdir -p database
touch database/database.sqlite

# Permissions pour SQLite
chmod 664 database/database.sqlite
chmod 775 database
chown -R www-data:www-data database

# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate:fresh --seed --force
```

### 9. Compiler les Assets Frontend

```bash
cd /var/www/gquiose

# Installer les dépendances npm
rm -rf node_modules package-lock.json
npm install

# Compiler les assets pour la production
npm run build

# Vérifier que le dossier build existe
ls -la public/build/

# Permissions
sudo chown -R www-data:www-data public/build
sudo chmod -R 755 public/build
```

### 10. Publier les Assets des Packages

```bash
cd /var/www/gquiose

# Publier les assets Livewire
php artisan livewire:publish --assets

# Copier les assets Filament manuellement
mkdir -p public/filament/assets
cp vendor/filament/filament/dist/* public/filament/assets/

# Permissions
sudo chown -R www-data:www-data public/filament
sudo chown -R www-data:www-data public/vendor
sudo chmod -R 755 public/filament
sudo chmod -R 755 public/vendor

# Créer le lien symbolique pour storage
php artisan storage:link
```

### 11. Optimiser Laravel

```bash
cd /var/www/gquiose

# Cacher les configurations
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions finales
sudo chown -R www-data:www-data /var/www/gquiose
sudo chmod -R 755 storage bootstrap/cache
sudo chmod -R 775 database
sudo chmod 664 database/database.sqlite
```

### 12. Configurer SSL avec Let's Encrypt

```bash
# Installer Certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtenir le certificat SSL (remplacer par votre domaine)
sudo certbot --nginx -d gquiose.mbayedione.xyz -d www.gquiose.mbayedione.xyz

# Le renouvellement automatique est configuré par défaut
# Tester le renouvellement
sudo certbot renew --dry-run
```

### 13. Configurer Nginx

```bash
# Créer la configuration
sudo nano /etc/nginx/sites-available/gquiose
```

**Contenu du fichier de configuration Nginx:**
```nginx
# Redirection HTTP vers HTTPS
server {
    listen 80;
    listen [::]:80;
    server_name gquiose.mbayedione.xyz www.gquiose.mbayedione.xyz;
    return 301 https://$server_name$request_uri;
}

# Configuration HTTPS
server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name gquiose.mbayedione.xyz www.gquiose.mbayedione.xyz;

    root /var/www/gquiose/public;
    index index.php index.html;

    # Certificats SSL
    ssl_certificate /etc/letsencrypt/live/gquiose.mbayedione.xyz/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/gquiose.mbayedione.xyz/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    # Headers de sécurité
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;

    # Gzip
    gzip on;
    gzip_types text/css application/javascript application/json image/svg+xml;
    gzip_comp_level 6;

    # Taille maximale des uploads
    client_max_body_size 50M;

    # Logs
    access_log /var/log/nginx/gquiose_access.log;
    error_log /var/log/nginx/gquiose_error.log;

    # Special handling for build assets (Vite)
    location /build/ {
        alias /var/www/gquiose/public/build/;
        expires max;
        access_log off;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Vendor assets (Livewire, etc) - fichiers statiques
    location /vendor/ {
        alias /var/www/gquiose/public/vendor/;
        expires max;
        access_log off;
        add_header Cache-Control "public, immutable";
        try_files $uri =404;
    }

    # Filament assets - doivent passer par Laravel (routes dynamiques)
    location /filament {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Livewire - doit passer par Laravel
    location /livewire {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP-FPM - DOIT être avant la location / générale
    location ~ \.php$ {
        include snippets/fastcgi-php.conf;
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_index index.php;
        fastcgi_buffers 16 16k;
        fastcgi_buffer_size 32k;
    }

    # Gestion générale des requêtes
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # Bloquer l'accès aux fichiers cachés
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Optimisations pour fichiers statiques communs
    location ~* \.(jpg|jpeg|png|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }
}
```

```bash
# Activer le site
sudo ln -s /etc/nginx/sites-available/gquiose /etc/nginx/sites-enabled/

# Supprimer le site par défaut si nécessaire
sudo rm /etc/nginx/sites-enabled/default

# Tester la configuration
sudo nginx -t

# Recharger Nginx
sudo systemctl reload nginx
sudo systemctl restart php8.3-fpm
```

### 14. Vérifier l'Installation

```bash
# Vérifier les services
sudo systemctl status nginx
sudo systemctl status php8.3-fpm

# Tester l'accès aux assets
curl -I https://gquiose.mbayedione.xyz/build/manifest.json
curl -I https://gquiose.mbayedione.xyz/filament/assets/app.css

# Voir les logs en temps réel
tail -f /var/log/nginx/gquiose_error.log
tail -f /var/www/gquiose/storage/logs/laravel.log
```

Ouvrir dans le navigateur:
- **URL:** https://gquiose.mbayedione.xyz
- **Admin:** admin@admin.com
- **Password:** password

---

## Script de Déploiement Rapide (Mises à jour futures)

Créer le fichier `deploy.sh` à la racine du projet:

```bash
#!/bin/bash

echo "🚀 Déploiement de Gquiose..."

# Activer le mode maintenance
php artisan down || true

# Mettre à jour le code
git pull origin main

# Installer les dépendances
composer install --no-dev --optimize-autoloader --ignore-platform-req=ext-http

# Installer et compiler les assets
npm install
npm run build

# Copier les assets Filament
mkdir -p public/filament/assets
cp vendor/filament/filament/dist/* public/filament/assets/ 2>/dev/null || true

# Publier les assets Livewire
php artisan livewire:publish --assets

# Exécuter les migrations
php artisan migrate --force

# Clear et recache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissions
sudo chown -R www-data:www-data public/build
sudo chown -R www-data:www-data public/filament
sudo chown -R www-data:www-data public/vendor
sudo chmod -R 755 public/build
sudo chmod -R 755 public/filament
sudo chmod -R 755 public/vendor
sudo chmod -R 775 database
sudo chmod 664 database/database.sqlite

# Désactiver le mode maintenance
php artisan up

echo "✅ Déploiement terminé!"
```

```bash
# Rendre le script exécutable
chmod +x deploy.sh

# Pour déployer les futures mises à jour:
cd /var/www/gquiose
sudo -u www-data ./deploy.sh
```

---

## Configuration du Queue Worker (Optionnel)

```bash
sudo nano /etc/systemd/system/gquiose-worker.service
```

Contenu:
```ini
[Unit]
Description=Gquiose Queue Worker
After=network.target

[Service]
User=www-data
Group=www-data
Restart=always
ExecStart=/usr/bin/php /var/www/gquiose/artisan queue:work --sleep=3 --tries=3 --max-time=3600
StandardOutput=append:/var/www/gquiose/storage/logs/worker.log
StandardError=append:/var/www/gquiose/storage/logs/worker.log

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable gquiose-worker
sudo systemctl start gquiose-worker
sudo systemctl status gquiose-worker
```

---

## Backups Automatiques

```bash
sudo nano /usr/local/bin/backup-gquiose.sh
```

Contenu:
```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups/gquiose"
DB_PATH="/var/www/gquiose/database/database.sqlite"

mkdir -p $BACKUP_DIR

# Backup SQLite
cp $DB_PATH $BACKUP_DIR/database_$DATE.sqlite
gzip $BACKUP_DIR/database_$DATE.sqlite

# Backup storage
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/gquiose/storage/app

# Garder 7 jours
find $BACKUP_DIR -type f -mtime +7 -delete

echo "✅ Backup: $DATE"
```

```bash
sudo chmod +x /usr/local/bin/backup-gquiose.sh

# Tester le backup
sudo /usr/local/bin/backup-gquiose.sh

# Ajouter au crontab (backup quotidien à 2h du matin)
sudo crontab -e
# Ajouter: 0 2 * * * /usr/local/bin/backup-gquiose.sh >> /var/log/gquiose-backup.log 2>&1
```

---

## Commandes Utiles

### Logs
```bash
# Laravel
tail -f /var/www/gquiose/storage/logs/laravel.log

# Nginx
tail -f /var/log/nginx/gquiose_error.log
tail -f /var/log/nginx/gquiose_access.log

# PHP-FPM
tail -f /var/log/php8.3-fpm.log
```

### Redémarrer les services
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
sudo systemctl restart gquiose-worker
```

### Vérifier l'état
```bash
sudo systemctl status php8.3-fpm
sudo systemctl status nginx
sudo systemctl status gquiose-worker
```

### Clear cache Laravel
```bash
cd /var/www/gquiose
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Dépannage

### Problème: 502 Bad Gateway
```bash
# Vérifier PHP-FPM
sudo systemctl status php8.3-fpm
sudo systemctl restart php8.3-fpm

# Vérifier les logs
tail -f /var/log/nginx/gquiose_error.log

# Vérifier le socket PHP
ls -la /var/run/php/php8.3-fpm.sock
```

### Problème: 404 sur les assets CSS/JS
```bash
# Vérifier que les assets sont compilés
ls -la /var/www/gquiose/public/build/

# Si vide, recompiler
cd /var/www/gquiose
npm install
npm run build

# Vérifier les assets Filament
ls -la /var/www/gquiose/public/filament/assets/

# Si vide, copier manuellement
mkdir -p public/filament/assets
cp vendor/filament/filament/dist/* public/filament/assets/

# Clear cache et recharger
php artisan config:clear
php artisan view:clear
sudo systemctl reload nginx
```

### Problème: Permission denied
```bash
sudo chown -R www-data:www-data /var/www/gquiose
sudo chmod -R 755 storage bootstrap/cache
sudo chmod -R 775 database
sudo chmod 664 database/database.sqlite
```

### Problème: Database locked (SQLite)
```bash
# Vérifier les processus utilisant la DB
sudo lsof /var/www/gquiose/database/database.sqlite

# Redémarrer PHP-FPM
sudo systemctl restart php8.3-fpm

# Vérifier les permissions
sudo chmod 664 /var/www/gquiose/database/database.sqlite
sudo chmod 775 /var/www/gquiose/database
sudo chown -R www-data:www-data /var/www/gquiose/database
```

---

## Checklist Post-Déploiement

- [ ] Site accessible via HTTPS
- [ ] Certificat SSL valide
- [ ] Page de login s'affiche correctement avec le style
- [ ] Connexion admin fonctionne
- [ ] Tableau de bord s'affiche (sans doublon)
- [ ] Tous les menus sont accessibles
- [ ] Les images/assets se chargent
- [ ] Changer le mot de passe admin
- [ ] Tester l'envoi d'emails
- [ ] Tester les uploads de fichiers
- [ ] Vérifier les notifications push (si configurées)
- [ ] Configurer les backups automatiques
- [ ] Documenter les accès pour l'équipe

---

## Notes Importantes

1. **SQLite vs MySQL:** Cette installation utilise SQLite. Pour une charge importante, considérez MySQL/PostgreSQL.

2. **Assets Filament:** Les assets Filament v2 doivent être copiés manuellement dans `public/filament/assets/`. C'est une spécificité de Filament v2.

3. **Vite Build:** Les assets frontend doivent être compilés avec `npm run build` sur le serveur de production.

4. **Node.js:** Node.js est nécessaire uniquement pour compiler les assets. Une fois compilés, Node.js n'est plus requis pour faire tourner l'application.

5. **Permissions:** SQLite nécessite des permissions spécifiques sur le fichier de base de données ET le dossier parent.

6. **Mises à jour:** Utilisez le script `deploy.sh` pour les mises à jour futures. Ne jamais oublier de recompiler les assets après un `git pull`.
