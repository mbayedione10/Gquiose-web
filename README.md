# Gquiose - Génération Qui Ose

Application backend pour la lutte contre les violences basées sur le genre (VBG) en Afrique.

[![Laravel](https://img.shields.io/badge/Laravel-10.x-red.svg)](https://laravel.com)
[![PHP](https://img.shields.io/badge/PHP-8.3-blue.svg)](https://php.net)
[![Filament](https://img.shields.io/badge/Filament-3.x-orange.svg)](https://filamentphp.com)

## À propos

**Gquiose** (Génération Qui Ose) est une plateforme complète de signalement et de support pour les victimes de violences basées sur le genre. L'application fournit une API REST sécurisée pour les applications mobiles iOS et Android, ainsi qu'un panel d'administration Filament pour la gestion des signalements et du contenu éducatif.

### Fonctionnalités principales

- **🚨 Signalement de VBG** - Formulaire multi-étapes pour documenter les incidents avec preuves
- **📱 Support multi-plateforme** - API pour iOS et Android avec notifications push (FCM/APNs)
- **📚 Contenu éducatif** - Articles, vidéos, FAQs sur la prévention des violences
- **💬 Forum communautaire** - Espace d'échange sécurisé et modéré
- **📊 Évaluations** - Questionnaires d'évaluation des connaissances
- **🩸 Suivi menstruel** - Calendrier de cycle avec symptômes et rappels
- **🏥 Annuaire de structures** - Localisation des centres d'aide et de santé
- **🔐 Authentification flexible** - Email, téléphone, OAuth (Google, Facebook, Apple)

## Technologies

- **Backend**: Laravel 10 + PHP 8.3
- **Base de données**: MySQL
- **Admin**: Filament 3
- **API**: RESTful JSON avec Laravel Sanctum
- **Frontend**: Vite + Bootstrap 4 + SCSS
- **SMS**: Multi-provider (NimbaSMS, Twilio, Vonage)
- **Push**: Firebase Cloud Messaging + Apple Push Notification Service
- **Tests**: Pest PHP

## Prérequis

- PHP 8.3 ou supérieur
- Composer 2.x
- Node.js 20.x et npm
- MySQL 8.0+
- Extensions PHP: `curl`, `gd`, `mbstring`, `xml`, `zip`, `bcmath`, `intl`

## Installation

### 1. Cloner le repository

```bash
git clone https://github.com/mbayedione10/Gquiose-web.git
cd Gquiose-web
```

### 2. Installer les dépendances

```bash
composer install
npm install
```

### 3. Configuration de l'environnement

```bash
cp .env.example .env
php artisan key:generate
```

Éditer le fichier `.env` avec vos configurations:

```env
# Base de données
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=gquiose_db
DB_USERNAME=votre_utilisateur
DB_PASSWORD=votre_mot_de_passe

# Provider SMS (nimba, twilio, vonage)
SMS_PROVIDER=nimba
NIMBA_SMS_SERVICE_ID=votre_service_id
NIMBA_SMS_SECRET=votre_secret

# Push Notifications
FCM_SERVER_KEY=votre_fcm_key
APNS_KEY_ID=votre_apns_key_id
APNS_TEAM_ID=votre_team_id
```

Voir [CONFIGURATION_ENVIRONNEMENT.md](CONFIGURATION_ENVIRONNEMENT.md) pour la documentation complète.

### 4. Base de données

```bash
php artisan migrate
php artisan db:seed
```

### 5. Compiler les assets

```bash
# Développement
npm run dev

# Production
npm run build
```

### 6. Lancer l'application

```bash
php artisan serve
```

L'application sera accessible à `http://localhost:8000`

## Documentation

- **[DOCUMENTATION_API_COMPLETE.md](DOCUMENTATION_API_COMPLETE.md)** - Documentation complète de l'API avec tous les endpoints
- **[CONFIGURATION_ENVIRONNEMENT.md](CONFIGURATION_ENVIRONNEMENT.md)** - Guide de configuration des variables d'environnement
- **[DEPLOY-CHECKLIST.md](DEPLOY-CHECKLIST.md)** - Guide de déploiement en production
- **[.github/copilot-instructions.md](.github/copilot-instructions.md)** - Instructions pour les agents IA

## Structure du projet

```
app/
├── Http/Controllers/
│   ├── API*Controller.php      # Endpoints API mobile
│   └── *Controller.php         # Contrôleurs web/Filament
├── Services/                   # Logique métier
│   ├── SMS/                    # Services SMS multi-provider
│   ├── Push/                   # Notifications push FCM/APNs
│   └── SocialAuth/             # Vérification OAuth
├── Models/                     # Modèles Eloquent
├── Filament/Resources/         # Ressources admin Filament
└── Helpers.php                 # Fonctions helper globales
```

## Tests

```bash
# Exécuter tous les tests
./vendor/bin/pest

# Tests avec couverture
./vendor/bin/pest --coverage

# Format du code
./vendor/bin/pint
```

## Déploiement

Pour déployer en production, suivre le guide [DEPLOY-CHECKLIST.md](DEPLOY-CHECKLIST.md):

1. Configuration serveur (Nginx + PHP 8.3-FPM)
2. Variables d'environnement de production
3. Compilation des assets: `npm run build && php artisan optimize`
4. Configuration des workers pour les queues
5. Configuration SSL avec Let's Encrypt

## Panel d'administration

Accès: `https://votre-domaine.com/admin`

Le panel Filament permet de:
- Gérer les signalements de VBG
- Modérer le forum et les contenus
- Créer et publier des articles, vidéos, FAQs
- Consulter les statistiques et évaluations
- Gérer les utilisateurs et permissions

## API Mobile

**Base URL**: `https://test.gquiose.africa/api/v1`

Endpoints principaux:
- `POST /login` - Authentification
- `POST /register` - Inscription
- `POST /alertes/step1-6` - Workflow de signalement VBG
- `GET /articles` - Contenu éducatif
- `POST /forum/message/sync` - Forum

Voir [DOCUMENTATION_API_COMPLETE.md](DOCUMENTATION_API_COMPLETE.md) pour tous les endpoints.

## Sécurité

Pour signaler une vulnérabilité de sécurité, veuillez contacter [mbayedione10@gmail.com](mailto:mbayedione10@gmail.com).

- Authentification Sanctum avec tokens à expiration
- Validation stricte des entrées utilisateur
- Protection CSRF sur les formulaires web
- Chiffrement des données sensibles
- Logs de sécurité pour les événements critiques

## Contribution

Les contributions sont les bienvenues! Pour contribuer:

1. Fork le projet
2. Créer une branche (`git checkout -b feature/amelioration`)
3. Commit vos changements (`git commit -m 'Ajout nouvelle fonctionnalité'`)
4. Push vers la branche (`git push origin feature/amelioration`)
5. Ouvrir une Pull Request

## Licence

Ce projet est sous licence MIT. Voir le fichier [LICENSE](LICENSE) pour plus de détails.

## Contact

**Email**: mbayedione10@gmail.com  
**URL Production**: https://test.gquiose.africa

---

Développé avec ❤️ pour soutenir la lutte contre les violences basées sur le genre en Afrique.
