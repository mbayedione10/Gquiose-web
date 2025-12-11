# Déploiement Laravel sur Infomaniak

## Prérequis

- Hébergement Web Infomaniak avec **PHP 8.3**
- **Base de données MariaDB** créée via le Manager
- Accès **SSH** activé
- Node.js installé **localement** (pour compiler les assets)

---

## 1. Configuration PHP 8.3 en SSH

Par défaut, SSH utilise PHP 8.1. Configurez PHP 8.3 :

```bash
# Ajouter les alias au profil
echo 'alias php="/usr/bin/php-8.3"' >> ~/.bashrc
echo 'alias composer="/usr/bin/php-8.3 /opt/php8.1/bin/composer2.phar"' >> ~/.bashrc
source ~/.bashrc

# Vérifier
php -v  # Doit afficher PHP 8.3.x
```

---

## 2. Cloner le projet

```bash
cd ~/sites/votre-domaine
git clone https://github.com/mbayedione10/Gquiose-web.git .

# Ou si dans un sous-dossier
git clone https://github.com/mbayedione10/Gquiose-web.git Gquiose-web
```

---

## 3. Installer les dépendances PHP

```bash
composer install --no-dev --optimize-autoloader
```

---

## 4. Compiler les assets (en local)

**Node.js n'est pas disponible sur l'hébergement mutualisé Infomaniak.**

Sur votre **machine locale** :

```bash
cd /chemin/vers/Gquiose-web

# Installer et compiler
npm install
npm run build

# Commiter les assets compilés
git add public/build -f
git commit -m "Add compiled production assets"
git push
```

Sur le **serveur Infomaniak** :

```bash
git pull
```

---

## 5. Configurer l'environnement (.env)

```bash
cp .env.example .env
nano .env
```

Modifier les valeurs suivantes :

```env
APP_NAME=Gquiose
APP_ENV=production
APP_DEBUG=false
APP_URL=https://votre-domaine.com

DB_CONNECTION=mysql
DB_HOST=xxxxx.myd.infomaniak.com
DB_PORT=3306
DB_DATABASE=votre_base
DB_USERNAME=votre_user
DB_PASSWORD=votre_password

CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database

# Mail Infomaniak
MAIL_MAILER=smtp
MAIL_HOST=mail.infomaniak.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@votre-domaine.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@votre-domaine.com
MAIL_FROM_NAME="${APP_NAME}"
```

---

## 6. Initialiser Laravel

```bash
# Générer la clé d'application
php artisan key:generate

# Exécuter les migrations
php artisan migrate --force

# Créer le Super Admin
php artisan db:seed --class=SuperAdminSeeder --force

# Créer le lien symbolique storage
php artisan storage:link
```

---

## 7. Publier les assets Filament & Livewire

```bash
# Assets Livewire
php artisan livewire:publish --assets

# Assets Filament (v2) - copie manuelle requise
mkdir -p public/filament/assets
cp vendor/filament/filament/dist/* public/filament/assets/
```

---

## 8. Optimiser pour la production

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

---

## 9. Configurer le Document Root

Dans le **Manager Infomaniak** :

1. Aller dans **Hébergement Web**
2. Cliquer sur **Sites**
3. Sélectionner **votre-domaine**
4. Modifier le **Dossier racine** vers :
   ```
   /sites/votre-domaine/Gquiose-web/public
   ```
5. Vérifier que **PHP 8.3** est sélectionné
6. Sauvegarder

**Important** : Le document root doit pointer vers `/public`, pas vers la racine du projet.

---

## 10. Réinitialiser un mot de passe admin

Si vous avez oublié le mot de passe (tinker ne fonctionne pas sur Infomaniak) :

```bash
cat > reset_password.php << 'EOF'
<?php
require 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
App\Models\User::where('email', 'admin@admin.com')->update(['password' => bcrypt('nouveau_mot_de_passe')]);
echo "✅ Mot de passe réinitialisé!\n";
EOF

php reset_password.php
rm reset_password.php
```

---

## 11. Script de mise à jour (deploy.sh)

Créez ce fichier à la racine du projet pour les futures mises à jour :

```bash
#!/bin/bash
echo "🚀 Déploiement Gquiose sur Infomaniak..."

# Mode maintenance
php artisan down || true

# Récupérer les dernières modifications
git pull origin main

# Installer les dépendances
composer install --no-dev --optimize-autoloader

# Publier les assets
mkdir -p public/filament/assets
cp vendor/filament/filament/dist/* public/filament/assets/ 2>/dev/null || true
php artisan livewire:publish --assets

# Migrations
php artisan migrate --force

# Clear et recache
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Désactiver le mode maintenance
php artisan up

echo "✅ Déploiement terminé!"
```

Rendre exécutable :

```bash
chmod +x deploy.sh
```

Pour déployer les mises à jour :

```bash
./deploy.sh
```

---

## Checklist de déploiement

| Étape | Commande/Action | Statut |
|-------|-----------------|--------|
| Alias PHP 8.3 configuré | `source ~/.bashrc && php -v` | ☐ |
| Dépendances composer | `composer install --no-dev` | ☐ |
| Assets compilés (local) | `npm run build` + push | ☐ |
| Fichier .env configuré | `nano .env` | ☐ |
| Clé générée | `php artisan key:generate` | ☐ |
| Migrations | `php artisan migrate --force` | ☐ |
| Admin créé | `php artisan db:seed --class=SuperAdminSeeder` | ☐ |
| Storage link | `php artisan storage:link` | ☐ |
| Assets Livewire | `php artisan livewire:publish --assets` | ☐ |
| Assets Filament | `cp vendor/filament/...` | ☐ |
| Cache optimisé | `php artisan config:cache` | ☐ |
| Document Root → /public | Manager Infomaniak | ☐ |
| PHP 8.3 (Web) | Manager Infomaniak | ☐ |
| Site accessible HTTPS | Test navigateur | ☐ |

---

## Accès administration

- **URL** : `https://votre-domaine.com/admin`
- **Email** : `admin@admin.com`
- **Mot de passe** : `password`

⚠️ **Changez le mot de passe immédiatement après la première connexion !**

---

## Dépannage

### Le navigateur télécharge les fichiers PHP

→ Vérifiez que PHP 8.3 est sélectionné dans le Manager pour le site.

### Erreur "PHP version >= 8.3.0 required"

→ Utilisez le chemin complet : `/usr/bin/php-8.3 artisan ...`

### Tinker ne fonctionne pas

→ Normal sur Infomaniak. Utilisez le script `reset_password.php` ci-dessus.

### 500 Internal Server Error

```bash
# Vérifier les logs
tail -f storage/logs/laravel.log

# Vérifier les permissions
chmod -R 755 storage bootstrap/cache
```

### Assets CSS/JS ne chargent pas

```bash
# Vérifier que le build existe
ls -la public/build/

# Si vide, recompiler en local et push
npm run build
git add public/build -f
git commit -m "Rebuild assets"
git push
```

---

## Commandes utiles

```bash
# Voir les logs Laravel
tail -f storage/logs/laravel.log

# Vider tous les caches
php artisan optimize:clear

# Recréer les caches
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Statut des migrations
php artisan migrate:status
```

---

## Notes importantes

1. **Node.js** : Non disponible sur hébergement mutualisé. Compilez toujours les assets en local.

2. **Filament v2** : Les assets doivent être copiés manuellement dans `public/filament/assets/`.

3. **PHP CLI vs Web** : Le panel configure PHP pour Apache, mais SSH utilise une version différente par défaut. Utilisez toujours les alias ou le chemin complet `/usr/bin/php-8.3`.

4. **Tinker** : Ne fonctionne pas sur Infomaniak (erreur d'écriture). Utilisez des scripts PHP temporaires.

5. **Mises à jour** : Après chaque `git pull`, n'oubliez pas de republier les assets Filament/Livewire et de vider les caches.
