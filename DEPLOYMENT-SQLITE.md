# Déploiement avec SQLite - Gquiose sur DigitalOcean

## Avantages de SQLite en production

✅ **Pas de serveur MySQL à configurer**
✅ **Fichier unique facile à sauvegarder**
✅ **Performances excellentes pour applications moyennes**
✅ **Configuration simplifiée**

⚠️ **Limites** : Convient pour jusqu'à ~100,000 requêtes/jour. Pour plus, utilisez MySQL/PostgreSQL.

---

## Déploiement Complet avec SQLite

### 1. Connexion au Droplet

```bash
ssh root@votre-ip-droplet
```

### 2. Mise à jour vers PHP 8.3

```bash
# Ajouter le repository PHP
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Installer PHP 8.3 avec SQLite
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-sqlite3 \
    php8.3-xml php8.3-curl php8.3-gd php8.3-mbstring \
    php8.3-zip php8.3-bcmath php8.3-intl php8.3-readline

# Vérifier l'installation
php -v
php -m | grep sqlite
```

### 3. Installer Composer

```bash
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer --version
```

### 4. Installer et configurer Nginx

```bash
sudo apt install nginx -y

# Créer la configuration du site
sudo nano /etc/nginx/sites-available/gquiose
```

Coller cette configuration :
```nginx
server {
    listen 80;
    listen [::]:80;
    server_name gquiose.mbayedione.xyz www.gquiose.mbayedione.xyz;
    root /var/www/gquiose/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.3-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }
}
```

Activer le site :
```bash
sudo ln -s /etc/nginx/sites-available/gquiose /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl restart nginx
```

### 5. Cloner le repository

```bash
cd /var/www

# Cloner votre repository
sudo git clone https://github.com/votre-username/gquiose-web.git gquiose
cd gquiose

# Permissions initiales
sudo chown -R www-data:www-data /var/www/gquiose
```

### 6. Créer la base de données SQLite

```bash
cd /var/www/gquiose

# Créer le répertoire database s'il n'existe pas
sudo mkdir -p database

# Créer le fichier SQLite
sudo touch database/database.sqlite

# Permissions critiques pour SQLite
sudo chown -R www-data:www-data database
sudo chmod -R 775 database
sudo chmod 664 database/database.sqlite
```

### 7. Installer les dépendances

```bash
cd /var/www/gquiose
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### 8. Configurer l'environnement

```bash
sudo cp .env.example .env
sudo nano .env
```

Configuration `.env` pour production avec SQLite :
```env
APP_NAME="G Qui Ose"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://gquiose.mbayedione.xyz

LOG_CHANNEL=stack
LOG_LEVEL=error

# Configuration SQLite
DB_CONNECTION=sqlite
DB_DATABASE=/var/www/gquiose/database/database.sqlite

BROADCAST_DRIVER=log
CACHE_DRIVER=file
FILESYSTEM_DISK=local
QUEUE_CONNECTION=database
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Mail Configuration
MAIL_MAILER=smtp
MAIL_HOST=mail.infomaniak.com
MAIL_PORT=587
MAIL_USERNAME=contact@gquiose.africa
MAIL_PASSWORD=Conakry2020@
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS="contact@gquiose.africa"
MAIL_FROM_NAME="${APP_NAME}"

# SMS Provider
SMS_PROVIDER=twilio
TWILIO_SID=
TWILIO_TOKEN=
TWILIO_FROM=

# Push Notifications
FCM_SERVER_KEY=
APNS_KEY_ID=
APNS_TEAM_ID=
APNS_BUNDLE_ID=com.gquiose.app
APNS_ENVIRONMENT=production
```

Sauvegarder avec `Ctrl+O`, `Enter`, puis `Ctrl+X`

### 9. Générer la clé d'application

```bash
sudo -u www-data php artisan key:generate
```

### 10. Exécuter les migrations et seeders

```bash
sudo -u www-data php artisan migrate:fresh --seed --force
```

### 11. Optimiser l'application

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan storage:link
```

### 12. Permissions finales

```bash
sudo chown -R www-data:www-data /var/www/gquiose
sudo chmod -R 755 /var/www/gquiose/storage
sudo chmod -R 755 /var/www/gquiose/bootstrap/cache
sudo chmod -R 775 /var/www/gquiose/database
sudo chmod 664 /var/www/gquiose/database/database.sqlite
```

### 13. Configuration SSL avec Let's Encrypt

```bash
# Installer certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtenir le certificat SSL
sudo certbot --nginx -d gquiose.mbayedione.xyz -d www.gquiose.mbayedione.xyz

# Suivre les instructions à l'écran
# Choisir l'option de redirection automatique HTTP vers HTTPS
```

### 14. Tester l'application

Ouvrez votre navigateur et accédez à :
```
https://gquiose.mbayedione.xyz
```

Connexion admin :
- Email : `admin@admin.com`
- Password : `password`

---

## Script de déploiement automatique

Copiez le fichier `deploy.sh` sur le serveur et rendez-le exécutable :

```bash
cd /var/www/gquiose
sudo chmod +x deploy.sh
```

Modifier le script pour SQLite si nécessaire (déjà optimisé).

---

## Sauvegardes SQLite

### Script de backup automatique

```bash
sudo nano /usr/local/bin/backup-gquiose-sqlite.sh
```

Contenu :
```bash
#!/bin/bash

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups/gquiose"
DB_PATH="/var/www/gquiose/database/database.sqlite"

# Créer le répertoire de backup
mkdir -p $BACKUP_DIR

# Backup de la base SQLite
cp $DB_PATH $BACKUP_DIR/database_$DATE.sqlite

# Backup des fichiers uploadés
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/gquiose/storage/app

# Compresser le backup SQLite
gzip $BACKUP_DIR/database_$DATE.sqlite

# Garder seulement les 7 derniers backups
find $BACKUP_DIR -type f -mtime +7 -delete

echo "✅ Backup effectué : $DATE"
```

Rendre exécutable :
```bash
sudo chmod +x /usr/local/bin/backup-gquiose-sqlite.sh
```

### Ajouter au crontab (backup quotidien à 2h du matin)

```bash
sudo crontab -e
```

Ajouter cette ligne :
```
0 2 * * * /usr/local/bin/backup-gquiose-sqlite.sh >> /var/log/gquiose-backup.log 2>&1
```

### Restaurer un backup

```bash
# Arrêter temporairement le serveur
sudo systemctl stop php8.3-fpm

# Restaurer la base
sudo gunzip -c /root/backups/gquiose/database_20250203_020000.sqlite.gz > /var/www/gquiose/database/database.sqlite

# Permissions
sudo chown www-data:www-data /var/www/gquiose/database/database.sqlite
sudo chmod 664 /var/www/gquiose/database/database.sqlite

# Redémarrer
sudo systemctl start php8.3-fpm
```

---

## Déploiements futurs (mises à jour)

Pour mettre à jour l'application après des modifications :

```bash
cd /var/www/gquiose
sudo -u www-data ./deploy.sh
```

Ou manuellement :
```bash
cd /var/www/gquiose
sudo -u www-data git pull origin main
sudo -u www-data composer install --no-dev --optimize-autoloader
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
```

---

## Monitoring et logs

### Voir les logs Laravel
```bash
sudo tail -f /var/www/gquiose/storage/logs/laravel.log
```

### Voir les logs Nginx
```bash
sudo tail -f /var/log/nginx/error.log
sudo tail -f /var/log/nginx/access.log
```

### Vérifier la taille de la base SQLite
```bash
ls -lh /var/www/gquiose/database/database.sqlite
du -h /var/www/gquiose/database/database.sqlite
```

### Optimiser la base SQLite
```bash
sudo -u www-data php artisan db:seed --class=OptimizeDatabaseSeeder
# Ou directement en SQL
sudo sqlite3 /var/www/gquiose/database/database.sqlite "VACUUM;"
```

---

## Dépannage

### 1. Erreur "database is locked"

SQLite peut avoir des problèmes avec des écritures concurrentes :

```bash
# Vérifier les processus
sudo lsof /var/www/gquiose/database/database.sqlite

# Augmenter le timeout dans config/database.php
# 'timeout' => env('DB_TIMEOUT', 60),
```

### 2. Permissions refusées

```bash
sudo chown -R www-data:www-data /var/www/gquiose/database
sudo chmod -R 775 /var/www/gquiose/database
sudo chmod 664 /var/www/gquiose/database/database.sqlite
```

### 3. 502 Bad Gateway

```bash
sudo systemctl status php8.3-fpm
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### 4. Base de données corrompue

```bash
# Vérifier l'intégrité
sudo sqlite3 /var/www/gquiose/database/database.sqlite "PRAGMA integrity_check;"

# Si corrompue, restaurer depuis backup
sudo gunzip -c /root/backups/gquiose/database_LATEST.sqlite.gz > /var/www/gquiose/database/database.sqlite
```

---

## Configuration du Queue Worker

Si vous utilisez les queues (recommandé pour les notifications) :

```bash
sudo nano /etc/systemd/system/gquiose-worker.service
```

Contenu :
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

Activer et démarrer :
```bash
sudo systemctl daemon-reload
sudo systemctl enable gquiose-worker
sudo systemctl start gquiose-worker
sudo systemctl status gquiose-worker
```

---

## Checklist finale ✅

- [ ] PHP 8.3 installé et vérifié
- [ ] Nginx configuré avec le bon server_name
- [ ] Repository cloné dans /var/www/gquiose
- [ ] Fichier SQLite créé avec bonnes permissions
- [ ] Dépendances Composer installées
- [ ] .env configuré avec DB_CONNECTION=sqlite
- [ ] Clé d'application générée
- [ ] Migrations exécutées
- [ ] Caches optimisés
- [ ] SSL configuré avec Let's Encrypt
- [ ] Site accessible sur https://gquiose.mbayedione.xyz
- [ ] Connexion admin testée
- [ ] Backups automatiques configurés
- [ ] Queue worker configuré (optionnel)

---

## Support

En cas de problème :
1. Vérifier les logs : `/var/www/gquiose/storage/logs/laravel.log`
2. Vérifier Nginx : `sudo nginx -t`
3. Vérifier PHP-FPM : `sudo systemctl status php8.3-fpm`
4. Vérifier les permissions : `ls -la /var/www/gquiose/database/`
