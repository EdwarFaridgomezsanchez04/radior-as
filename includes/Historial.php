<?php
class Historial {
    private $conexion;
    
    public function __construct($conexion) {
        $this->conexion = $conexion;
    }
    
    /**
     * Registrar un evento en el historial
     * 
     * @param string $tipo_evento - Tipo de evento (login, create, update, delete, view, download, play, contact)
     * @param string $categoria - Categoría (usuario, podcast, noticia, programa, contacto)
     * @param string $accion - Descripción de la acción
     * @param array $params - Parámetros adicionales
     * @return bool
     */
    public function registrar($tipo_evento, $categoria, $accion, $params = []) {
        try {
            // Obtener información del usuario si está logueado
            $usuario_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
            $usuario_nombre = isset($_SESSION['username']) ? $_SESSION['username'] : null;
            
            // Si no hay usuario en sesión pero se pasó en params
            if ($usuario_id === null && isset($params['usuario_id'])) {
                $usuario_id = $params['usuario_id'];
            }
            if ($usuario_nombre === null && isset($params['usuario_nombre'])) {
                $usuario_nombre = $params['usuario_nombre'];
            }
            
            // Obtener información del visitante
            $ip_address = $this->obtenerIP();
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? '';
            $entidad_id = $params['entidad_id'] ?? null;
            $entidad_nombre = $params['entidad_nombre'] ?? null;
            $detalles = isset($params['detalles']) ? json_encode($params['detalles']) : null;
            
            $sql = "INSERT INTO historial (
                tipo_evento, categoria, usuario_id, usuario_nombre, 
                ip_address, user_agent, entidad_id, entidad_nombre, 
                accion, detalles, fecha_hora
            ) VALUES (
                :tipo_evento, :categoria, :usuario_id, :usuario_nombre,
                :ip_address, :user_agent, :entidad_id, :entidad_nombre,
                :accion, :detalles, NOW()
            )";
            
            $stmt = $this->conexion->prepare($sql);
            $stmt->bindParam(':tipo_evento', $tipo_evento, PDO::PARAM_STR);
            $stmt->bindParam(':categoria', $categoria, PDO::PARAM_STR);
            $stmt->bindParam(':usuario_id', $usuario_id, PDO::PARAM_INT);
            $stmt->bindParam(':usuario_nombre', $usuario_nombre, PDO::PARAM_STR);
            $stmt->bindParam(':ip_address', $ip_address, PDO::PARAM_STR);
            $stmt->bindParam(':user_agent', $user_agent, PDO::PARAM_STR);
            $stmt->bindParam(':entidad_id', $entidad_id, PDO::PARAM_INT);
            $stmt->bindParam(':entidad_nombre', $entidad_nombre, PDO::PARAM_STR);
            $stmt->bindParam(':accion', $accion, PDO::PARAM_STR);
            $stmt->bindParam(':detalles', $detalles, PDO::PARAM_STR);
            
            return $stmt->execute();
            
        } catch (Exception $e) {
            // Log del error pero no lanzar excepción para no interrumpir el flujo
            error_log("Error al registrar historial: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Obtener la dirección IP real del visitante
     */
    private function obtenerIP() {
        $ip = '';
        
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } elseif (!empty($_SERVER['REMOTE_ADDR'])) {
            $ip = $_SERVER['REMOTE_ADDR'];
        }
        
        return $ip;
    }
    
    /**
     * Obtener historial con filtros
     */
    public function obtenerHistorial($filtros = [], $limite = 100, $offset = 0) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filtros['tipo_evento'])) {
                $where[] = "tipo_evento = :tipo_evento";
                $params[':tipo_evento'] = $filtros['tipo_evento'];
            }
            
            if (!empty($filtros['categoria'])) {
                $where[] = "categoria = :categoria";
                $params[':categoria'] = $filtros['categoria'];
            }
            
            if (!empty($filtros['usuario_id'])) {
                $where[] = "usuario_id = :usuario_id";
                $params[':usuario_id'] = $filtros['usuario_id'];
            }
            
            if (!empty($filtros['fecha_desde'])) {
                $where[] = "fecha_hora >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $where[] = "fecha_hora <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'];
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $sql = "SELECT * FROM historial 
                    $whereClause 
                    ORDER BY fecha_hora DESC 
                    LIMIT :limite OFFSET :offset";
            
            $stmt = $this->conexion->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->bindValue(':limite', (int)$limite, PDO::PARAM_INT);
            $stmt->bindValue(':offset', (int)$offset, PDO::PARAM_INT);
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener historial: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Obtener estadísticas del historial
     */
    public function obtenerEstadisticas($filtros = []) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filtros['fecha_desde'])) {
                $where[] = "fecha_hora >= :fecha_desde";
                $params[':fecha_desde'] = $filtros['fecha_desde'];
            }
            
            if (!empty($filtros['fecha_hasta'])) {
                $where[] = "fecha_hora <= :fecha_hasta";
                $params[':fecha_hasta'] = $filtros['fecha_hasta'];
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $sql = "SELECT 
                        tipo_evento,
                        COUNT(*) as total
                    FROM historial 
                    $whereClause
                    GROUP BY tipo_evento
                    ORDER BY total DESC";
            
            $stmt = $this->conexion->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
            
        } catch (Exception $e) {
            error_log("Error al obtener estadísticas: " . $e->getMessage());
            return [];
        }
    }
    
    /**
     * Contar total de registros
     */
    public function contar($filtros = []) {
        try {
            $where = [];
            $params = [];
            
            if (!empty($filtros['tipo_evento'])) {
                $where[] = "tipo_evento = :tipo_evento";
                $params[':tipo_evento'] = $filtros['tipo_evento'];
            }
            
            if (!empty($filtros['categoria'])) {
                $where[] = "categoria = :categoria";
                $params[':categoria'] = $filtros['categoria'];
            }
            
            $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";
            
            $sql = "SELECT COUNT(*) as total FROM historial $whereClause";
            $stmt = $this->conexion->prepare($sql);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }
            
            $stmt->execute();
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            
            return $result['total'] ?? 0;
            
        } catch (Exception $e) {
            error_log("Error al contar historial: " . $e->getMessage());
            return 0;
        }
    }
}

