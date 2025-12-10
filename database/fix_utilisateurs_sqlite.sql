-- Script pour rendre email et phone nullable dans SQLite
-- Version corrigée - NE recrée PAS les index s'ils existent déjà

BEGIN TRANSACTION;

-- 1. Désactiver les foreign keys
PRAGMA foreign_keys=OFF;

-- 2. Renommer la table actuelle
ALTER TABLE utilisateurs RENAME TO utilisateurs_old;

-- 3. Créer la nouvelle table avec les bonnes contraintes (SANS les index pour l'instant)
CREATE TABLE utilisateurs (
    id INTEGER PRIMARY KEY AUTOINCREMENT NOT NULL,
    nom VARCHAR(255) NOT NULL,
    prenom VARCHAR(255) NOT NULL,
    email VARCHAR(255) NULL,
    phone VARCHAR(255) NULL,
    sexe VARCHAR(255) NOT NULL,
    status BOOLEAN NOT NULL,
    created_at DATETIME,
    updated_at DATETIME,
    password VARCHAR(255),
    dob VARCHAR(255),
    provider VARCHAR(255),
    provider_id VARCHAR(255),
    photo VARCHAR(255),
    fcm_token TEXT,
    platform VARCHAR(255),
    email_verified_at DATETIME,
    ville_id INTEGER,
    phone_verified_at DATETIME
);

-- 4. Copier toutes les données
INSERT INTO utilisateurs SELECT * FROM utilisateurs_old;

-- 5. Créer les index UNIQUEMENT s'ils n'existent pas
-- Note: SQLite va ignorer l'erreur "already exists" avec CREATE INDEX IF NOT EXISTS
CREATE UNIQUE INDEX IF NOT EXISTS utilisateurs_email_unique ON utilisateurs (email);
CREATE UNIQUE INDEX IF NOT EXISTS utilisateurs_phone_unique ON utilisateurs (phone);

-- 6. Supprimer l'ancienne table
DROP TABLE utilisateurs_old;

-- 7. Réactiver les foreign keys
PRAGMA foreign_keys=ON;

COMMIT;
