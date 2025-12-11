# Configuration de l'environnement (.env)

## Introduction

Le fichier `.env` contient toutes les variables d'environnement nécessaires au bon fonctionnement de l'application Laravel. Ce fichier ne doit **JAMAIS** être commité dans Git car il contient des informations sensibles.

---

## Configuration de base

### 1. Application

```env
# Nom de l'application (affiché dans les emails, etc.)
APP_NAME="Gquiose"

# Environnement (local, staging, production)
APP_ENV=production

# Clé de chiffrement (générer avec: php artisan key:generate)
APP_KEY=base64:votre_cle_ici

# Mode debug (TOUJOURS false en production)
APP_DEBUG=false

# URL publique de l'application
APP_URL=https://votre-domaine.com
```

**⚠️ Important:**
- `APP_DEBUG=false` en production pour la sécurité
- `APP_KEY` est généré automatiquement avec `php artisan key:generate`
- `APP_URL` doit correspondre à votre domaine réel

---

### 2. Base de données

```env
# Type de base de données (mysql, pgsql, sqlite, sqlsrv)
DB_CONNECTION=mysql

# Hôte de la base de données
DB_HOST=127.0.0.1

# Port MySQL (3306 par défaut)
DB_PORT=3306

# Nom de la base de données
DB_DATABASE=gquiose_db

# Identifiants de connexion
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe_securise
```

**Configuration recommandée:**

**Pour la production:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gquiose_production
DB_USERNAME=gquiose_user
DB_PASSWORD=un_mot_de_passe_tres_securise_avec_caracteres_speciaux_123!@#
```

**Pour le développement local:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gquiose_local
DB_USERNAME=root
DB_PASSWORD=
```

---

### 3. Logs et monitoring

```env
# Canal de logs (stack, single, daily, slack, syslog, errorlog, custom)
LOG_CHANNEL=daily

# Canal pour les logs de dépréciation
LOG_DEPRECATIONS_CHANNEL=null

# Niveau de log (debug, info, notice, warning, error, critical, alert, emergency)
LOG_LEVEL=error
```

**Recommandations:**
- **Production:** `LOG_CHANNEL=daily` (rotation automatique des logs)
- **Production:** `LOG_LEVEL=error` (ne logger que les erreurs)
- **Développement:** `LOG_LEVEL=debug` (tout logger)

---

### 4. Cache et sessions

```env
# Driver de cache (file, redis, memcached, database, array)
CACHE_DRIVER=redis

# Système de fichiers (local, public, s3)
FILESYSTEM_DISK=public

# Queue (sync, database, redis, sqs, beanstalkd)
QUEUE_CONNECTION=database

# Driver de session (file, cookie, database, redis, memcached, array)
SESSION_DRIVER=redis

# Durée de vie de la session en minutes
SESSION_LIFETIME=120
```

**Recommandations:**

**Pour une application en production avec trafic élevé:**
```env
CACHE_DRIVER=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis
```

**Pour une application simple:**
```env
CACHE_DRIVER=file
SESSION_DRIVER=file
QUEUE_CONNECTION=database
```

---

### 5. Redis (optionnel mais recommandé)

```env
# Hôte Redis
REDIS_HOST=127.0.0.1

# Mot de passe Redis (laisser null si pas de mot de passe)
REDIS_PASSWORD=null

# Port Redis (6379 par défaut)
REDIS_PORT=6379
```

**Pourquoi utiliser Redis ?**
- Cache ultra-rapide
- Gestion de sessions performante
- Queue workers efficaces
- Stockage de données temporaires

---

## Configuration des emails

### Configuration SMTP (Infomaniak, Gmail, etc.)

```env
# Type de mailer (smtp, sendmail, mailgun, ses, postmark)
MAIL_MAILER=smtp

# Serveur SMTP
MAIL_HOST=mail.infomaniak.com

# Port (587 pour TLS, 465 pour SSL)
MAIL_PORT=587

# Identifiants
MAIL_USERNAME=noreply@gquiose.com
MAIL_PASSWORD=votre_mot_de_passe_email

# Chiffrement (tls, ssl)
MAIL_ENCRYPTION=tls

# Email expéditeur par défaut
MAIL_FROM_ADDRESS=noreply@gquiose.com
MAIL_FROM_NAME="${APP_NAME}"
```

**Exemples de configurations:**

**Infomaniak:**
```env
MAIL_MAILER=smtp
MAIL_HOST=mail.infomaniak.com
MAIL_PORT=587
MAIL_USERNAME=noreply@votre-domaine.com
MAIL_PASSWORD=votre_mot_de_passe
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@votre-domaine.com
MAIL_FROM_NAME="Gquiose"
```

**Gmail (pour tests uniquement):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=votre-email@gmail.com
MAIL_PASSWORD=votre_app_password
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=votre-email@gmail.com
MAIL_FROM_NAME="Gquiose"
```

**⚠️ Pour Gmail:**
- Activez l'authentification à 2 facteurs
- Créez un "App Password" dans les paramètres Google
- N'utilisez pas votre mot de passe Gmail principal

---

## Configuration SMS

### Provider SMS (Twilio recommandé)

```env
# Provider SMS (twilio, vonage, aws_sns, local)
SMS_PROVIDER=twilio

# Twilio
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=votre_token_twilio
TWILIO_FROM=+224XXXXXXXXX
```

**Comment obtenir les credentials Twilio:**

1. Créez un compte sur [Twilio](https://www.twilio.com)
2. Achetez un numéro de téléphone Guinéen (+224)
3. Récupérez vos credentials:
   - **Account SID** → `TWILIO_SID`
   - **Auth Token** → `TWILIO_TOKEN`
   - **Phone Number** → `TWILIO_FROM`

**Alternative locale (pour tests):**
```env
SMS_PROVIDER=local
# Les SMS seront loggés dans storage/logs/sms.log
```

---

## Configuration Firebase (Notifications Push)

### Firebase Cloud Messaging (Android)

```env
# Chemin vers le fichier credentials Firebase Admin SDK
FCM_CREDENTIALS_PATH=storage/app/firebase/credentials.json
```

**Comment obtenir le fichier credentials.json:**

1. Allez sur [Firebase Console](https://console.firebase.google.com)
2. Sélectionnez votre projet (ou créez-en un)
3. Project Settings → Service Accounts
4. Cliquez sur "Generate new private key"
5. Téléchargez le fichier JSON
6. Placez-le dans `storage/app/firebase/credentials.json`

**Structure du fichier credentials.json:**
```json
{
  "type": "service_account",
  "project_id": "votre-projet",
  "private_key_id": "...",
  "private_key": "-----BEGIN PRIVATE KEY-----\n...\n-----END PRIVATE KEY-----\n",
  "client_email": "firebase-adminsdk-xxxxx@votre-projet.iam.gserviceaccount.com",
  "client_id": "...",
  "auth_uri": "https://accounts.google.com/o/oauth2/auth",
  "token_uri": "https://oauth2.googleapis.com/token",
  "auth_provider_x509_cert_url": "https://www.googleapis.com/oauth2/v1/certs",
  "client_x509_cert_url": "..."
}
```

### Apple Push Notifications (iOS)

```env
# Key ID (trouvé dans Apple Developer Portal)
APNS_KEY_ID=ABCDEFGHIJ

# Team ID (trouvé dans Apple Developer Portal)
APNS_TEAM_ID=1234567890

# Bundle ID de votre application iOS
APNS_BUNDLE_ID=com.gquiose.app

# Chemin vers le fichier AuthKey.p8
APNS_KEY_PATH=storage/app/apns/AuthKey_ABCDEFGHIJ.p8

# Environnement (sandbox pour TestFlight, production pour App Store)
APNS_ENVIRONMENT=production
```

**Comment obtenir les credentials APNs:**

1. Allez sur [Apple Developer Portal](https://developer.apple.com)
2. Certificates, Identifiers & Profiles → Keys
3. Créez une nouvelle clé avec la permission "Apple Push Notifications service (APNs)"
4. Téléchargez le fichier `.p8`
5. Notez le **Key ID** (10 caractères)
6. Placez le fichier dans `storage/app/apns/AuthKey_XXXXXXXXXX.p8`
7. Trouvez votre **Team ID** dans Membership

---

## Configuration OAuth (Réseaux sociaux)

### Google OAuth

```env
GOOGLE_CLIENT_ID=123456789-abcdefghijklmnop.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-votre_secret_ici
```

**Comment obtenir:**
1. [Google Cloud Console](https://console.cloud.google.com)
2. APIs & Services → Credentials
3. Create OAuth 2.0 Client ID
4. Type: Web application (pour Android) ou iOS
5. Configurez les URIs de redirection autorisées

### Facebook OAuth

```env
FACEBOOK_APP_ID=1234567890123456
FACEBOOK_APP_SECRET=abcdefghijklmnopqrstuvwxyz123456
```

**Comment obtenir:**
1. [Facebook Developers](https://developers.facebook.com)
2. Create App → Consumer
3. Settings → Basic
4. Récupérez App ID et App Secret

### Apple OAuth

```env
APPLE_BUNDLE_ID=com.gquiose.app
APPLE_TEAM_ID=ABCD123456
APPLE_KEY_ID=EFGH789012
APPLE_KEY_PATH=storage/app/apple/AuthKey_EFGH789012.p8
```

**Comment obtenir:**
1. [Apple Developer Portal](https://developer.apple.com)
2. Certificates, Identifiers & Profiles
3. Créez un Service ID pour "Sign in with Apple"
4. Créez une clé avec "Sign in with Apple"

---

## Configuration VBG National (Optionnel)

Si vous intégrez le système national de gestion des VBG:

```env
# Activer l'intégration
VBG_NATIONAL_ENABLED=true

# URL de l'API nationale
VBG_NATIONAL_API_URL=https://api-vbg-national.gov.gn/api/v1

# Clé API fournie par le système national
VBG_NATIONAL_API_KEY=votre_cle_api_nationale
```

**Si vous n'avez pas d'intégration nationale:**
```env
VBG_NATIONAL_ENABLED=false
VBG_NATIONAL_API_URL=
VBG_NATIONAL_API_KEY=
```

---

## Configuration des fonctionnalités (Nouveau)

### Évaluations et statistiques

```env
# Activer le système d'évaluation
EVALUATIONS_ENABLED=true

# Nombre de jours de conservation des évaluations
EVALUATIONS_RETENTION_DAYS=365

# Activer les statistiques détaillées
EVALUATIONS_STATS_ENABLED=true
```

### Suivi du cycle menstruel

```env
# Activer le suivi du cycle
CYCLE_TRACKING_ENABLED=true

# Durée moyenne du cycle (jours)
CYCLE_DEFAULT_LENGTH=28

# Durée moyenne des règles (jours)
PERIOD_DEFAULT_LENGTH=5

# Jours avant les règles pour les rappels
CYCLE_REMINDER_DAYS_BEFORE=3
```

### Notifications push avancées

```env
# Activer le tracking détaillé des notifications
NOTIFICATION_TRACKING_ENABLED=true

# Délai de retry en cas d'échec (minutes)
NOTIFICATION_RETRY_DELAY=15

# Nombre maximum de tentatives
NOTIFICATION_MAX_RETRIES=3

# Activer les notifications planifiées
NOTIFICATION_SCHEDULING_ENABLED=true
```

### Sécurité et anonymisation

```env
# Rayon d'anonymisation GPS (km)
LOCATION_ANONYMIZATION_RADIUS=3

# Chiffrement des preuves VBG
EVIDENCE_ENCRYPTION_ENABLED=true

# Clé de chiffrement des preuves (générer avec: php artisan key:generate)
EVIDENCE_ENCRYPTION_KEY=base64:votre_cle_de_chiffrement

# Supprimer les métadonnées EXIF des images
REMOVE_EXIF_DATA=true
```

---

## Configuration complète recommandée

### Pour la PRODUCTION:

```env
# Application
APP_NAME="Gquiose"
APP_ENV=production
APP_KEY=base64:votre_cle_generee_automatiquement
APP_DEBUG=false
APP_URL=https://api.gquiose.com

# Logs
LOG_CHANNEL=daily
LOG_DEPRECATIONS_CHANNEL=null
LOG_LEVEL=error

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gquiose_production
DB_USERNAME=gquiose_user
DB_PASSWORD=mot_de_passe_tres_securise_123!@#

# Cache et performance
CACHE_DRIVER=redis
FILESYSTEM_DISK=public
QUEUE_CONNECTION=redis
SESSION_DRIVER=redis
SESSION_LIFETIME=120

# Redis
REDIS_HOST=127.0.0.1
REDIS_PASSWORD=votre_mot_de_passe_redis_securise
REDIS_PORT=6379

# Email (Infomaniak)
MAIL_MAILER=smtp
MAIL_HOST=mail.infomaniak.com
MAIL_PORT=587
MAIL_USERNAME=noreply@gquiose.com
MAIL_PASSWORD=votre_mot_de_passe_email
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=noreply@gquiose.com
MAIL_FROM_NAME="${APP_NAME}"

# SMS (Twilio)
SMS_PROVIDER=twilio
TWILIO_SID=ACxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx
TWILIO_TOKEN=votre_token_twilio
TWILIO_FROM=+224XXXXXXXXX

# Firebase (Android)
FCM_CREDENTIALS_PATH=storage/app/firebase/credentials.json

# APNs (iOS)
APNS_KEY_ID=ABCDEFGHIJ
APNS_TEAM_ID=1234567890
APNS_BUNDLE_ID=com.gquiose.app
APNS_KEY_PATH=storage/app/apns/AuthKey_ABCDEFGHIJ.p8
APNS_ENVIRONMENT=production

# Google OAuth
GOOGLE_CLIENT_ID=votre_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=GOCSPX-votre_secret

# Facebook OAuth
FACEBOOK_APP_ID=votre_app_id
FACEBOOK_APP_SECRET=votre_app_secret

# Apple OAuth
APPLE_BUNDLE_ID=com.gquiose.app
APPLE_TEAM_ID=votre_team_id
APPLE_KEY_ID=votre_key_id
APPLE_KEY_PATH=storage/app/apple/AuthKey.p8

# VBG National (si applicable)
VBG_NATIONAL_ENABLED=false
VBG_NATIONAL_API_URL=
VBG_NATIONAL_API_KEY=

# Fonctionnalités avancées
EVALUATIONS_ENABLED=true
EVALUATIONS_STATS_ENABLED=true
CYCLE_TRACKING_ENABLED=true
NOTIFICATION_TRACKING_ENABLED=true
EVIDENCE_ENCRYPTION_ENABLED=true
LOCATION_ANONYMIZATION_RADIUS=3
```

### Pour le DÉVELOPPEMENT:

```env
# Application
APP_NAME="Gquiose Dev"
APP_ENV=local
APP_KEY=base64:votre_cle_generee_automatiquement
APP_DEBUG=true
APP_URL=http://localhost:8000

# Logs
LOG_CHANNEL=stack
LOG_LEVEL=debug

# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gquiose_local
DB_USERNAME=root
DB_PASSWORD=

# Cache (simple pour dev)
CACHE_DRIVER=file
FILESYSTEM_DISK=public
QUEUE_CONNECTION=database
SESSION_DRIVER=file
SESSION_LIFETIME=120

# Email (Log uniquement - pas d'envoi réel)
MAIL_MAILER=log
MAIL_HOST=
MAIL_PORT=
MAIL_USERNAME=
MAIL_PASSWORD=
MAIL_ENCRYPTION=
MAIL_FROM_ADDRESS=test@gquiose.com
MAIL_FROM_NAME="${APP_NAME}"

# SMS (Local - logs uniquement)
SMS_PROVIDER=local

# Firebase (utiliser un projet test)
FCM_CREDENTIALS_PATH=storage/app/firebase/credentials-test.json

# OAuth (utiliser des credentials de test)
GOOGLE_CLIENT_ID=
GOOGLE_CLIENT_SECRET=
FACEBOOK_APP_ID=
FACEBOOK_APP_SECRET=

# VBG National
VBG_NATIONAL_ENABLED=false

# Fonctionnalités avancées (utiliser les valeurs par défaut)
EVALUATIONS_ENABLED=true
CYCLE_TRACKING_ENABLED=true
NOTIFICATION_TRACKING_ENABLED=true
EVIDENCE_ENCRYPTION_ENABLED=false
```

---

## Installation et configuration

### 1. Première installation

```bash
# Copier le fichier exemple
cp .env.example .env

# Générer la clé de l'application
php artisan key:generate

# Modifier le fichier .env avec vos configurations
nano .env

# Lancer les migrations
php artisan migrate

# Créer les liens symboliques (pour les fichiers publics)
php artisan storage:link
```

### 2. Permissions des fichiers

```bash
# Donner les permissions d'écriture
chmod -R 775 storage bootstrap/cache

# Si vous êtes sur un serveur web
chown -R www-data:www-data storage bootstrap/cache
```

### 3. Cache de configuration

En production, optimisez les performances:

```bash
# Cache de configuration
php artisan config:cache

# Cache des routes
php artisan route:cache

# Cache des vues
php artisan view:cache

# Optimisation de l'autoload
composer dump-autoload --optimize
```

**⚠️ Important:** Après chaque modification du `.env`, relancez:
```bash
php artisan config:clear
php artisan config:cache
```

---

## Sécurité

### Bonnes pratiques:

1. **Ne jamais commiter le fichier .env**
   - Ajoutez `.env` dans `.gitignore`
   - Utilisez `.env.example` comme template

2. **Mots de passe forts**
   - Base de données: minimum 20 caractères
   - Redis: minimum 32 caractères
   - Mélange de lettres, chiffres, caractères spéciaux

3. **HTTPS obligatoire en production**
   - Configurez un certificat SSL (Let's Encrypt gratuit)
   - Forcez HTTPS dans `app/Providers/AppServiceProvider.php`

4. **Backups réguliers**
   - Sauvegardez la base de données quotidiennement
   - Sauvegardez le dossier `storage/app` (fichiers uploadés)

5. **Variables sensibles**
   - Stockez les clés API dans `.env`, jamais dans le code
   - Utilisez des secrets managers en production (AWS Secrets Manager, Vault)

---

## Vérification de la configuration

Créez un script de vérification:

```bash
# Vérifier la connexion à la base de données
php artisan tinker
>>> DB::connection()->getPdo();

# Tester l'envoi d'email
php artisan tinker
>>> Mail::raw('Test', function($m) { $m->to('test@example.com')->subject('Test'); });

# Vérifier Redis
php artisan tinker
>>> Cache::driver('redis')->put('test', 'value', 60);
>>> Cache::driver('redis')->get('test');

# Tester les queues
php artisan queue:work --once
```

---

## Nouveaux services et workers (Version 1.1.0)

### Queue Workers pour notifications

Pour traiter les notifications en arrière-plan, démarrez les workers:

```bash
# Worker principal
php artisan queue:work redis --queue=default,notifications --tries=3

# Worker dédié aux notifications (recommandé en production)
php artisan queue:work redis --queue=notifications --tries=3 --timeout=60
```

**Configuration supervisord (production):**
```ini
[program:gquiose-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /path/to/gquiose-web/artisan queue:work redis --sleep=3 --tries=3 --max-time=3600
autostart=true
autorestart=true
user=www-data
numprocs=2
redirect_stderr=true
stdout_logfile=/path/to/gquiose-web/storage/logs/worker.log
stopwaitsecs=3600
```

### Scheduler pour rappels de cycle

Ajoutez à votre crontab:

```bash
* * * * * cd /path/to/gquiose-web && php artisan schedule:run >> /dev/null 2>&1
```

Le scheduler gère:
- Rappels de cycle menstruel
- Notifications planifiées
- Nettoyage des logs anciens
- Retry des notifications échouées

### Service d'anonymisation GPS

Le service d'anonymisation s'active automatiquement. Vérifiez sa configuration:

```bash
php artisan tinker
>>> config('app.location_anonymization_radius')
```

### Service de chiffrement des preuves

Testez le chiffrement:

```bash
php artisan tinker
>>> $service = app(\App\Services\SecureEvidenceService::class);
>>> $service->testEncryption();
```

---

## Troubleshooting

### Erreur: "No application encryption key"
```bash
php artisan key:generate
```

### Erreur de connexion à la base de données
- Vérifiez que MySQL est démarré
- Vérifiez les credentials dans `.env`
- Vérifiez que la base de données existe

### Erreur de permissions
```bash
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
```

### Cache bloqué après modification .env
```bash
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear
```

### Les notifications ne sont pas envoyées
```bash
# Vérifier les jobs en queue
php artisan queue:failed

# Relancer les jobs échoués
php artisan queue:retry all

# Vérifier les logs
tail -f storage/logs/laravel.log
```

### Les statistiques d'évaluations ne s'affichent pas
```bash
# Vérifier la configuration
php artisan config:cache

# Vérifier les données
php artisan tinker
>>> \App\Models\Evaluation::count()
```

---

## Support

Pour toute question sur la configuration:
- Documentation Laravel: https://laravel.com/docs
- Email: support@gquiose.com

---

**Version:** 1.1.0
**Dernière mise à jour:** 1er Décembre 2025

## Changelog

### Version 1.1.0 (1er Décembre 2025)

#### Nouvelles configurations
- ✅ Variables pour le système d'évaluations
- ✅ Configuration du suivi du cycle menstruel
- ✅ Paramètres de tracking des notifications
- ✅ Configuration de l'anonymisation GPS
- ✅ Paramètres de chiffrement des preuves

#### Nouveaux services
- 🔄 Queue workers pour notifications
- 📅 Scheduler pour rappels de cycle
- 🔐 Service de chiffrement des preuves
- 📍 Service d'anonymisation GPS

#### Améliorations
- 📊 Configuration des statistiques d'évaluations
- 🔔 Gestion avancée des notifications push
- 🔒 Sécurité renforcée pour les preuves VBG
- ⚙️ Guide de configuration supervisord

### Version 1.0.0 (28 Novembre 2025)
- 🚀 Configuration initiale complète
- 📧 Configuration email et SMS
- 🔔 Configuration Firebase et APNs
- 🔐 Configuration OAuth (Google, Facebook, Apple)
- 🗄️ Configuration base de données et Redis
