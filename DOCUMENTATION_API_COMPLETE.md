# Documentation Complète de l'API Gquiose

**Version:** 1.0
**Base URL:** `https://gquiose.mbayedione.xyz/api/v1`
**Format:** JSON
**Date:** 2025-12-04

---

## Configuration Serveur

### Variables d'Environnement Requises

Pour le bon fonctionnement de l'API, les variables suivantes doivent être configurées dans le fichier `.env`:

#### SMS Provider (pour codes de vérification)
```env
# SMS Provider (twilio, vonage, aws_sns, local)
SMS_PROVIDER=twilio
TWILIO_SID=your_twilio_account_sid
TWILIO_TOKEN=your_twilio_auth_token
TWILIO_FROM=your_twilio_phone_number
```

**Providers supportés:**
- **Twilio** (recommandé) - [https://www.twilio.com/](https://www.twilio.com/)
- **Vonage** (ex-Nexmo) - [https://www.vonage.com/](https://www.vonage.com/)
- **AWS SNS** - [https://aws.amazon.com/sns/](https://aws.amazon.com/sns/)
- **local** - Mode test sans envoi réel

#### Notifications Push
```env
# Push Notifications - FCM (Android)
FCM_SERVER_KEY=your_firebase_server_key

# Push Notifications - APNs (iOS)
APNS_KEY_ID=your_key_id
APNS_TEAM_ID=your_team_id
APNS_BUNDLE_ID=com.gquiose.app
APNS_ENVIRONMENT=production
```

Voir la section [12. Notifications](#12-notifications) pour plus de détails sur la configuration des notifications push.

---

## Table des Matières

1. [Authentification](#1-authentification)
2. [Configuration](#2-configuration)
3. [Articles](#3-articles)
4. [Rubriques](#4-rubriques)
5. [Structures d'Aide](#5-structures-daide)
6. [Forum](#6-forum)
7. [Vidéos](#7-vidéos)
8. [Alertes VBG](#8-alertes-vbg)
9. [Quiz](#9-quiz)
10. [Évaluations](#10-évaluations)
11. [Cycle Menstruel](#11-cycle-menstruel)
12. [Notifications](#12-notifications)

---

## 1. Authentification

### 1.1 Inscription

#### Informations générales
- **URL:** `/api/v1/register`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Créer un nouveau compte utilisateur.

#### Paramètres requis
```json
{
  "nom": "string",
  "prenom": "string",
  "email": "string (email valide)",
  "phone": "string",
  "password": "string (min 8 caractères)",
  "password_confirmation": "string",
  "dob": "date (YYYY-MM-DD)",
  "sexe": "string (M/F)",
  "ville_id": "integer",
  "platform": "string (ios/android/web)"
}
```

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/register
Content-Type: application/json

{
  "nom": "Diallo",
  "prenom": "Fatou",
  "email": "fatou.diallo@test.gn",
  "phone": "+224621234567",
  "password": "password123",
  "password_confirmation": "password123",
  "dob": "1995-03-15",
  "sexe": "F",
  "ville_id": 1,
  "platform": "ios"
}
```

#### Réponse réussie (201)
```json
{
  "code": 201,
  "message": "Utilisateur créé avec succès",
  "data": {
    "utilisateur": {
      "id": 1,
      "nom": "Diallo",
      "prenom": "Fatou",
      "email": "fatou.diallo@test.gn"
    }
  }
}
```

---

### 1.2 Connexion

#### Informations générales
- **URL:** `/api/v1/login`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Se connecter et obtenir un token d'authentification.

#### Paramètres requis
```json
{
  "identifier": "string (email ou téléphone)",
  "password": "string",
  "platform": "string (ios/android/web)"
}
```

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/login
Content-Type: application/json

{
  "identifier": "fatou.diallo@test.gn",
  "password": "password",
  "platform": "ios"
}
```

#### Réponse réussie (200)
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "utilisateur": {
      "id": 1,
      "nom": "Diallo",
      "prenom": "Fatou",
      "phone": "+224621234567",
      "email": "fatou.diallo@test.gn",
      "dob": "1995-03-15T00:00:00.000000Z",
      "sexe": "F",
      "photo": null,
      "ville_id": 1,
      "status": true
    },
    "token": "5|E1yJI53lywUw5fPUHM8ZDrFYEMvOQFEGZMpCj14i0ead7686",
    "token_type": "Bearer",
    "expires_in": 2592000
  }
}
```

#### Utilisation du token
```bash
Authorization: Bearer 5|E1yJI53lywUw5fPUHM8ZDrFYEMvOQFEGZMpCj14i0ead7686
```

---

### 1.3 Déconnexion

#### Informations générales
- **URL:** `/api/v1/logout`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise (Bearer Token)
- **Format de réponse:** JSON

#### Description
Se déconnecter et invalider le token actuel.

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/logout
Authorization: Bearer {votre_token}
```

#### Réponse réussie (200)
```json
{
  "code": 200,
  "message": "Déconnexion réussie"
}
```

---

### 1.4 Confirmation de code

#### Informations générales
- **URL:** `/api/v1/code-confirmation`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Confirmer l'inscription avec le code reçu par SMS/Email.

#### Paramètres requis
```json
{
  "user_id": "integer",
  "code": "string (6 chiffres)"
}
```

---

### 1.5 Réinitialisation du mot de passe

#### 1.5.1 Envoyer le code de réinitialisation

#### Informations générales
- **URL:** `/api/v1/send-password-reset-code`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Paramètres requis
```json
{
  "identifier": "string (email ou téléphone)"
}
```

#### 1.5.2 Réinitialiser le mot de passe

#### Informations générales
- **URL:** `/api/v1/reset-password`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Paramètres requis
```json
{
  "identifier": "string",
  "code": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

---

### 1.6 Modifier le profil

#### Informations générales
- **URL:** `/api/v1/update-profile`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Paramètres (optionnels)
```json
{
  "nom": "string",
  "prenom": "string",
  "phone": "string",
  "email": "string",
  "dob": "date",
  "sexe": "string",
  "ville_id": "integer",
  "photo": "file (image)"
}
```

---

### 1.7 Changer le mot de passe

#### Informations générales
- **URL:** `/api/v1/change-password`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Paramètres requis
```json
{
  "current_password": "string",
  "password": "string",
  "password_confirmation": "string"
}
```

---

### 1.8 Supprimer le compte

#### Informations générales
- **URL:** `/api/v1/delete-account`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Paramètres requis
```json
{
  "password": "string"
}
```

---

## 2. Configuration

### 2.1 Configuration globale de l'app

#### Informations générales
- **URL:** `/api/v1/config`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère toutes les données de configuration nécessaires au démarrage de l'application : quiz, structures d'aide, FAQs, thèmes du forum, mots censurés, et informations générales.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/config
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "informations": {
      "id": 1,
      "image": "url_image",
      "rendez_vous": "texte",
      "structure_url": "url",
      "splash": "url_splash"
    },
    "quiz": [
      {
        "id": 1,
        "name": "Question du quiz",
        "reponse": "Réponse correcte",
        "option1": "Option 1",
        "option2": "Option 2",
        "thematique_id": 1,
        "thematique": "Nom de la thématique"
      }
    ],
    "conseils": [
      {
        "id": 1,
        "message": "Conseil du jour"
      }
    ],
    "structures": [
      {
        "id": 1,
        "name": "Nom de la structure",
        "description": "Description",
        "latitude": "9.5092",
        "longitude": "-13.7122",
        "phone": "+224621000000",
        "ville": "Conakry",
        "adresse": "Adresse complète",
        "offre": "Services offerts"
      }
    ],
    "faqs": [
      {
        "id": 1,
        "question": "Question fréquente",
        "reponse": "Réponse détaillée"
      }
    ],
    "themes": [
      {
        "id": 1,
        "name": "Nom du thème"
      }
    ],
    "censures": [
      {
        "id": 1,
        "name": "mot_censuré"
      }
    ]
  }
}
```


---

## 3. Articles

### 3.1 Liste des articles

#### Informations générales
- **URL:** `/api/v1/articles`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère la liste des articles récents, des articles vedettes et la liste des rubriques.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/articles
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "recents": [
      {
        "id": 11,
        "title": "Titre de l'article",
        "description": "Description complète...",
        "slug": "titre-de-larticle",
        "image": null,
        "video_url": null,
        "vedette": 0,
        "rubrique": "Nom de la rubrique",
        "author": "Nom de l'auteur",
        "created_at": "2025-12-03 13:17:31"
      }
    ],
    "vedettes": [
      {
        "id": 1,
        "title": "Article en vedette",
        "description": "Description...",
        "slug": "article-en-vedette",
        "image": "url_image",
        "video_url": null,
        "vedette": 1,
        "rubrique": "Rubrique",
        "author": "Auteur",
        "created_at": "2025-12-03 13:17:31"
      }
    ],
    "rubriques": [
      {
        "id": 1,
        "name": "Nom de la rubrique"
      }
    ]
  }
}
```

---

### 3.2 Détail d'un article

#### Informations générales
- **URL:** `/api/v1/articles/{slug}`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère les détails complets d'un article par son slug.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/articles/les-methodes-de-contraception-adaptees-aux-jeunes
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "id": 4,
    "title": "Les méthodes de contraception adaptées aux jeunes",
    "description": "Description complète de l'article...",
    "slug": "les-methodes-de-contraception-adaptees-aux-jeunes",
    "image": null,
    "video_url": null,
    "vedette": 0,
    "rubrique": "Contraception : Mes Options",
    "author": "Super Admin",
    "created_at": "2025-12-03 13:17:31"
  }
}
```

#### Erreur (404)
```json
{
  "code": 404,
  "message": "Cet article n'existe pas"
}
```

---

### 3.3 Articles par catégorie/rubrique

#### Informations générales
- **URL:** `/api/v1/articles/categorie/{rubriqueId}`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère la liste des articles d'une rubrique spécifique (max 25 articles).

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/articles/categorie/4
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": [
    {
      "id": 5,
      "title": "Titre de l'article",
      "description": "Description...",
      "slug": "titre-de-larticle",
      "image": null,
      "video_url": null,
      "vedette": 0,
      "rubrique": "Contraception : Mes Options",
      "author": "Super Admin",
      "created_at": "2025-12-03 13:17:31"
    }
  ]
}
```

---

## 4. Rubriques

### 4.1 Liste des rubriques avec articles

#### Informations générales
- **URL:** `/api/v1/rubriques`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère toutes les rubriques avec leurs articles associés. Chaque rubrique inclut le nombre d'articles et la liste complète des articles actifs.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/rubriques
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "rubriques": [
      {
        "id": 4,
        "name": "Contraception : Mes Options",
        "articles_count": 2,
        "articles": [
          {
            "id": 5,
            "title": "Le préservatif : mode d'emploi",
            "description": "Description...",
            "slug": "le-preservatif-mode-demploi",
            "image": null,
            "video_url": null,
            "vedette": false,
            "created_at": "2025-12-03T13:17:31.000000Z"
          }
        ]
      }
    ]
  }
}
```

---

## 5. Structures d'Aide

### 5.1 Liste des structures

#### Informations générales
- **URL:** `/api/v1/structures`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère la liste complète de toutes les structures d'aide disponibles (centres de santé, associations, etc.).

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/structures
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "structures": [
      {
        "id": 1,
        "name": "Centre de Santé Jeunes",
        "description": "Description des services",
        "latitude": "9.5092",
        "longitude": "-13.7122",
        "phone": "+224621000000",
        "type": "Centre de santé",
        "icon": "icon_url",
        "ville": "Conakry",
        "adresse": "Adresse complète"
      }
    ]
  }
}
```

---

### 5.2 Structures à proximité

#### Informations générales
- **URL:** `/api/v1/structures/nearby`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère les structures d'aide à proximité d'une position géographique donnée. Utilise la formule de Haversine pour calculer les distances.

#### Paramètres requis
| Paramètre | Type | Obligatoire | Description |
|-----------|------|-------------|-------------|
| `lat` | float | ✅ | Latitude (-90 à 90) |
| `lng` | float | ✅ | Longitude (-180 à 180) |
| `radius` | integer | ❌ | Rayon de recherche en km (défaut: 50, max: 200) |
| `type_structure_id` | integer | ❌ | Filtrer par type de structure |

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/structures/nearby?lat=9.5092&lng=-13.7122&radius=10
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "structures": [
      {
        "id": 1,
        "name": "Centre de Santé",
        "description": "Description",
        "latitude": "9.5092",
        "longitude": "-13.7122",
        "phone": "+224621000000",
        "adresse": "Adresse",
        "offre": "Services offerts",
        "type": "Centre de santé",
        "icon": "icon_url",
        "ville": "Conakry",
        "distance": 2.45
      }
    ],
    "search_params": {
      "latitude": 9.5092,
      "longitude": -13.7122,
      "radius_km": 10,
      "type_structure_id": null
    },
    "count": 5
  }
}
```

---

## 6. Forum

### 6.1 Liste des messages du forum

#### Informations générales
- **URL:** `/api/v1/forum`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère tous les messages et chats du forum.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/forum
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "messages": [
      {
        "id": 1,
        "question": "Titre du message",
        "utilisateurId": 1,
        "utilisateur": "Fatou",
        "themeId": 1,
        "theme": "Contraception",
        "date": "2025-12-03 13:17:31",
        "status": true
      }
    ],
    "chats": [
      {
        "id": 1,
        "message": "Réponse au message",
        "messageId": 1,
        "utilisateurId": 2,
        "utilisateurName": "Amadou",
        "date": "2025-12-03 14:20:00",
        "status": true,
        "anomyme": false
      }
    ]
  }
}
```

---

### 6.2 Créer un message (question)

#### Informations générales
- **URL:** `/api/v1/message-sync` ou `/api/v1/forum/message/sync`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Créer un nouveau message/question sur le forum.

#### Paramètres requis
```json
{
  "theme_id": "integer",
  "utilisateur_id": "integer",
  "question": "string"
}
```

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/message-sync
Content-Type: application/json

{
  "theme_id": 1,
  "utilisateur_id": 5,
  "question": "Comment parler de contraception avec mes parents ?"
}
```

#### Réponse réussie
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "id": 15,
    "utilisateur_id": 5,
    "theme_id": 1,
    "question": "Comment parler de contraception avec mes parents ?",
    "status": true,
    "created_at": "2025-12-04T10:30:00.000000Z"
  }
}
```

---

### 6.3 Répondre à un message (chat)

#### Informations générales
- **URL:** `/api/v1/chat-sync` ou `/api/v1/forum/chat/sync`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Répondre à un message existant sur le forum.

#### Paramètres requis
```json
{
  "message_id": "integer",
  "utilisateur_id": "integer",
  "message": "string",
  "anomyme": "boolean"
}
```

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/chat-sync
Content-Type: application/json

{
  "message_id": 15,
  "utilisateur_id": 7,
  "message": "Tu peux commencer par...",
  "anomyme": false
}
```

#### Validation
- Le message est filtré contre les mots censurés
- Erreur si le message contient un mot censuré

---

### 6.4 Supprimer un chat

#### Informations générales
- **URL:** `/api/v1/chat-delete/{id}` ou `/api/v1/forum/chat/{id}`
- **Méthode:** `POST` ou `DELETE`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Supprimer une réponse (chat) du forum.

#### Exemple de requête
```bash
DELETE https://gquiose.mbayedione.xyz/api/v1/forum/chat/25
```

---

## 7. Vidéos

### 7.1 Liste des vidéos

#### Informations générales
- **URL:** `/api/v1/videos`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère la liste de toutes les vidéos éducatives disponibles.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/videos
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "videos": [
      {
        "id": 1,
        "title": "Titre de la vidéo",
        "description": "Description",
        "url": "https://youtube.com/watch?v=...",
        "thumbnail": "url_miniature",
        "duration": "10:30",
        "created_at": "2025-12-03T13:17:31.000000Z"
      }
    ]
  }
}
```

---

## 8. Alertes VBG (Violence Basée sur le Genre)

### 8.1 Liste des alertes

#### Informations générales
- **URL:** `/api/v1/alertes`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère la liste de toutes les alertes ou les alertes d'un utilisateur spécifique. Chaque alerte inclut toutes les informations organisées par sections : utilisateur associé, informations générales, violences numériques, détails de l'incident, preuves & conseils, et consentement.

#### Paramètres optionnels
| Paramètre | Type | Obligatoire | Description |
|-----------|------|-------------|-------------|
| `user_id` | integer | ❌ | Filtrer les alertes par utilisateur |

#### Exemples de requêtes

**Toutes les alertes :**
```bash
GET https://gquiose.mbayedione.xyz/api/v1/alertes
```

**Alertes d'un utilisateur spécifique :**
```bash
GET https://gquiose.mbayedione.xyz/api/v1/alertes?user_id=5
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "alertes": [
      {
        "id": 1,
        "ref": "ALRT-2025-001",
        "etat": "Non approuvée",
        "numero_suivi": "VBG-2025-000001",
        "created_at": "2025-12-03T13:17:31.000000Z",

        "utilisateur": {
          "id": 1,
          "name": "Fatou Diallo",
          "email": "fatou.diallo@test.gn",
          "phone": "+224621234567"
        },

        "informations_generales": {
          "type_alerte": "Diffusion Images Intimes (Revenge Porn)",
          "description": "Description de l'incident...",
          "ville": "Conakry",
          "latitude": 9.6412,
          "longitude": -13.5784,
          "precision_localisation": "approximative",
          "rayon_approximation_km": null,
          "quartier": null,
          "commune": null
        },

        "violences_numeriques": {
          "plateformes": ["Facebook", "WhatsApp", "Instagram"],
          "nature_contenu": ["Photos intimes", "Messages privés"],
          "urls_problematiques": "https://facebook.com/[profil-agresseur]",
          "comptes_impliques": "@agresseur123",
          "frequence_incidents": "continu"
        },

        "details_incident": {
          "date_incident": "2025-11-28T00:00:00.000000Z",
          "heure_incident": "2025-12-04T14:20:00.000000Z",
          "relation_agresseur": "ex_partenaire",
          "impact": ["Humiliation", "Dépression", "Anxiété"]
        },

        "preuves_conseils": {
          "preuves": [
            {
              "path": "preuves/screenshot1.jpg",
              "type": "image"
            }
          ],
          "conseils_securite": "1. Documentez tout...",
          "conseils_lus": true
        },

        "consentement": {
          "anonymat_souhaite": false,
          "consentement_transmission": true
        }
      }
    ],
    "total": 1
  }
}
```


---

### 8.2 Options du workflow

#### Informations générales
- **URL:** `/api/v1/alertes/workflow-options`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère toutes les options nécessaires pour remplir le formulaire d'alerte VBG (types de violence, lieux, etc.).

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/alertes/workflow-options
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "types_alerte": [
      {
        "id": 1,
        "nom": "Violence physique",
        "description": "Description du type"
      }
    ],
    "types_violence_numerique": [
      {
        "id": 1,
        "nom": "Cyberharcèlement",
        "description": "Description"
      }
    ],
    "lieux": [
      {
        "id": 1,
        "nom": "À la maison"
      }
    ],
    "relations_agresseur": [
      {
        "id": 1,
        "nom": "Conjoint/Partenaire"
      }
    ]
  }
}
```

---

### 8.3 Workflow multi-étapes

Les alertes VBG utilisent un workflow en 6 étapes :

#### Étape 1 : Informations de base
- **URL:** `/api/v1/alertes/step1`
- **Méthode:** `POST`
- **Paramètres:** `type_alerte_id`, `description`, etc.

#### Étape 2 : Détails de la violence
- **URL:** `/api/v1/alertes/step2`
- **Méthode:** `POST`
- **Paramètres:** `alerte_id`, `lieu_id`, `date_incident`, etc.

#### Étape 3 : Informations sur l'agresseur
- **URL:** `/api/v1/alertes/step3`
- **Méthode:** `POST`
- **Paramètres:** `alerte_id`, `relation_agresseur_id`, `preuves[]`, etc.

#### Étape 4 : Affichage des conseils de sécurité
- **URL:** `/api/v1/alertes/step4/{alerte_id}`
- **Méthode:** `GET`
- **Retourne:** Conseils de sécurité personnalisés

#### Étape 5 : Structures recommandées
- **URL:** `/api/v1/alertes/step5/{alerte_id}`
- **Méthode:** `GET`
- **Retourne:** Structures d'aide à proximité

#### Étape 6 : Confirmation
- **URL:** `/api/v1/alertes/step6`
- **Méthode:** `POST`
- **Finalise l'alerte**

---

### 8.4 Télécharger une preuve (sécurisé)

#### Informations générales
- **URL:** `/api/v1/alertes/{alerte}/evidence/{index}`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** Fichier (image, vidéo, audio)

#### Description
Télécharger une preuve chiffrée associée à une alerte. Seul l'auteur de l'alerte ou un admin peut accéder aux preuves.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/alertes/15/evidence/0
Authorization: Bearer {token}
```

---

### 8.5 Marquer les conseils comme lus

#### Informations générales
- **URL:** `/api/v1/alertes/{alerte}/mark-advice-read`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Marquer les conseils de sécurité comme lus.

---

### 8.6 Synchroniser une alerte (ancienne méthode)

#### Informations générales
- **URL:** `/api/v1/alert-sync`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

**Note:** Cette méthode est dépréciée. Utilisez le workflow multi-étapes à la place.

---

## 9. Quiz

### 9.1 Synchroniser les réponses du quiz

#### Informations générales
- **URL:** `/api/v1/sync-quiz`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Envoyer les réponses d'un quiz et obtenir le score.

#### Paramètres requis
```json
{
  "utilisateur_id": "integer",
  "reponses": [
    {
      "question_id": "integer",
      "reponse": "string"
    }
  ]
}
```

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/sync-quiz
Content-Type: application/json

{
  "utilisateur_id": 5,
  "reponses": [
    {
      "question_id": 1,
      "reponse": "Réponse A"
    },
    {
      "question_id": 2,
      "reponse": "Réponse B"
    }
  ]
}
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "score": 8,
    "total": 10,
    "pourcentage": 80,
    "resultat": "Bien ! Continue comme ça"
  }
}
```

---

## 10. Évaluations

**Note:** Tous les endpoints d'évaluations nécessitent une authentification.

### 10.1 Obtenir les questions

#### Informations générales
- **URL:** `/api/v1/evaluations/questions`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Récupère les questions d'évaluation selon le contexte (générale, post-VBG, etc.).

#### Paramètres optionnels
| Paramètre | Type | Description |
|-----------|------|-------------|
| `contexte` | string | Type d'évaluation (défaut: "generale") |
| `previous_answers` | array | Réponses précédentes pour questions conditionnelles |

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/evaluations/questions?contexte=generale
Authorization: Bearer {token}
```

#### Structure de la réponse
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "question": "Comment vous sentez-vous aujourd'hui ?",
      "type": "choix_multiple",
      "formulaire_type": "generale",
      "options": ["Très bien", "Bien", "Neutre", "Mal", "Très mal"],
      "obligatoire": true,
      "has_condition": false,
      "condition": null
    }
  ]
}
```

---

### 10.2 Soumettre une évaluation

#### Informations générales
- **URL:** `/api/v1/evaluations/submit`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Soumettre les réponses à une évaluation.

#### Paramètres requis
```json
{
  "formulaire_type": "string",
  "reponses": [
    {
      "question_id": "integer",
      "reponse": "string ou array"
    }
  ]
}
```

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/evaluations/submit
Authorization: Bearer {token}
Content-Type: application/json

{
  "formulaire_type": "generale",
  "reponses": [
    {
      "question_id": 1,
      "reponse": "Bien"
    },
    {
      "question_id": 2,
      "reponse": ["Option A", "Option C"]
    }
  ]
}
```

---

### 10.3 Statistiques de l'utilisateur

#### Informations générales
- **URL:** `/api/v1/evaluations/statistics`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Récupère les statistiques d'évaluations de l'utilisateur connecté.

---

### 10.4 Évaluations d'un utilisateur

#### Informations générales
- **URL:** `/api/v1/evaluations/user/{userId}`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Récupère l'historique des évaluations d'un utilisateur spécifique.

---

### 10.5 Statistiques globales

#### Informations générales
- **URL:** `/api/v1/evaluations/stats/global`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Récupère les statistiques globales de toutes les évaluations.

---

### 10.6 Statistiques par question

#### Informations générales
- **URL:** `/api/v1/evaluations/stats/question/{questionId}`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Récupère les statistiques détaillées pour une question spécifique.

---

### 10.7 Statistiques par formulaire

#### Informations générales
- **URL:** `/api/v1/evaluations/stats/formulaire/{formulaireType}`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Récupère les statistiques pour un type de formulaire (generale, post-vbg, etc.).

---

### 10.8 Rapport détaillé

#### Informations générales
- **URL:** `/api/v1/evaluations/stats/report`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Génère un rapport détaillé avec graphiques et analyses.

---

## 11. Cycle Menstruel

### 11.1 Démarrer une période

#### Informations générales
- **URL:** `/api/v1/cycle/start`
- **Méthode:** `POST`
- **Authentification:** Non requise (mais user_id requis)
- **Format de réponse:** JSON

#### Description
Enregistrer le début d'une nouvelle période menstruelle.

#### Paramètres requis
```json
{
  "user_id": "integer",
  "start_date": "date (YYYY-MM-DD)",
  "flow_intensity": "string (leger/moyen/abondant)"
}
```

#### Exemple de requête
```bash
POST https://gquiose.mbayedione.xyz/api/v1/cycle/start
Content-Type: application/json

{
  "user_id": 5,
  "start_date": "2025-12-01",
  "flow_intensity": "moyen"
}
```

---

### 11.2 Terminer une période

#### Informations générales
- **URL:** `/api/v1/cycle/end-period`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Marquer la fin de la période menstruelle actuelle.

#### Paramètres requis
```json
{
  "cycle_id": "integer",
  "end_date": "date (YYYY-MM-DD)"
}
```

---

### 11.3 Enregistrer des symptômes

#### Informations générales
- **URL:** `/api/v1/cycle/log-symptoms`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Enregistrer les symptômes ressentis (douleurs, humeur, etc.).

#### Paramètres requis
```json
{
  "cycle_id": "integer",
  "symptom_date": "date",
  "symptoms": ["crampes", "fatigue", "maux_de_tete"],
  "mood": "string",
  "notes": "string (optionnel)"
}
```

---

### 11.4 Cycle actuel

#### Informations générales
- **URL:** `/api/v1/cycle/current/{user_id}`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère les informations du cycle menstruel en cours.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/cycle/current/5
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "cycle": {
      "id": 15,
      "user_id": 5,
      "start_date": "2025-12-01",
      "end_date": null,
      "cycle_length": null,
      "period_length": 5,
      "flow_intensity": "moyen"
    },
    "next_period_prediction": "2025-12-29",
    "fertile_window": {
      "start": "2025-12-13",
      "end": "2025-12-18"
    },
    "symptoms_today": ["crampes", "fatigue"]
  }
}
```

---

### 11.5 Historique du cycle

#### Informations générales
- **URL:** `/api/v1/cycle/history/{user_id}`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère l'historique complet des cycles menstruels.

---

### 11.6 Symptômes

#### Informations générales
- **URL:** `/api/v1/cycle/symptoms/{user_id}`
- **Méthode:** `GET`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Récupère l'historique des symptômes enregistrés.

---

### 11.7 Paramètres du cycle

#### Informations générales
- **URL:** `/api/v1/cycle/settings`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Configurer les paramètres personnels du cycle (durée moyenne, durée des règles, etc.).

#### Paramètres
```json
{
  "user_id": "integer",
  "average_cycle_length": "integer (défaut: 28)",
  "average_period_length": "integer (défaut: 5)",
  "enable_predictions": "boolean"
}
```

---

### 11.8 Rappels

#### Informations générales
- **URL:** `/api/v1/cycle/reminders`
- **Méthode:** `POST`
- **Authentification:** Non requise
- **Format de réponse:** JSON

#### Description
Configurer les rappels/notifications pour le cycle menstruel.

#### Paramètres
```json
{
  "user_id": "integer",
  "remind_before_period": "integer (jours)",
  "remind_fertile_window": "boolean",
  "notification_time": "time (HH:MM)"
}
```

---

## 12. Notifications

**Note:** Tous les endpoints de notifications nécessitent une authentification.

### Configuration Serveur Requise

Le système de notifications push nécessite la configuration des variables d'environnement suivantes dans le fichier `.env`:

#### FCM (Firebase Cloud Messaging) - Android
```env
# Push Notifications - FCM (Android)
FCM_SERVER_KEY=your_firebase_server_key_here
```

#### APNs (Apple Push Notification service) - iOS
```env
# Push Notifications - APNs (iOS)
APNS_KEY_ID=your_key_id
APNS_TEAM_ID=your_team_id
APNS_BUNDLE_ID=com.gquiose.app
APNS_ENVIRONMENT=production
```

---

### 12.1 Enregistrer un token push

#### Informations générales
- **URL:** `/api/v1/notifications/register-token`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Enregistrer le token de notification push (FCM pour Android, APNS pour iOS).

#### Paramètres requis
```json
{
  "token": "string",
  "platform": "string (ios/android)",
  "device_id": "string"
}
```

---

### 12.2 Préférences de notification

#### 12.2.1 Mettre à jour les préférences

#### Informations générales
- **URL:** `/api/v1/notifications/preferences`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Paramètres
```json
{
  "enable_forum_replies": "boolean",
  "enable_cycle_reminders": "boolean",
  "enable_new_articles": "boolean",
  "enable_evaluation_reminders": "boolean"
}
```

#### 12.2.2 Obtenir les préférences

#### Informations générales
- **URL:** `/api/v1/notifications/preferences/{userId}`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

---

### 12.3 Marquer comme ouverte

#### Informations générales
- **URL:** `/api/v1/notifications/opened` ou `/api/v1/notifications/{notificationId}/opened`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Enregistrer qu'une notification a été ouverte.

---

### 12.4 Marquer comme cliquée

#### Informations générales
- **URL:** `/api/v1/notifications/clicked` ou `/api/v1/notifications/{notificationId}/clicked`
- **Méthode:** `POST`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Enregistrer qu'une notification a été cliquée.

---

### 12.5 Historique des notifications

#### Informations générales
- **URL:** `/api/v1/notifications/history`
- **Méthode:** `GET`
- **Authentification:** ✅ Requise
- **Format de réponse:** JSON

#### Description
Récupère l'historique complet des notifications envoyées à l'utilisateur.

#### Exemple de requête
```bash
GET https://gquiose.mbayedione.xyz/api/v1/notifications/history
Authorization: Bearer {token}
```

#### Structure de la réponse
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    "notifications": [
      {
        "id": 1,
        "title": "Nouveau message",
        "body": "Tu as reçu une réponse",
        "type": "forum_reply",
        "sent_at": "2025-12-04T10:30:00.000000Z",
        "opened_at": "2025-12-04T10:35:00.000000Z",
        "clicked": true
      }
    ]
  }
}
```

---

## Codes de Statut HTTP

| Code | Signification | Description |
|------|---------------|-------------|
| 200 | OK | Requête réussie |
| 201 | Created | Ressource créée avec succès |
| 400 | Bad Request | Paramètres invalides ou manquants |
| 401 | Unauthorized | Token manquant ou invalide |
| 403 | Forbidden | Accès refusé (permissions insuffisantes) |
| 404 | Not Found | Ressource non trouvée |
| 422 | Unprocessable Entity | Erreur de validation |
| 429 | Too Many Requests | Limite de taux dépassée |
| 500 | Internal Server Error | Erreur serveur |

---

## Format Standard des Réponses

### Réponse réussie
```json
{
  "code": 200,
  "message": "OK",
  "data": {
    // Données de la réponse
  }
}
```

### Réponse d'erreur
```json
{
  "code": 400,
  "message": "Description de l'erreur",
  "errors": {
    "field_name": ["Message d'erreur détaillé"]
  }
}
```

---

## Rate Limiting

- **Limite:** 60 requêtes par minute par IP (authentifié)
- **Limite:** 30 requêtes par minute par IP (non authentifié)
- **Header de réponse:** `X-RateLimit-Limit`, `X-RateLimit-Remaining`

---

## Authentification

### Bearer Token
Pour les endpoints protégés, inclure le header :
```
Authorization: Bearer {votre_token}
```

### Durée de validité
- **Token:** 30 jours (2592000 secondes)
- **Rafraîchissement:** Reconnecter pour obtenir un nouveau token

---

## Support et Contact

- **Documentation GitHub:** https://github.com/mbayedione10/Gquiose-web
- **Issues:** https://github.com/mbayedione10/Gquiose-web/issues

---

## Changelog

### Version 1.0 (2025-12-04)
- ✅ Documentation complète de tous les endpoints
- ✅ Ajout de l'endpoint `/api/v1/rubriques` - Liste des rubriques avec articles associés
- ✅ Ajout de l'endpoint `/api/v1/alertes` - Liste de toutes les alertes avec informations complètes
- ✅ Correction du filtre vedette dans `/api/v1/articles/{slug}`
- ✅ Correction des erreurs SQL ambiguës
- ✅ Standardisation du namespace `App\Services`

---

**Dernière mise à jour:** 2025-12-04
