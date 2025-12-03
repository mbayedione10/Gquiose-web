# Guide de Déploiement - Gquiose sur DigitalOcean

## Prérequis sur le Droplet

### 1. Mise à jour vers PHP 8.3 (depuis PHP 8.2)

```bash
# Se connecter au serveur
ssh root@votre-ip-droplet

# Ajouter le repository Ondrej PHP
sudo apt update
sudo apt install -y software-properties-common
sudo add-apt-repository ppa:ondrej/php -y
sudo apt update

# Installer PHP 8.3 et extensions requises
sudo apt install -y php8.3 php8.3-fpm php8.3-cli php8.3-common \
    php8.3-mysql php8.3-sqlite3 php8.3-xml php8.3-curl \
    php8.3-gd php8.3-mbstring php8.3-zip php8.3-bcmath \
    php8.3-intl php8.3-readline

# Désactiver PHP 8.2 (si Nginx)
sudo systemctl stop php8.2-fpm
sudo systemctl disable php8.2-fpm

# Activer PHP 8.3
sudo systemctl start php8.3-fpm
sudo systemctl enable php8.3-fpm

# Vérifier la version
php -v
```

### 2. Configuration de la base de données

#### Option A : MySQL
```bash
sudo apt install mysql-server -y
sudo mysql_secure_installation

# Créer la base de données
sudo mysql -e "CREATE DATABASE gquiose;"
sudo mysql -e "CREATE USER 'gquiose_user'@'localhost' IDENTIFIED BY 'votre_mot_de_passe_securise';"
sudo mysql -e "GRANT ALL PRIVILEGES ON gquiose.* TO 'gquiose_user'@'localhost';"
sudo mysql -e "FLUSH PRIVILEGES;"
```

#### Option B : SQLite (Plus simple)
```bash
# Aucune installation nécessaire, juste créer le fichier
sudo mkdir -p /var/www/gquiose/database
sudo touch /var/www/gquiose/database/database.sqlite
sudo chown -R www-data:www-data /var/www/gquiose/database
sudo chmod -R 775 /var/www/gquiose/database
```

### 3. Installation de Composer

```bash
# Télécharger et installer Composer
cd ~
curl -sS https://getcomposer.org/installer -o composer-setup.php
sudo php composer-setup.php --install-dir=/usr/local/bin --filename=composer
composer --version
```

### 4. Configuration du serveur web

#### Si Nginx :
```bash
# Copier la configuration
sudo nano /etc/nginx/sites-available/gquiose

# Coller le contenu de nginx-config-example.conf
# Modifier server_name et root selon vos besoins

# Activer le site
sudo ln -s /etc/nginx/sites-available/gquiose /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

#### Si Apache :
```bash
sudo a2enmod rewrite
sudo systemctl restart apache2
```

## Déploiement de l'application

### 1. Cloner le repository

```bash
# Aller dans le répertoire web
cd /var/www

# Cloner le repository
sudo git clone https://github.com/votre-username/gquiose-web.git gquiose
cd gquiose

# Donner les bonnes permissions
sudo chown -R www-data:www-data /var/www/gquiose
sudo chmod -R 755 /var/www/gquiose
```

### 2. Installer les dépendances

```bash
cd /var/www/gquiose
sudo -u www-data composer install --no-dev --optimize-autoloader
```

### 3. Configurer l'environnement

```bash
# Copier le fichier .env
sudo cp .env.example .env
sudo nano .env
```

Configuration .env pour production :
```env
APP_NAME="G Qui Ose"
APP_ENV=production
APP_KEY=
APP_DEBUG=false
APP_URL=https://votre-domaine.com

LOG_CHANNEL=stack
LOG_LEVEL=error

# Pour MySQL
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gquiose
DB_USERNAME=gquiose_user
DB_PASSWORD=votre_mot_de_passe_securise

# OU pour SQLite
# DB_CONNECTION=sqlite
# DB_DATABASE=/var/www/gquiose/database/database.sqlite

QUEUE_CONNECTION=database
SESSION_DRIVER=database
CACHE_DRIVER=file

# Vos autres configurations (SMS, notifications, etc.)
```

### 4. Générer la clé d'application

```bash
sudo -u www-data php artisan key:generate
```

### 5. Exécuter les migrations

```bash
sudo -u www-data php artisan migrate --force
sudo -u www-data php artisan db:seed --force
```

### 6. Optimiser l'application

```bash
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan storage:link
```

### 7. Permissions finales

```bash
sudo chown -R www-data:www-data /var/www/gquiose
sudo chmod -R 755 /var/www/gquiose/storage
sudo chmod -R 755 /var/www/gquiose/bootstrap/cache
```

## Configuration SSL avec Let's Encrypt

```bash
# Installer certbot
sudo apt install certbot python3-certbot-nginx -y

# Obtenir le certificat SSL
sudo certbot --nginx -d votre-domaine.com -d www.votre-domaine.com

# Renouvellement automatique (déjà configuré)
sudo certbot renew --dry-run
```

## Déploiements futurs

Pour les mises à jour ultérieures, utilisez le script deploy.sh :

```bash
cd /var/www/gquiose
sudo -u www-data ./deploy.sh
```

## Surveillance et logs

```bash
# Voir les logs Laravel
sudo tail -f /var/www/gquiose/storage/logs/laravel.log

# Voir les logs Nginx
sudo tail -f /var/log/nginx/error.log

# Redémarrer les services si nécessaire
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

## Dépannage

### Permission denied
```bash
sudo chown -R www-data:www-data /var/www/gquiose
sudo chmod -R 755 /var/www/gquiose/storage
```

### 502 Bad Gateway
```bash
sudo systemctl status php8.3-fpm
sudo systemctl restart php8.3-fpm
sudo systemctl restart nginx
```

### Base de données connection refused
```bash
# Vérifier MySQL
sudo systemctl status mysql

# Vérifier les credentials dans .env
sudo nano /var/www/gquiose/.env
```

## Configuration du Queue Worker (optionnel)

Si vous utilisez les queues :

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

[Install]
WantedBy=multi-user.target
```

Activer :
```bash
sudo systemctl enable gquiose-worker
sudo systemctl start gquiose-worker
```

## Sauvegardes automatiques

```bash
# Créer un script de backup
sudo nano /usr/local/bin/backup-gquiose.sh
```

Contenu :
```bash
#!/bin/bash
DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/root/backups"

mkdir -p $BACKUP_DIR

# Backup de la base de données
mysqldump -u gquiose_user -p'votre_mot_de_passe' gquiose > $BACKUP_DIR/db_$DATE.sql

# Backup des fichiers uploadés
tar -czf $BACKUP_DIR/storage_$DATE.tar.gz /var/www/gquiose/storage/app

# Garder seulement les 7 derniers backups
find $BACKUP_DIR -type f -mtime +7 -delete
```

Ajouter au crontab :
```bash
sudo chmod +x /usr/local/bin/backup-gquiose.sh
sudo crontab -e
# Ajouter : 0 2 * * * /usr/local/bin/backup-gquiose.sh
```
