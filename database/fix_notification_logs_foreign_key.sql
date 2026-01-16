-- Script de correction de la contrainte foreign key sur notification_logs
-- À exécuter sur la base de données de production

-- 1. Vérifier la contrainte actuelle
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'notification_logs' 
  AND CONSTRAINT_NAME LIKE '%notification_schedule%';

-- 2. Supprimer l'ancienne contrainte si elle existe
ALTER TABLE `notification_logs` 
DROP FOREIGN KEY `notification_logs_notification_schedule_id_foreign`;

-- 3. Ajouter la nouvelle contrainte pointant vers push_notifications
ALTER TABLE `notification_logs` 
ADD CONSTRAINT `notification_logs_notification_schedule_id_foreign` 
FOREIGN KEY (`notification_schedule_id`) 
REFERENCES `push_notifications` (`id`) 
ON DELETE SET NULL;

-- 4. Vérifier que la contrainte a été créée correctement
SELECT 
    CONSTRAINT_NAME, 
    TABLE_NAME, 
    COLUMN_NAME, 
    REFERENCED_TABLE_NAME, 
    REFERENCED_COLUMN_NAME
FROM information_schema.KEY_COLUMN_USAGE
WHERE TABLE_NAME = 'notification_logs' 
  AND CONSTRAINT_NAME LIKE '%notification_schedule%';

-- 5. Test: Essayer de créer un log de notification
-- Cette requête devrait maintenant fonctionner
SELECT COUNT(*) as total_push_notifications FROM push_notifications;
SELECT COUNT(*) as total_notification_logs FROM notification_logs;
