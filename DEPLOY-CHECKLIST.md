p# Checklist de Déploiement - Gquiose

## État actuel du serveur
- ✅ SSL déjà configuré avec Let's Encrypt
- ✅ Nginx installé
- ⚠️ PHP 8.2 (doit être mis à jour vers 8.3)
- ⚠️ Application à configurer

---

## Étapes à suivre sur le serveur

### 1. Mettre à jour vers PHP 8.3 ⏳

```bash
# Se connecter au serveur
ssh root@votre-ip-droplet

# Ajouter le repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Installer PHP 8.3 et extensions
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

# Arrêter PHP 8.2
sudo systemctl stop php8.2-fpm
sudo systemctl disable php8.2-fpm

# Démarrer PHP 8.3
sudo systemctl start php8.3-fpm
sudo systemctl enable php8.3-fpm

# Changer la version par défaut
sudo update-alternatives --set php /usr/bin/php8.3

# Vérifier
php -v
# Doit afficher: PHP 8.3.x
```

### 2. Mettre à jour la configuration Nginx ⏳

```bash
# Éditer la configuration
sudo nano /etc/nginx/sites-available/gquiose
```

Remplacer le contenu par celui du fichier `nginx-gquiose.conf` (corrigé).

```bash
# Tester la configuration
sudo nginx -t

# Si OK, redémarrer
sudo systemctl restart nginx
sudo systemctl restart php8.3-fpm
```

### 3. Installer Composer (si pas déjà installé) ⏳

```bash
# Vérifier si Composer est installé
composer --version

# Si pas installé:
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
rm composer-setup.php
composer --version
```

### 4. Configurer l'application ⏳

```bash
# Aller dans le répertoire
cd /var/www/gquiose

# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Créer le fichier .env
cp .env.example .env
nano .env
```

**Configuration .env à utiliser:**
```env
APP_NAME="G Qui Ose"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://gquiose.mbayedione.xyz

LOG_CHANNEL=stack
LOG_LEVEL=error

DB_CONNECTION=sqlite
DB_DATABASE=/var/www/gquiose/database/database.sqlite

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_DRIVER=file

MAIL_MAILER=smtp
MAIL_HOST=mail.infomaniak.com
MAIL_PORT=587
MAIL_USERNAME=contact@gquiose.africa
MAIL_PASSWORD=Conakry2020@
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="contact@gquiose.africa"
MAIL_FROM_NAME="${APP_NAME}"

SMS_PROVIDER=twilio
TWILIO_SID=
TWILIO_TOKEN=
TWILIO_FROM=

FCM_SERVER_KEY=
APNS_KEY_ID=
APNS_TEAM_ID=
APNS_BUNDLE_ID=com.gquiose.app
APNS_ENVIRONMENT=production
```

```bash
# Générer la clé d'application
php artisan key:generate

# Créer et configurer la base SQLite
mkdir -p database
touch database/database.sqlite
chmod 664 database/database.sqlite
chown www-data:www-data database/database.sqlite
chmod 775 database
chown www-data:www-data database

# Exécuter les migrations
php artisan migrate:fresh --seed --force

# Optimiser l'application
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan storage:link

# Permissions finales
chown -R www-data:www-data /var/www/gquiose
chmod -R 755 storage bootstrap/cache
chmod -R 775 database
chmod 664 database/database.sqlite
```

### 5. Tester l'application ⏳

```bash
# Vérifier les logs
tail -f storage/logs/laravel.log
```

Ouvrir dans le navigateur:
- https://gquiose.mbayedione.xyz

Connexion admin:
- Email: `admin@admin.com`
- Password: `password`

### 6. Configuration du Queue Worker (Optionnel) ⏳

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

### 7. Configurer les backups automatiques ⏳

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

# Ajouter au crontab
sudo crontab -e
# Ajouter: 0 2 * * * /usr/local/bin/backup-gquiose.sh >> /var/log/gquiose-backup.log 2>&1
```

---

## Commandes utiles

### Voir les logs
```bash
# Laravel
tail -f /var/www/gquiose/storage/logs/laravel.log

# Nginx
tail -f /var/log/nginx/error.log
tail -f /var/log/nginx/access.log

# PHP-FPM
tail -f /var/log/php8.3-fpm.log
```

### Redémarrer les services
```bash
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
sudo systemctl restart gquiose-worker
```

### Vérifier l'état des services
```bash
sudo systemctl status php8.3-fpm
sudo systemctl status nginx
sudo systemctl status gquiose-worker
```

### Déploiement rapide (après configuration initiale)
```bash
cd /var/www/gquiose
sudo -u www-data ./deploy.sh
```

---

## Dépannage

### 502 Bad Gateway
```bash
# Vérifier PHP-FPM
sudo systemctl status php8.3-fpm
sudo systemctl restart php8.3-fpm

# Vérifier les logs
tail -f /var/log/nginx/error.log
```

### Permission denied
```bash
sudo chown -R www-data:www-data /var/www/gquiose
sudo chmod -R 755 storage bootstrap/cache
sudo chmod -R 775 database
sudo chmod 664 database/database.sqlite
```

### Database locked (SQLite)
```bash
# Vérifier les processus
sudo lsof /var/www/gquiose/database/database.sqlite

# Redémarrer PHP-FPM
sudo systemctl restart php8.3-fpm
```

### Clear cache
```bash
cd /var/www/gquiose
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

---

## Prochaines étapes après déploiement

1. ✅ Changer le mot de passe admin
2. ✅ Tester toutes les fonctionnalités
3. ✅ Vérifier les emails (SMTP)
4. ✅ Tester les notifications push
5. ✅ Vérifier les uploads de fichiers
6. ✅ Configurer le monitoring (optionnel)
7. ✅ Documenter les accès pour l'équipe
