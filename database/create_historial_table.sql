-- Tabla de historial para registrar todas las acciones del sistema
CREATE TABLE IF NOT EXISTS historial (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo_evento VARCHAR(50) NOT NULL COMMENT 'login, logout, create, update, delete, view, download, play, contact, etc',
    categoria VARCHAR(50) NOT NULL COMMENT 'usuario, podcast, noticia, programa, contacto, etc',
    usuario_id INT DEFAULT NULL COMMENT 'ID del usuario que realizó la acción',
    usuario_nombre VARCHAR(100) DEFAULT NULL COMMENT 'Nombre del usuario o visitante',
    ip_address VARCHAR(45) DEFAULT NULL COMMENT 'Dirección IP',
    user_agent TEXT COMMENT 'Navegador y sistema operativo',
    entidad_id INT DEFAULT NULL COMMENT 'ID de la entidad afectada (podcast_id, noticia_id, etc)',
    entidad_nombre VARCHAR(255) DEFAULT NULL COMMENT 'Nombre de la entidad (título del podcast, noticia, etc)',
    accion VARCHAR(100) NOT NULL COMMENT 'Descripción breve de la acción',
    detalles JSON DEFAULT NULL COMMENT 'Información adicional en formato JSON',
    fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT 'Fecha y hora del evento',
    INDEX idx_tipo_evento (tipo_evento),
    INDEX idx_categoria (categoria),
    INDEX idx_usuario_id (usuario_id),
    INDEX idx_fecha_hora (fecha_hora),
    INDEX idx_entidad (categoria, entidad_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Historial completo de todas las acciones del sistema';

