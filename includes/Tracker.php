<?php
class Tracker {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    /**
     * Registrar visita a una página
     */
    public function trackVisit($page_url, $page_title = '') {
        try {
            // Obtener información del visitante
            $ip = $this->getIP();
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $referer = $_SERVER['HTTP_REFERER'] ?? '';
            $session_id = session_id();
            
            // Detectar si es un bot
            $is_bot = $this->isBot($user_agent);
            
            // Obtener información del dispositivo
            $device_info = $this->parseUserAgent($user_agent);
            
            // Obtener usuario si está logueado
            $user_id = $_SESSION['user_id'] ?? null;
            
            // Insertar visita
            $sql = "INSERT INTO analytics (
                session_id, ip_address, user_agent, page_url, page_title, 
                referer, visit_date, visit_time, visit_datetime, 
                device_type, browser, os, is_bot, user_id
            ) VALUES (
                :session_id, :ip_address, :user_agent, :page_url, :page_title,
                :referer, CURDATE(), CURTIME(), NOW(),
                :device_type, :browser, :os, :is_bot, :user_id
            )";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':session_id', $session_id, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ip, PDO::PARAM_STR);
            $stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
            $stmt->bindParam(':page_url', $page_url, PDO::PARAM_STR);
            $stmt->bindParam(':page_title', $page_title, PDO::PARAM_STR);
            $stmt->bindParam(':referer', $referer, PDO::PARAM_STR);
            $stmt->bindParam(':device_type', $device_info['device'], PDO::PARAM_STR);
            $stmt->bindParam(':browser', $device_info['browser'], PDO::PARAM_STR);
            $stmt->bindParam(':os', $device_info['os'], PDO::PARAM_STR);
            $stmt->bindParam(':is_bot', $is_bot, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            
            $stmt->execute();
            
            return true;
            
        } catch (Exception $e) {
            // Silenciar errores para no afectar el sitio
            error_log("Error en tracker: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener estadísticas
     */
    public function getStats($days = 30) {
        try {
            $stats = [];
            
            // Visitas totales
            $sql = "SELECT COUNT(*) as total, COUNT(DISTINCT session_id) as unique_visits 
                    FROM analytics 
                    WHERE is_bot = 0 AND visit_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$days]);
            $visits = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['total_visits'] = $visits['total'] ?? 0;
            $stats['unique_visits'] = $visits['unique_visits'] ?? 0;
            
            // Visitas de hoy
            $sql = "SELECT COUNT(*) as total, COUNT(DISTINCT session_id) as unique_visits 
                    FROM analytics 
                    WHERE is_bot = 0 AND visit_date = CURDATE()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $today = $stmt->fetch(PDO::FETCH_ASSOC);
            $stats['today_visits'] = $today['total'] ?? 0;
            $stats['today_unique'] = $today['unique_visits'] ?? 0;
            
            // Páginas más visitadas
            $sql = "SELECT page_url, page_title, COUNT(*) as visits 
                    FROM analytics 
                    WHERE is_bot = 0 AND visit_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                    GROUP BY page_url, page_title 
                    ORDER BY visits DESC 
                    LIMIT 10";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$days]);
            $stats['top_pages'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Dispositivos
            $sql = "SELECT device_type, COUNT(*) as count 
                    FROM analytics 
                    WHERE is_bot = 0 AND visit_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                    GROUP BY device_type";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$days]);
            $stats['devices'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            // Navegadores
            $sql = "SELECT browser, COUNT(*) as count 
                    FROM analytics 
                    WHERE is_bot = 0 AND visit_date >= DATE_SUB(CURDATE(), INTERVAL ? DAY)
                    GROUP BY browser";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute([$days]);
            $stats['browsers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error al obtener stats: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener datos reales del historial
     */
    public function getHistoryStats() {
        try {
            $stats = [];
            
            // Totales del historial
            $sql = "SELECT 
                        COUNT(*) as total,
                        COUNT(DISTINCT usuario_id) as unique_users,
                        SUM(CASE WHEN tipo_evento = 'login' THEN 1 ELSE 0 END) as logins,
                        SUM(CASE WHEN tipo_evento = 'play' THEN 1 ELSE 0 END) as plays,
                        SUM(CASE WHEN tipo_evento = 'download' THEN 1 ELSE 0 END) as downloads,
                        SUM(CASE WHEN tipo_evento = 'contact' THEN 1 ELSE 0 END) as contacts,
                        SUM(CASE WHEN tipo_evento = 'create' THEN 1 ELSE 0 END) as creates,
                        SUM(CASE WHEN tipo_evento = 'update' THEN 1 ELSE 0 END) as updates,
                        SUM(CASE WHEN tipo_evento = 'delete' THEN 1 ELSE 0 END) as deletes
                    FROM historial 
                    WHERE DATE(fecha_hora) >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            $stats['total_events'] = $result['total'] ?? 0;
            $stats['unique_users'] = $result['unique_users'] ?? 0;
            $stats['logins'] = $result['logins'] ?? 0;
            $stats['plays'] = $result['plays'] ?? 0;
            $stats['downloads'] = $result['downloads'] ?? 0;
            $stats['contacts'] = $result['contacts'] ?? 0;
            $stats['creates'] = $result['creates'] ?? 0;
            $stats['updates'] = $result['updates'] ?? 0;
            $stats['deletes'] = $result['deletes'] ?? 0;
            
            // Actividad de hoy
            $sql = "SELECT COUNT(*) as total FROM historial WHERE DATE(fecha_hora) = CURDATE()";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $stats['today_events'] = $stmt->fetchColumn();
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error al obtener stats del historial: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas de podcasts
     */
    public function getPodcastStats() {
        try {
            $stats = [];
            
            // Total podcasts
            $sql = "SELECT COUNT(*) FROM podcasts WHERE activo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $stats['total_podcasts'] = $stmt->fetchColumn();
            
            // Total reproducciones
            $sql = "SELECT SUM(reproducciones) FROM podcasts WHERE activo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $stats['total_plays'] = $stmt->fetchColumn() ?? 0;
            
            // Total descargas
            $sql = "SELECT SUM(descargas) FROM podcasts WHERE activo = 1";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $stats['total_downloads'] = $stmt->fetchColumn() ?? 0;
            
            // Podcasts más populares
            $sql = "SELECT id, titulo_es, reproducciones, descargas 
                    FROM podcasts 
                    WHERE activo = 1 
                    ORDER BY reproducciones DESC 
                    LIMIT 5";
            $stmt = $this->conexion->prepare($sql);
            $stmt->execute();
            $stats['top_podcasts'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
            
            return $stats;
            
        } catch (Exception $e) {
            error_log("Error al obtener stats de podcasts: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Detectar si es un bot
     */
    private function isBot($user_agent) {
        $bots = ['bot', 'crawler', 'spider', 'scraper', 'curl', 'wget', 'python', 'java', 'go-http'];
        $ua_lower = strtolower($user_agent);
        foreach ($bots as $bot) {
            if (strpos($ua_lower, $bot) !== false) {
                return true;
            }
        }
        return false;
    }
    
    /**
     * Parsear información del dispositivo
     */
    private function parseUserAgent($user_agent) {
        $info = [
            'device' => 'Unknown',
            'browser' => 'Unknown',
            'os' => 'Unknown'
        ];
        
        // Detectar dispositivo
        if (preg_match('/mobile/i', $user_agent)) {
            $info['device'] = 'Mobile';
        } elseif (preg_match('/tablet/i', $user_agent)) {
            $info['device'] = 'Tablet';
        } else {
            $info['device'] = 'Desktop';
        }
        
        // Detectar navegador
        if (preg_match('/chrome/i', $user_agent)) {
            $info['browser'] = 'Chrome';
        } elseif (preg_match('/firefox/i', $user_agent)) {
            $info['browser'] = 'Firefox';
        } elseif (preg_match('/safari/i', $user_agent)) {
            $info['browser'] = 'Safari';
        } elseif (preg_match('/edge/i', $user_agent)) {
            $info['browser'] = 'Edge';
        }
        
        // Detectar OS
        if (preg_match('/windows/i', $user_agent)) {
            $info['os'] = 'Windows';
        } elseif (preg_match('/linux/i', $user_agent)) {
            $info['os'] = 'Linux';
        } elseif (preg_match('/mac/i', $user_agent)) {
            $info['os'] = 'Mac';
        } elseif (preg_match('/android/i', $user_agent)) {
            $info['os'] = 'Android';
        } elseif (preg_match('/ios/i', $user_agent)) {
            $info['os'] = 'iOS';
        }
        
        return $info;
    }
    
    /**
     * Obtener IP real
     */
    private function getIP() {
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            return $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            return $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            return $_SERVER['REMOTE_ADDR'] ?? '0.0.0.0';
        }
    }
}

