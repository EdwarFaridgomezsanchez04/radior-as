-- Script para actualizar la tabla podcasts para soporte de YouTube
-- Ejecutar este script en phpMyAdmin o MySQL Workbench
-- IMPORTANTE: Ejecutar este script ANTES de usar la sincronización con YouTube

-- Paso 1: Agregar nuevas columnas para YouTube si no existen
ALTER TABLE `podcasts` 
ADD COLUMN IF NOT EXISTS `youtube_url` VARCHAR(500) NULL AFTER `imagen`,
ADD COLUMN IF NOT EXISTS `youtube_id` VARCHAR(20) NULL AFTER `youtube_url`;

-- Paso 2: Hacer que la columna 'archivo' sea opcional (NULL) 
-- ya que ahora los podcasts pueden venir de YouTube
ALTER TABLE `podcasts` 
MODIFY COLUMN `archivo` VARCHAR(255) NULL;

-- Paso 3: Crear índice para búsquedas más rápidas por youtube_id (si no existe)
CREATE INDEX IF NOT EXISTS `idx_youtube_id` ON `podcasts` (`youtube_id`);

-- Verificación: Comprobar que las columnas se crearon correctamente
SELECT 
    COLUMN_NAME, 
    DATA_TYPE, 
    IS_NULLABLE, 
    COLUMN_DEFAULT,
    CHARACTER_MAXIMUM_LENGTH
FROM INFORMATION_SCHEMA.COLUMNS 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME = 'podcasts' 
  AND COLUMN_NAME IN ('youtube_url', 'youtube_id', 'archivo');

-- Para ver la estructura completa actualizada de la tabla:
-- DESCRIBE podcasts;
