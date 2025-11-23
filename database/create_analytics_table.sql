-- Tabla para estadísticas de visitas y páginas
CREATE TABLE IF NOT EXISTS analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    session_id VARCHAR(255) NOT NULL,
    ip_address VARCHAR(45) NOT NULL,
    user_agent TEXT,
    page_url VARCHAR(500) NOT NULL,
    page_title VARCHAR(255),
    referer VARCHAR(500),
    visit_date DATE NOT NULL,
    visit_time TIME NOT NULL,
    visit_datetime DATETIME NOT NULL,
    country VARCHAR(100),
    device_type VARCHAR(50),
    browser VARCHAR(100),
    os VARCHAR(100),
    is_bot TINYINT(1) DEFAULT 0,
    user_id INT DEFAULT NULL,
    time_on_page INT DEFAULT 0 COMMENT 'Tiempo en segundos',
    INDEX idx_session_id (session_id),
    INDEX idx_visit_date (visit_date),
    INDEX idx_ip_address (ip_address),
    INDEX idx_user_id (user_id),
    INDEX idx_is_bot (is_bot)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Analytics de visitas al sitio';

-- Tabla para contadores de reproducciones
CREATE TABLE IF NOT EXISTS play_counts (
    id INT AUTO_INCREMENT PRIMARY KEY,
    podcast_id INT DEFAULT NULL,
    program_id INT DEFAULT NULL,
    youtube_id VARCHAR(100) DEFAULT NULL,
    play_date DATE NOT NULL,
    plays INT DEFAULT 1,
    unique_plays INT DEFAULT 0,
    INDEX idx_podcast_id (podcast_id),
    INDEX idx_play_date (play_date),
    INDEX idx_youtube_id (youtube_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci COMMENT='Contadores de reproducciones por fecha';

