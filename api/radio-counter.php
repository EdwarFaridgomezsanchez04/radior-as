<?php
/**
 * Contador simple para radio en vivo
 * Solo rastrea lo que hPanel NO puede rastrear
 */

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Manejar preflight OPTIONS request
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit;
}

// Directorio para contadores
$counters_dir = __DIR__ . '/../radio-stats/';
if (!is_dir($counters_dir)) {
    mkdir($counters_dir, 0755, true);
}

// Archivos de contadores
$today = date('Y-m-d');
$plays_file = $counters_dir . 'plays-' . $today . '.txt';
$listeners_file = $counters_dir . 'listeners.json';

try {
    $action = $_GET['action'] ?? $_POST['action'] ?? '';
    
    switch ($action) {
        case 'radio_play':
            // Registrar reproducción de radio
            $timestamp = date('H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? 'unknown';
            
            // Guardar en archivo simple
            $data = $timestamp . '|' . $ip . '|' . substr($user_agent, 0, 50) . "\n";
            file_put_contents($plays_file, $data, FILE_APPEND | LOCK_EX);
            
            // Actualizar oyentes activos
            updateActiveListeners($listeners_file, $ip);
            
            echo json_encode(['success' => true, 'message' => 'Radio play registered']);
            break;
            
        case 'radio_stop':
            // Registrar parada de radio
            $input = json_decode(file_get_contents('php://input'), true);
            $duration = $input['duration'] ?? 0;
            
            $timestamp = date('H:i:s');
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            
            // Guardar duración en archivo
            $duration_file = $counters_dir . 'durations-' . $today . '.txt';
            $data = $timestamp . '|' . $ip . '|' . $duration . "\n";
            file_put_contents($duration_file, $data, FILE_APPEND | LOCK_EX);
            
            // Remover de oyentes activos
            removeActiveListener($listeners_file, $ip);
            
            echo json_encode(['success' => true, 'message' => 'Radio stop registered']);
            break;
            
        case 'heartbeat':
            // Mantener oyente activo
            $ip = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
            updateActiveListeners($listeners_file, $ip);
            
            echo json_encode(['success' => true, 'message' => 'Heartbeat registered']);
            break;
            
        case 'get_stats':
            // Obtener estadísticas simples
            $stats = getRadioStats($counters_dir, $today);
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        default:
            throw new Exception('Acción no válida');
    }
    
} catch (Exception $e) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}

/**
 * Actualizar oyentes activos
 */
function updateActiveListeners($file, $ip) {
    $listeners = [];
    if (file_exists($file)) {
        $listeners = json_decode(file_get_contents($file), true) ?: [];
    }
    
    // Limpiar oyentes inactivos (más de 2 minutos)
    $cutoff = time() - 120;
    $listeners = array_filter($listeners, function($timestamp) use ($cutoff) {
        return $timestamp > $cutoff;
    });
    
    // Agregar/actualizar oyente actual
    $listeners[$ip] = time();
    
    file_put_contents($file, json_encode($listeners), LOCK_EX);
}

/**
 * Remover oyente activo
 */
function removeActiveListener($file, $ip) {
    if (!file_exists($file)) return;
    
    $listeners = json_decode(file_get_contents($file), true) ?: [];
    unset($listeners[$ip]);
    
    file_put_contents($file, json_encode($listeners), LOCK_EX);
}

/**
 * Obtener estadísticas de radio
 */
function getRadioStats($dir, $today) {
    $stats = [
        'today_plays' => 0,
        'active_listeners' => 0,
        'avg_duration' => 0,
        'total_duration' => 0,
        'unique_listeners_today' => 0
    ];
    
    // Reproducciones de hoy
    $plays_file = $dir . 'plays-' . $today . '.txt';
    if (file_exists($plays_file)) {
        $plays = file($plays_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $stats['today_plays'] = count($plays);
        
        // IPs únicas de hoy
        $unique_ips = [];
        foreach ($plays as $play) {
            $parts = explode('|', $play);
            if (isset($parts[1])) {
                $unique_ips[$parts[1]] = true;
            }
        }
        $stats['unique_listeners_today'] = count($unique_ips);
    }
    
    // Oyentes activos
    $listeners_file = $dir . 'listeners.json';
    if (file_exists($listeners_file)) {
        $listeners = json_decode(file_get_contents($listeners_file), true) ?: [];
        
        // Limpiar inactivos
        $cutoff = time() - 120;
        $active = array_filter($listeners, function($timestamp) use ($cutoff) {
            return $timestamp > $cutoff;
        });
        
        $stats['active_listeners'] = count($active);
    }
    
    // Duraciones de hoy
    $duration_file = $dir . 'durations-' . $today . '.txt';
    if (file_exists($duration_file)) {
        $durations = file($duration_file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        $total = 0;
        $count = 0;
        
        foreach ($durations as $duration_line) {
            $parts = explode('|', $duration_line);
            if (isset($parts[2]) && is_numeric($parts[2])) {
                $total += (int)$parts[2];
                $count++;
            }
        }
        
        $stats['total_duration'] = $total;
        $stats['avg_duration'] = $count > 0 ? round($total / $count) : 0;
    }
    
    return $stats;
}
?>
