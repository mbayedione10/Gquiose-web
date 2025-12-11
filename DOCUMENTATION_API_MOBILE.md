# Documentation API - Application Mobile Gquiose

## Table des matières
1. [Vue d'ensemble](#vue-densemble)
2. [Informations générales](#informations-générales)
3. [Authentification](#authentification)
4. [Modules de l'application](#modules-de-lapplication)
5. [Flux utilisateur](#flux-utilisateur)
6. [Gestion des fichiers](#gestion-des-fichiers)
7. [Notifications push](#notifications-push)
8. [Sécurité et confidentialité](#sécurité-et-confidentialité)

---

## Vue d'ensemble

### À propos de l'API

L'API Gquiose est une API REST construite avec Laravel qui fournit toutes les fonctionnalités pour l'application mobile de santé reproductive et lutte contre les violences basées sur le genre (VBG).

**URL de base:** `https://gquiose.mbayedione.xyz/api/v1`

**Format:** Toutes les requêtes et réponses sont en JSON

**Authentification:** Laravel Sanctum (Bearer Token)

---

## Informations générales

### Format des réponses

Toutes les réponses API suivent ce format standard :

**Succès:**
```json
{
  "success": true,
  "data": {
    // Données de la réponse
  },
  "message": "Message de succès (optionnel)"
}
```

**Erreur:**
```json
{
  "success": false,
  "message": "Message d'erreur",
  "errors": {
    "champ": ["Erreur de validation"]
  }
}
```

### Codes HTTP

- `200` - Succès
- `201` - Créé avec succès
- `400` - Mauvaise requête
- `401` - Non authentifié (token invalide ou expiré)
- `403` - Non autorisé
- `404` - Ressource introuvable
- `409` - Conflit (ex: email déjà utilisé)
- `422` - Erreur de validation
- `429` - Trop de tentatives
- `500` - Erreur serveur

### Headers requis

**Pour toutes les requêtes:**
```
Content-Type: application/json
Accept: application/json
```

**Pour les requêtes authentifiées:**
```
Authorization: Bearer {le_token_reçu_lors_de_la_connexion}
```

---

## Authentification

### Concept général

L'application utilise un système d'authentification par token (Laravel Sanctum). Voici le processus :

1. **Inscription** → L'utilisateur reçoit un code de vérification
2. **Confirmation** → L'utilisateur valide le code et reçoit un token
3. **Utilisation** → Le token est envoyé dans chaque requête authentifiée
4. **Expiration** → Le token expire après 30 jours

### 1. Inscription

L'application supporte 3 types d'inscription :

#### A. Par numéro de téléphone

**Endpoint:** `POST /register`

**Données à envoyer:**
```json
{
  "type": "phone",
  "phone": "+224XXXXXXXXX",
  "password": "motdepasse",
  "password_confirmation": "motdepasse",
  "sexe": "F",
  "age": 25,
  "nom": "Diallo",
  "prenom": "Aissatou",
  "platform": "android",
  "fcm_token": "token_firebase",
  "ville_id": 1
}
```

**Important:**
- Format téléphone: `+224` suivi de 9 chiffres
- `sexe`: `M`, `F` ou `Autre`
- `age`: minimum 13 ans
- `platform`: `android` ou `ios`
- `ville_id`: optionnel

**Ce qui se passe:**
1. Un code à 4 chiffres est généré
2. Un SMS est envoyé au numéro
3. L'utilisateur a 10 minutes pour confirmer le code
4. Maximum 3 tentatives de validation

#### B. Par email

**Endpoint:** `POST /register`

**Données à envoyer:**
```json
{
  "type": "email",
  "email": "aissatou@example.com",
  "password": "motdepasse",
  "password_confirmation": "motdepasse",
  "sexe": "F",
  "age": 25,
  "platform": "android",
  "fcm_token": "token_firebase"
}
```

**Ce qui se passe:**
1. Un code à 4 chiffres est envoyé par email
2. L'utilisateur a 10 minutes pour confirmer
3. Maximum 3 tentatives

#### C. Par réseaux sociaux (Google, Facebook, Apple)

**Endpoint:** `POST /register`

**Données à envoyer:**
```json
{
  "type": "social",
  "provider": "google",
  "access_token": "token_google",
  "sexe": "F",
  "age": 25,
  "platform": "android",
  "fcm_token": "token_firebase"
}
```

**Providers disponibles:** `google`, `facebook`, `apple`

**Ce qui se passe:**
1. Le token est vérifié auprès du provider
2. Si l'utilisateur existe déjà, il est connecté directement
3. Sinon, un nouveau compte est créé et activé automatiquement
4. Un token d'authentification est retourné

### 2. Confirmation du code

**Endpoint:** `POST /code-confirmation`

**Données à envoyer:**
```json
{
  "identifier": "+224XXXXXXXXX",
  "code": "1234"
}
```

**Important:**
- `identifier` peut être un email ou un téléphone
- Le code expire après 10 minutes
- Maximum 3 tentatives, puis blocage de 24h

**Ce qui est reçu:**
```json
{
  "success": true,
  "data": {
    "utilisateur": {
      "id": 1,
      "nom": "Diallo",
      "prenom": "Aissatou",
      "email": "aissatou@example.com",
      "sexe": "F"
    },
    "token": "1|abcdef123456...",
    "token_type": "Bearer",
    "expires_in": 2592000
  },
  "message": "Compte activé avec succès"
}
```

**À faire après:**
- Stocker le `token` de manière sécurisée (FlutterSecureStorage)
- Stocker les infos utilisateur
- Rediriger vers l'accueil

### 3. Connexion

**Endpoint:** `POST /login`

**Données à envoyer:**
```json
{
  "identifier": "aissatou@example.com",
  "password": "motdepasse",
  "platform": "android",
  "fcm_token": "token_firebase"
}
```

**Important:**
- `identifier` accepte email OU téléphone
- Le système détecte automatiquement le type
- Le compte doit être activé (code confirmé)

### 4. Déconnexion

**Endpoint:** `POST /logout`

**Headers:** Authorization Bearer token requis

**À faire:**
- Supprimer le token stocké localement
- Effacer les données utilisateur
- Rediriger vers la page de connexion

### 5. Mot de passe oublié

**Étape 1 - Demander le code:**

**Endpoint:** `POST /send-password-reset-code`
```json
{
  "identifier": "aissatou@example.com"
}
```

**Étape 2 - Réinitialiser:**

**Endpoint:** `POST /reset-password`
```json
{
  "identifier": "aissatou@example.com",
  "code": "1234",
  "password": "nouveaumotdepasse",
  "password_confirmation": "nouveaumotdepasse"
}
```

### 6. Gestion du profil

#### Mettre à jour le profil

**Endpoint:** `POST /update-profile`

**Type de requête:** `multipart/form-data` (pour la photo)

**Données:**
```
nom: Diallo
prenom: Aissatou
sexe: F
dob: 1998-05-15
ville_id: 1
photo: (fichier image)
```

#### Changer le mot de passe

**Endpoint:** `POST /change-password`
```json
{
  "old_password": "ancienmdp",
  "new_password": "nouveaumdp",
  "new_password_confirmation": "nouveaumdp"
}
```

#### Supprimer le compte

**Endpoint:** `POST /delete-account`
```json
{
  "password": "motdepasse"
}
```

---

## Modules de l'application

### 1. Articles éducatifs

#### Lister les articles

**Endpoint:** `GET /articles`

**Paramètres optionnels:**
- `page`: Numéro de page
- `per_page`: Nombre d'articles par page
- `rubrique_id`: Filtrer par rubrique

**Exemple:** `/articles?page=1&per_page=10&rubrique_id=2`

**Utilisation:**
- Afficher les articles par rubrique (contraception, VBG, santé, etc.)
- Pagination automatique
- Images optimisées pour mobile

#### Voir un article

**Endpoint:** `GET /articles/{slug}`

**Exemple:** `/articles/les-methodes-de-contraception`

**Contenu reçu:**
- Titre, contenu HTML
- Image de couverture
- Rubrique
- Date de publication

### 2. Quiz éducatifs

#### Synchroniser les réponses

**Endpoint:** `POST /sync-quiz`

**Données:**
```json
{
  "user_id": 1,
  "quizzes": [
    {
      "question_id": 1,
      "reponse_id": 2,
      "is_correct": true,
      "completed_at": "2025-11-28T10:30:00Z"
    }
  ]
}
```

**Utilisation:**
- Envoyer les réponses après chaque quiz
- Synchroniser les scores
- Suivre la progression de l'utilisateur

### 3. Structures d'aide

#### Liste des structures

**Endpoint:** `GET /structures`

**Paramètres:**
- `type_structure_id`: Type (hôpital, centre d'écoute, etc.)
- `ville_id`: Ville

**Types de structures:**
- Centres de santé
- Hôpitaux
- Centres d'écoute VBG
- Postes de police
- Associations

#### Structures à proximité (géolocalisation)

**Endpoint:** `GET /structures/nearby`

**Paramètres:**
- `latitude`: Coordonnée GPS
- `longitude`: Coordonnée GPS
- `radius`: Rayon en km (défaut: 50km)

**Exemple:** `/structures/nearby?latitude=9.5092&longitude=-13.7122&radius=10`

**Utilisation:**
- Demander la permission de localisation à l'utilisateur
- Obtenir les coordonnées GPS
- Afficher les structures sur une carte
- Trier par distance

### 4. Forum communautaire

#### Récupérer les discussions

**Endpoint:** `GET /forum`

**Paramètres:**
- `theme_id`: Filtrer par thème

**Utilisation:**
- Afficher les chats par thème
- Système de discussion anonyme
- Modération automatique des contenus sensibles

#### Envoyer un message

**Endpoint:** `POST /message-sync`

```json
{
  "user_id": 1,
  "chat_id": 5,
  "contenu": "Mon message",
  "created_at": "2025-11-28T10:00:00Z"
}
```

#### Créer un nouveau chat

**Endpoint:** `POST /chat-sync`

```json
{
  "user_id": 1,
  "theme_id": 2,
  "titre": "Besoin de conseils",
  "last_message_at": "2025-11-28T10:00:00Z"
}
```

### 5. Vidéos éducatives

**Endpoint:** `GET /videos`

**Paramètres:**
- `category`: Catégorie de vidéo

**Contenu:**
- Lien YouTube/Vimeo
- Miniature
- Durée
- Description

---

## Flux utilisateur

### Signalement d'une violence (Workflow VBG)

Le signalement se fait en **6 étapes** pour une meilleure expérience utilisateur et collecte progressive des informations.

#### Vue d'ensemble du workflow

```
Étape 1: Type de violence
    ↓
Étape 2: Détails violence numérique (si applicable)
    ↓
Étape 3: Description et preuves
    ↓
Étape 4: Conseils de sécurité personnalisés
    ↓
Étape 5: Ressources disponibles
    ↓
Étape 6: Consentement et soumission
```

#### Avant de commencer

**Endpoint:** `GET /alertes/workflow-options`

**Utilisation:** Récupérer toutes les options pour les formulaires

**Reçu:**
- Types d'alertes
- Sous-types de violence numérique
- Plateformes (Facebook, Instagram, etc.)
- Natures de contenu
- Relations avec l'agresseur
- Impacts possibles

**À faire:**
- Charger ces options au démarrage de l'écran
- Les stocker pour les étapes suivantes
- Permettre la sélection multiple quand nécessaire

---

#### Étape 1: Quel type de violence ?

**Endpoint:** `POST /alertes/step1`

**Données:**
```json
{
  "utilisateur_id": 1,
  "type_alerte_id": 2,
  "sous_type_violence_numerique_id": 3
}
```

**Types disponibles:**
- Violence physique
- Violence psychologique
- Violence sexuelle
- Violence économique
- **Violence numérique** (avec sous-types)
- Mariage forcé
- Mutilations génitales féminines

**Violence numérique - Sous-types:**
- Harcèlement en ligne
- Cyberharcèlement
- Diffusion d'images intimes
- Usurpation d'identité
- Chantage en ligne
- Revenge porn

**Réponse:**
```json
{
  "alerte_id": 10,
  "ref": "ALRT-2025-000010",
  "numero_suivi": "VBG-2025-000010",
  "next_step": "step2"
}
```

**Important:**
- Sauvegarder `alerte_id` pour les étapes suivantes
- Afficher le `numero_suivi` à l'utilisateur
- L'alerte est créée en statut "Brouillon"

---

#### Étape 2: Détails violence numérique (conditionnelle)

**⚠️ Cette étape est affichée UNIQUEMENT si violence numérique sélectionnée**

**Endpoint:** `POST /alertes/step2`

**Données:**
```json
{
  "alerte_id": 10,
  "plateformes": ["Facebook", "WhatsApp"],
  "nature_contenu": ["Menaces", "Images intimes"],
  "urls_problematiques": "https://facebook.com/post/123",
  "comptes_impliques": "@user123, @user456"
}
```

**Plateformes:**
- Facebook
- Instagram
- WhatsApp
- TikTok
- Snapchat
- Twitter/X
- Telegram
- Autre

**Nature du contenu:**
- Menaces
- Insultes
- Images intimes non consensuelles
- Fausses informations
- Usurpation d'identité
- Harcèlement

**UI recommandée:**
- Sélection multiple pour plateformes et nature
- Champ texte pour URLs
- Champ texte pour comptes

---

#### Étape 3: Description et preuves

**Endpoint:** `POST /alertes/step3`

**Type:** `multipart/form-data` (pour les fichiers)

**Données:**
```
alerte_id: 10
description: "Description détaillée de l'incident..."
date_incident: 2025-11-25
heure_incident: 14:30
relation_agresseur: ex_partenaire
frequence_incidents: quotidien
impact[]: stress_anxiete
impact[]: peur_securite
latitude: 9.5092
longitude: -13.7122
ville_id: 1
preuves[0]: (fichier)
preuves[1]: (fichier)
```

**Champs obligatoires:**
- `alerte_id`
- `description` (max 1000 caractères)

**Champs optionnels:**
- `date_incident`: Date de l'incident
- `heure_incident`: Heure (format HH:mm)
- `relation_agresseur`: Relation avec l'agresseur
- `frequence_incidents`: Fréquence
- `impact`: Impacts (tableau)
- `latitude` / `longitude`: GPS
- `ville_id`: Ville
- `preuves`: Fichiers (max 5, max 10MB chacun)

**Relations agresseur:**
- `conjoint`: Conjoint actuel
- `ex_partenaire`: Ex-partenaire
- `famille`: Membre de la famille
- `collegue`: Collègue
- `ami`: Ami
- `connaissance`: Connaissance
- `inconnu`: Inconnu
- `autre`: Autre

**Fréquences:**
- `unique`: Incident unique
- `quotidien`: Tous les jours
- `hebdomadaire`: Chaque semaine
- `mensuel`: Chaque mois
- `continu`: En continu

**Impacts:**
- `stress_anxiete`: Stress et anxiété
- `peur_securite`: Peur pour ma sécurité
- `depression`: Dépression
- `problemes_sommeil`: Problèmes de sommeil
- `isolement_social`: Isolement social
- `difficultes_professionnelles`: Difficultés professionnelles
- `autre`: Autre

**Types de fichiers acceptés:**
- Images: JPG, PNG, JPEG
- Vidéos: MP4, MOV, AVI
- Documents: PDF

**⚠️ Important - Géolocalisation:**

Si l'utilisateur partage sa localisation :
1. Les coordonnées sont **automatiquement anonymisées** côté serveur
2. Un rayon d'approximation de 1-5 km est appliqué
3. Le quartier/commune est conservé mais pas l'adresse exacte
4. Cela protège l'utilisateur tout en permettant l'orientation vers les structures

**⚠️ Important - Preuves:**

Les fichiers uploadés sont :
1. **Chiffrés** automatiquement
2. **Nettoyés** des métadonnées EXIF (GPS, appareil, etc.)
3. Stockés de manière sécurisée
4. Accessibles uniquement par les administrateurs autorisés

---

#### Étape 4: Conseils de sécurité

**Endpoint:** `GET /alertes/step4/{alerte_id}`

**Type:** Écran informatif (pas de soumission)

**Reçu:**
```json
{
  "alerte_id": 10,
  "conseils_securite": [
    {
      "titre": "Protection immédiate",
      "contenu": "Bloquez immédiatement les comptes...",
      "priorite": "haute"
    },
    {
      "titre": "Conservation des preuves",
      "contenu": "Prenez des captures d'écran...",
      "priorite": "haute"
    }
  ]
}
```

**Utilisation:**
- Afficher les conseils de manière claire
- Permettre de lire et relire
- Bouton "Marquer comme lu"
- Option de partager/sauvegarder

**Les conseils sont personnalisés selon:**
- Type de violence
- Contexte (numérique, physique, etc.)
- Fréquence
- Impacts déclarés

---

#### Étape 5: Ressources disponibles

**Endpoint:** `GET /alertes/step5/{alerte_id}`

**Type:** Écran informatif

**Reçu:**
```json
{
  "structures_disponibles": [
    {
      "nom": "Centre d'écoute VBG",
      "adresse": "Conakry, Kaloum",
      "telephone": "+224XXXXXXXXX",
      "distance": 2.5
    }
  ],
  "numeros_urgence": {
    "hotline_vbg": "+224XXXXXXXXX",
    "police": "122",
    "samu": "144"
  },
  "plateformes_signalement": [
    {
      "nom": "Facebook",
      "signalement_url": "https://..."
    }
  ]
}
```

**Utilisation:**
- Afficher les structures sur une carte
- Permettre d'appeler directement les numéros
- Ouvrir les liens de signalement
- Bouton "Obtenir l'itinéraire"

---

#### Étape 6: Consentement et soumission

**Endpoint:** `POST /alertes/step6`

**Données:**
```json
{
  "alerte_id": 10,
  "anonymat_souhaite": true,
  "consentement_transmission": true
}
```

**Important:**
- `anonymat_souhaite`: Si true, les infos personnelles ne sont pas partagées
- `consentement_transmission`: Doit être true (obligatoire pour soumettre)

**Réponse finale:**
```json
{
  "alerte_id": 10,
  "numero_suivi": "VBG-2025-000010",
  "ref": "ALRT-2025-000010",
  "etat": "Non approuvée",
  "message": "Votre signalement a été enregistré avec succès",
  "ressources_urgence": {
    "hotline_vbg": "+224XXXXXXXXX",
    "police": "122"
  }
}
```

**À faire après soumission:**
1. Afficher un message de confirmation
2. Montrer le numéro de suivi
3. Proposer de sauvegarder les ressources
4. Rediriger vers l'accueil

**Statuts de l'alerte:**
- `Brouillon`: En cours de création
- `Non approuvée`: Soumise, en attente de vérification
- `Approuvée`: Validée par l'équipe
- `En cours`: Prise en charge en cours
- `Résolue`: Cas résolu

---

### Suivi du cycle menstruel

#### Démarrer un nouveau cycle

**Endpoint:** `POST /cycle/start`

**Données:**
```json
{
  "user_id": 1,
  "period_start_date": "2025-11-28",
  "flow_intensity": "modere",
  "notes": "Notes optionnelles"
}
```

**Intensités du flux:**
- `leger`: Léger
- `modere`: Modéré
- `abondant`: Abondant

**Réponse:**
```json
{
  "cycle": { ... },
  "predictions": {
    "next_period": "2025-12-26",
    "ovulation": "2025-12-12",
    "fertile_window": {
      "start": "2025-12-10",
      "end": "2025-12-14"
    }
  }
}
```

**À faire:**
- Afficher les prédictions dans un calendrier
- Marquer les dates importantes
- Envoyer des notifications de rappel

#### Terminer la période

**Endpoint:** `POST /cycle/end-period`

**Données:**
```json
{
  "user_id": 1,
  "period_end_date": "2025-12-02"
}
```

#### Enregistrer les symptômes quotidiens

**Endpoint:** `POST /cycle/log-symptoms`

**Données:**
```json
{
  "user_id": 1,
  "symptom_date": "2025-11-28",
  "physical_symptoms": ["crampes", "fatigue"],
  "pain_level": 6,
  "mood": ["irritable", "stresse"],
  "discharge_type": "creamy",
  "temperature": 36.8,
  "sexual_activity": false,
  "contraception_used": true,
  "notes": "Notes personnelles"
}
```

**Symptômes physiques:**
- `crampes`, `fatigue`, `maux_tete`, `nausee`
- `sensibilite_seins`, `ballonnements`, `douleurs_dos`, `acne`

**Humeurs:**
- `joyeuse`, `triste`, `irritable`, `anxieuse`
- `calme`, `energique`, `stresse`

**Types de pertes:**
- `aucune`, `creamy`, `sticky`, `watery`, `egg_white`

**Utilisation:**
- Journal quotidien simple
- Rappels pour enregistrer
- Graphiques d'évolution

#### Consulter le cycle actuel

**Endpoint:** `GET /cycle/current/{user_id}`

**Reçu:**
```json
{
  "cycle": { ... },
  "status": "period",
  "days_until_next_period": 28,
  "in_fertile_window": false
}
```

**Statuts possibles:**
- `period`: En période de règles
- `fertile`: Fenêtre fertile
- `pms`: Syndrome prémenstruel (3 jours avant)
- `normal`: Phase normale

**Utilisation:**
- Afficher le statut actuel
- Code couleur selon la phase
- Countdown jusqu'aux prochaines règles

#### Historique des cycles

**Endpoint:** `GET /cycle/history/{user_id}?limit=6`

**Utilisation:**
- Graphique de régularité
- Statistiques mensuelles
- Évolution des symptômes

#### Paramètres personnalisés

**Endpoint:** `POST /cycle/settings`

**Données:**
```json
{
  "user_id": 1,
  "average_cycle_length": 28,
  "average_period_length": 5,
  "track_temperature": true,
  "track_symptoms": true,
  "track_mood": true,
  "notifications_enabled": true
}
```

#### Configurer les rappels

**Endpoint:** `POST /cycle/reminders`

**Données:**
```json
{
  "user_id": 1,
  "reminders": [
    {
      "type": "period_approaching",
      "time": "09:00",
      "enabled": true,
      "days_before": [1, 2, 3]
    },
    {
      "type": "log_symptoms",
      "time": "20:00",
      "enabled": true
    }
  ]
}
```

**Types de rappels:**
- `period_approaching`: Règles approchent
- `period_today`: Règles aujourd'hui
- `ovulation_approaching`: Ovulation approche
- `fertile_window`: Fenêtre fertile
- `log_symptoms`: Enregistrer symptômes
- `pill_reminder`: Rappel pilule

---

### Évaluations et feedback

#### Récupérer les questions

**Endpoint:** `GET /evaluations/questions?contexte=quiz`

**Contextes disponibles:**
- `quiz`: Après un quiz
- `article`: Après lecture d'un article
- `structure`: Après consultation de structures
- `generale`: Évaluation générale de l'app
- `alerte`: Après signalement VBG

**Reçu:**
```json
{
  "data": [
    {
      "id": 1,
      "question": "Comment évaluez-vous cette fonctionnalité ?",
      "type": "echelle",
      "options": ["1", "2", "3", "4", "5"],
      "obligatoire": true
    }
  ],
  "formulaire_type": "satisfaction_quiz"
}
```

**Types de questions:**
- `echelle`: Échelle de 1 à 5
- `choix_unique`: Une seule réponse
- `choix_multiple`: Plusieurs réponses
- `texte`: Réponse libre

#### Soumettre une évaluation

**Endpoint:** `POST /evaluations/submit`

**Données:**
```json
{
  "user_id": 1,
  "contexte": "quiz",
  "contexte_id": 5,
  "reponses": [
    {
      "question_id": 1,
      "reponse": "5",
      "valeur_numerique": 5
    }
  ],
  "commentaire": "Très utile !"
}
```

**Réponse:**
```json
{
  "success": true,
  "data": {
    "evaluation_id": 123,
    "message": "Évaluation enregistrée avec succès"
  }
}
```

**Utilisation:**
- Afficher après actions clés
- Ne pas être trop intrusif
- Permettre de passer

#### Statistiques d'évaluations

**Statistiques globales:**
**Endpoint:** `GET /evaluations/stats/global`

**Statistiques par question:**
**Endpoint:** `GET /evaluations/stats/question/{questionId}`

**Statistiques par formulaire:**
**Endpoint:** `GET /evaluations/stats/formulaire/{formulaireType}`

**Rapport complet:**
**Endpoint:** `GET /evaluations/stats/report`

**À savoir:**
- Toutes les statistiques nécessitent authentification
- Les données incluent des graphiques prêts à afficher
- Distribution des réponses et moyennes calculées automatiquement
- Évolution mensuelle des scores

---

## Gestion des fichiers

### Upload de fichiers

Pour les preuves VBG ou les photos de profil:

**Headers:**
```
Content-Type: multipart/form-data
Authorization: Bearer {token}
```

**Limites:**
- Taille maximale par fichier: 10 MB
- Nombre maximum de fichiers (preuves): 5
- Formats images: JPG, JPEG, PNG
- Formats vidéos: MP4, MOV, AVI
- Formats documents: PDF

### Téléchargement de preuves

**Endpoint:** `GET /alertes/{alerte}/evidence/{index}`

**Sécurité:**
- Nécessite authentification
- Vérification que l'utilisateur est propriétaire
- Les fichiers sont déchiffrés à la volée
- Pas de cache côté client

---

## Notifications push

### Enregistrer le token FCM

**Endpoint:** `POST /notifications/register-token`

**Données:**
```json
{
  "fcm_token": "token_firebase",
  "platform": "android",
  "device_id": "unique_device_id"
}
```

**À faire au démarrage de l'app:**
1. Obtenir le token FCM
2. L'enregistrer sur le serveur
3. Écouter les rafraîchissements du token
4. Mettre à jour le serveur si changement

### Préférences de notifications

**Endpoint:** `POST /notifications/preferences`

**Données:**
```json
{
  "user_id": 1,
  "notifications_enabled": true,
  "quiz_notifications": true,
  "article_notifications": true,
  "forum_notifications": false,
  "vbg_notifications": true,
  "cycle_notifications": true
}
```

**Récupérer les préférences:**

**Endpoint:** `GET /notifications/preferences/{userId}`

### Types de notifications

L'app peut recevoir:
- **Quiz:** Nouveau quiz disponible
- **Articles:** Nouvel article
- **Forum:** Réponse à votre discussion
- **VBG:** Mise à jour de votre signalement
- **Cycle:** Rappels liés au cycle menstruel
- **Système:** Mises à jour importantes

### Tracking des notifications

#### Marquer comme ouverte

**Endpoint:** `POST /notifications/{notificationId}/opened`

**Données:**
```json
{
  "device_id": "unique_device_id"
}
```

**Utilisation:** Appeler quand l'utilisateur voit la notification

#### Marquer comme cliquée

**Endpoint:** `POST /notifications/{notificationId}/clicked`

**Données:**
```json
{
  "device_id": "unique_device_id",
  "action": "view_article"
}
```

**Utilisation:** Appeler quand l'utilisateur clique sur la notification

### Historique

**Endpoint:** `GET /notifications/history?page=1&per_page=20`

**Nouveaux paramètres de filtrage:**
- `status`: Filtrer par statut (pending, sent, delivered, opened, clicked, failed)
- `platform`: Filtrer par plateforme (android, ios)
- `type`: Filtrer par type de notification

**Réponse enrichie:**
```json
{
  "success": true,
  "data": {
    "notifications": [...],
    "pagination": {...},
    "stats": {
      "total_sent": 50,
      "total_delivered": 48,
      "total_opened": 35,
      "total_clicked": 20,
      "total_failed": 2,
      "open_rate": 0.73,
      "click_rate": 0.42
    }
  }
}
```

**Statuts de notification:**
- `pending`: En attente
- `sent`: Envoyée
- `delivered`: Livrée
- `opened`: Ouverte
- `clicked`: Cliquée
- `failed`: Échec

**Utilisation:**
- Afficher toutes les notifications reçues
- Pagination automatique
- Filtrer par type et statut
- Afficher les statistiques d'engagement

---

## Sécurité et confidentialité

### Protection des données

#### Chiffrement
- Les numéros de téléphone sont chiffrés en base de données
- Les preuves uploadées sont chiffrées
- Les communications utilisent HTTPS

#### Métadonnées
- Les photos sont nettoyées des données EXIF
- Pas de GPS dans les images
- Pas d'informations sur l'appareil photo

#### Géolocalisation
- Anonymisation automatique (rayon de 1-5 km)
- Pas de stockage de l'adresse exacte
- Quartier/commune conservés pour orientation

### Anonymat

#### Signalement anonyme
- Option d'anonymat lors du signalement VBG
- Les informations personnelles ne sont pas partagées
- Seul le numéro de suivi est communiqué

#### Forum
- Discussions anonymes par défaut
- Pas de lien avec le profil utilisateur
- Modération automatique des contenus

### Bonnes pratiques

#### Côté application mobile:

1. **Stockage sécurisé**
   - Utiliser FlutterSecureStorage pour le token
   - Ne jamais stocker les mots de passe
   - Effacer les données sensibles à la déconnexion

2. **Permissions**
   - Demander les permissions uniquement quand nécessaire
   - Expliquer pourquoi (localisation, photos, etc.)
   - Respecter le refus de l'utilisateur

3. **Cache**
   - Ne pas cacher les données sensibles (preuves VBG)
   - Effacer le cache régulièrement
   - Utiliser le cache uniquement pour les données publiques

4. **Erreurs**
   - Ne jamais afficher le token dans les logs
   - Messages d'erreur génériques pour l'utilisateur
   - Logger les détails techniques uniquement en dev

5. **Navigation**
   - Déconnecter automatiquement après inactivité
   - Demander le mot de passe pour actions sensibles
   - Confirmation pour suppression de compte

---

## Cas d'usage et scénarios

### Scénario 1: Première utilisation

1. L'utilisateur ouvre l'app
2. Écran de bienvenue avec slides
3. Choix inscription ou connexion
4. Inscription par email
5. Réception du code par email
6. Validation du code
7. Configuration du profil
8. Demande permission notifications
9. Tour guidé des fonctionnalités
10. Accueil personnalisé

### Scénario 2: Signalement d'urgence

1. Utilisateur dans une situation de violence
2. Accès rapide depuis l'accueil ("Signaler")
3. Choix rapide du type de violence
4. Description brève
5. Option "Envoyer maintenant" (skip autres étapes)
6. Affichage immédiat des numéros d'urgence
7. Géolocalisation des structures proches
8. Bouton d'appel direct

### Scénario 3: Suivi du cycle

1. Premier jour des règles
2. Notification "Démarrer un nouveau cycle"
3. Enregistrement rapide (intensité)
4. Calendrier mis à jour automatiquement
5. Rappels quotidiens pour symptômes
6. Notification 3 jours avant prochaines règles
7. Statistiques mensuelles

### Scénario 4: Utilisation du forum

1. Question sur contraception
2. Création d'un chat dans thème "Contraception"
3. Publication anonyme
4. Réception de réponses
5. Notification de nouvelles réponses
6. Continuation de la discussion
7. Clôture du chat quand satisfait

---

## Support et contact

### En cas de problème technique

- Email: support@gquiose.com
- Joindre les logs d'erreur si possible
- Préciser la version de l'app
- Décrire les étapes de reproduction

### Documentation

- Documentation API: voir ce document
- Configuration serveur: CONFIGURATION_ENVIRONNEMENT.md
- Changelog: (à définir)

### Tests

**Environnement de test:**
- URL: https://test-api.gquiose.com/api/v1
- Utilisateur test: test@gquiose.com / password123
- Données de test disponibles

**Environnement de production:**
- URL: https://api.gquiose.com/api/v1

---

**Version:** 1.1.0
**Dernière mise à jour:** 1er Décembre 2025
**Auteur:** [#NioulBoy](mailto:mbayedione10@gmail.com)

---

## Changelog

### Version 1.1.0 (1er Décembre 2025)

#### Nouvelles fonctionnalités
- ✅ **Statistiques d'évaluations avancées** : Nouveaux endpoints pour analyses détaillées
- ✅ **Tracking amélioré des notifications** : Statuts détaillés (pending, sent, delivered, opened, clicked, failed)
- ✅ **Statistiques d'engagement** : Taux d'ouverture et de clic des notifications
- ✅ **Questions conditionnelles** : Support de la logique conditionnelle dans les évaluations
- ✅ **Nouveaux types de questions** : rating, yesno, multiple_choice, scale, text

#### Améliorations API
- 📊 Graphiques automatiques pour les statistiques d'évaluations
- 📱 Filtres avancés dans l'historique des notifications
- 📈 Évolution mensuelle des scores d'évaluation
- 🔍 Distribution des réponses par question
- 🎯 Rapport détaillé avec paramètres de date

#### Endpoints ajoutés
- `GET /api/v1/evaluations/stats/global` - Statistiques globales
- `GET /api/v1/evaluations/stats/question/{id}` - Stats par question
- `GET /api/v1/evaluations/stats/formulaire/{type}` - Stats par formulaire
- `GET /api/v1/evaluations/stats/report` - Rapport complet

#### Améliorations notifications
- Paramètre `device_id` pour le tracking
- Paramètre `action` pour les clics
- Filtres par `status`, `platform`, `type`
- Statistiques d'engagement dans l'historique

### Version 1.0.0 (28 Novembre 2025)
- 🚀 Version initiale de la documentation
- 📱 Documentation complète pour l'application mobile
- 🔐 Système d'authentification multi-canal
- 🆘 Workflow VBG en 6 étapes
- 📅 Suivi du cycle menstruel
- 💬 Forum communautaire
- 📚 Articles et vidéos éducatives
- 🔔 Notifications push
