<?php
// Proxy Infinito para RadioRías - Stream Continuo Sin Cortes
error_reporting(0);
ini_set('display_errors', 0);

// URL del servidor Icecast
$streamUrl = "http://88.150.230.110:8950/stream";

// Headers optimizados para streaming infinito
header("Content-Type: audio/mpeg");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Range");
header("Accept-Ranges: bytes");
header("Connection: keep-alive");
header("Keep-Alive: timeout=0, max=0");

// Manejo de preflight requests (CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración para streaming INFINITO
set_time_limit(0);                    // Sin límite de tiempo
ini_set('memory_limit', '1024M');     // Más memoria
ini_set('max_execution_time', 0);     // Sin timeout de ejecución
ini_set('max_input_time', 0);         // Sin timeout de input
ignore_user_abort(true);              // Continuar aunque el usuario se desconecte

// Función para limpiar buffers
function clearBuffers() {
    while (ob_get_level()) {
        ob_end_clean();
    }
}

// Función optimizada para streaming continuo sin cortes
function streamAudioData($ch, $data) {
    static $totalBytes = 0;
    static $lastFlush = 0;
    
    // Verificar si la conexión sigue activa
    if (connection_aborted()) {
        error_log("RadioRías: Conexión abortada después de $totalBytes bytes");
        return 0;
    }
    
    // Enviar datos inmediatamente
    echo $data;
    
    // Flush más inteligente (cada 8KB o cada segundo)
    $currentTime = microtime(true);
    $dataSize = strlen($data);
    $totalBytes += $dataSize;
    
    if ($dataSize >= 8192 || ($currentTime - $lastFlush) >= 1.0) {
        // Forzar envío inmediato
        if (function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        } else {
            if (ob_get_level()) {
                ob_flush();
            }
            flush();
        }
        $lastFlush = $currentTime;
    }
    
    // Log cada 10MB para monitoreo
    if ($totalBytes > 0 && ($totalBytes % (10 * 1024 * 1024)) == 0) {
        error_log("RadioRías: Stream continuo - " . ($totalBytes / (1024 * 1024)) . " MB transmitidos");
    }
    
    return $dataSize;
}

// Limpiar buffers existentes
clearBuffers();

// Inicializar cURL
$ch = curl_init();

// Configuración INFINITA de cURL - Sin timeouts, máxima estabilidad
curl_setopt_array($ch, [
    CURLOPT_URL => $streamUrl,
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_WRITEFUNCTION => 'streamAudioData',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 10,
    
    // TIMEOUTS INFINITOS
    CURLOPT_TIMEOUT => 0,                    // Sin timeout total
    CURLOPT_CONNECTTIMEOUT => 30,            // 30 segundos para conectar (razonable)
    CURLOPT_LOW_SPEED_LIMIT => 1,            // Mínimo 1 byte/segundo
    CURLOPT_LOW_SPEED_TIME => 60,            // Esperar 60 segundos antes de timeout por velocidad
    
    // HEADERS OPTIMIZADOS
    CURLOPT_USERAGENT => 'RadioRias-InfiniteProxy/2.0',
    CURLOPT_HTTPHEADER => [
        'Accept: audio/mpeg, audio/*, */*',
        'Connection: keep-alive',
        'Keep-Alive: timeout=0',
        'Cache-Control: no-cache',
        'Pragma: no-cache'
    ],
    
    // CONFIGURACIÓN DE RED
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_BUFFERSIZE => 16384,             // 16KB buffer para mejor rendimiento
    CURLOPT_TCP_NODELAY => true,             // Sin delay TCP
    CURLOPT_TCP_KEEPALIVE => 1,              // Keep-alive TCP
    CURLOPT_TCP_KEEPIDLE => 120,             // Idle time antes de keep-alive
    CURLOPT_TCP_KEEPINTVL => 60,             // Intervalo entre keep-alives
    
    // CONFIGURACIÓN ADICIONAL
    CURLOPT_FRESH_CONNECT => false,          // Reutilizar conexiones
    CURLOPT_FORBID_REUSE => false,           // Permitir reutilización
    CURLOPT_MAXCONNECTS => 10,               // Pool de conexiones
]);

// Log de inicio
error_log("RadioRías: Iniciando proxy infinito para stream: $streamUrl");

// Ejecutar el streaming INFINITO
$startTime = microtime(true);
$result = curl_exec($ch);

// Verificar errores y logs detallados
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$totalTime = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
$connectTime = curl_getinfo($ch, CURLINFO_CONNECT_TIME);

if ($result === false) {
    $error = curl_error($ch);
    $errno = curl_errno($ch);
    
    error_log("RadioRías ERROR: [$errno] $error - HTTP: $httpCode - Tiempo: {$totalTime}s");
    
    // Intentar reconexión automática una vez
    error_log("RadioRías: Intentando reconexión automática...");
    curl_close($ch);
    
    // Nueva conexión
    $ch = curl_init();
    curl_setopt_array($ch, [
        CURLOPT_URL => $streamUrl,
        CURLOPT_RETURNTRANSFER => false,
        CURLOPT_WRITEFUNCTION => 'streamAudioData',
        CURLOPT_TIMEOUT => 0,
        CURLOPT_CONNECTTIMEOUT => 15,
        CURLOPT_USERAGENT => 'RadioRias-Recovery/1.0',
        CURLOPT_HTTPHEADER => ['Connection: keep-alive'],
        CURLOPT_BUFFERSIZE => 8192,
        CURLOPT_TCP_NODELAY => true,
    ]);
    
    $result = curl_exec($ch);
    if ($result === false) {
        http_response_code(500);
        echo "Error crítico de streaming";
    }
} else {
    $duration = microtime(true) - $startTime;
    error_log("RadioRías: Stream completado exitosamente - Duración: {$duration}s - HTTP: $httpCode");
}

// Cerrar cURL
curl_close($ch);

error_log("RadioRías: Proxy finalizado");
?>
