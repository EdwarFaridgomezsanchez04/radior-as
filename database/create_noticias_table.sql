-- Script para crear la tabla de noticias
-- Ejecutar este script en phpMyAdmin o MySQL Workbench

-- Crear tabla de noticias
CREATE TABLE IF NOT EXISTS `noticias` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `titulo_es` varchar(200) NOT NULL,
  `titulo_gl` varchar(200) NOT NULL,
  `subtitulo_es` varchar(300) DEFAULT NULL,
  `subtitulo_gl` varchar(300) DEFAULT NULL,
  `contenido_es` longtext NOT NULL,
  `contenido_gl` longtext NOT NULL,
  `resumen_es` text DEFAULT NULL,
  `resumen_gl` text DEFAULT NULL,
  `imagen` varchar(255) DEFAULT NULL,
  `imagen_alt_es` varchar(200) DEFAULT NULL,
  `imagen_alt_gl` varchar(200) DEFAULT NULL,
  `autor` varchar(100) DEFAULT NULL,
  `categoria_id` int(11) DEFAULT NULL,
  `fecha_publicacion` datetime NOT NULL,
  `fecha_creacion` datetime NOT NULL DEFAULT current_timestamp(),
  `ultima_actualizacion` datetime DEFAULT NULL ON UPDATE current_timestamp(),
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `destacado` tinyint(1) NOT NULL DEFAULT 0,
  `vistas` int(11) NOT NULL DEFAULT 0,
  `slug_es` varchar(200) DEFAULT NULL,
  `slug_gl` varchar(200) DEFAULT NULL,
  `meta_titulo_es` varchar(200) DEFAULT NULL,
  `meta_titulo_gl` varchar(200) DEFAULT NULL,
  `meta_descripcion_es` text DEFAULT NULL,
  `meta_descripcion_gl` text DEFAULT NULL,
  `creado_por` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_categoria` (`categoria_id`),
  KEY `idx_fecha_publicacion` (`fecha_publicacion`),
  KEY `idx_activo` (`activo`),
  KEY `idx_destacado` (`destacado`),
  KEY `idx_slug_es` (`slug_es`),
  KEY `idx_slug_gl` (`slug_gl`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Crear tabla de categorías de noticias
CREATE TABLE IF NOT EXISTS `categorias_noticia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `nombre_es` varchar(100) NOT NULL,
  `nombre_gl` varchar(100) NOT NULL,
  `descripcion_es` text DEFAULT NULL,
  `descripcion_gl` text DEFAULT NULL,
  `color` varchar(7) DEFAULT '#20B2AA',
  `icono` varchar(50) DEFAULT 'fas fa-newspaper',
  `activo` tinyint(1) NOT NULL DEFAULT 1,
  `orden` int(11) DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Insertar categorías por defecto
INSERT INTO `categorias_noticia` (`nombre_es`, `nombre_gl`, `descripcion_es`, `descripcion_gl`, `color`, `icono`, `orden`) VALUES
('General', 'Xeral', 'Noticias generales', 'Noticias xerais', '#20B2AA', 'fas fa-newspaper', 1),
('Deportes', 'Deportes', 'Noticias deportivas', 'Noticias deportivas', '#dc2626', 'fas fa-futbol', 2),
('Cultura', 'Cultura', 'Noticias culturales', 'Noticias culturais', '#7c3aed', 'fas fa-palette', 3),
('Local', 'Local', 'Noticias locales', 'Noticias locais', '#059669', 'fas fa-map-marker-alt', 4),
('Tecnología', 'Tecnoloxía', 'Noticias tecnológicas', 'Noticias tecnolóxicas', '#2563eb', 'fas fa-microchip', 5),
('Entretenimiento', 'Entretenemento', 'Noticias de entretenimiento', 'Noticias de entretenemento', '#ea580c', 'fas fa-music', 6);

-- Crear tabla de comentarios de noticias (opcional)
CREATE TABLE IF NOT EXISTS `comentarios_noticia` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `noticia_id` int(11) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `comentario` text NOT NULL,
  `fecha` datetime NOT NULL DEFAULT current_timestamp(),
  `aprobado` tinyint(1) NOT NULL DEFAULT 0,
  `ip` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idx_noticia_id` (`noticia_id`),
  KEY `idx_aprobado` (`aprobado`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Verificar que las tablas se crearon correctamente
SELECT 
    TABLE_NAME, 
    TABLE_ROWS,
    CREATE_TIME
FROM INFORMATION_SCHEMA.TABLES 
WHERE TABLE_SCHEMA = DATABASE() 
  AND TABLE_NAME IN ('noticias', 'categorias_noticia', 'comentarios_noticia');

-- Para ver la estructura de las tablas:
-- DESCRIBE noticias;
-- DESCRIBE categorias_noticia;
-- DESCRIBE comentarios_noticia;
