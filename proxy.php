<?php
// Proxy para RadioRías - Evitar CORS con servidor Icecast
error_reporting(0);
ini_set('display_errors', 0);

// URL del servidor Icecast
$streamUrl = "http://88.150.230.110:8950/stream";

// Headers para evitar CORS y optimizar streaming
header("Content-Type: audio/mpeg");
header("Cache-Control: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Range");
header("Accept-Ranges: bytes");

// Manejo de preflight requests (CORS)
if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {
    http_response_code(200);
    exit();
}

// Configuración para streaming continuo
set_time_limit(0);
ini_set('memory_limit', '512M');
ini_set('max_execution_time', 0);

// Función para limpiar buffers
function clearBuffers() {
    while (ob_get_level()) {
        ob_end_clean();
    }
}

// Función para streaming de datos
function streamAudioData($ch, $data) {
    static $totalBytes = 0;
    
    // Verificar si la conexión sigue activa
    if (connection_aborted()) {
        return 0;
    }
    
    // Enviar datos inmediatamente
    echo $data;
    
    // Forzar envío
    if (function_exists('fastcgi_finish_request')) {
        fastcgi_finish_request();
    } else {
        if (ob_get_level()) {
            ob_flush();
        }
        flush();
    }
    
    $totalBytes += strlen($data);
    
    return strlen($data);
}

// Limpiar buffers existentes
clearBuffers();

// Inicializar cURL
$ch = curl_init();

// Configuración optimizada de cURL para streaming
curl_setopt_array($ch, [
    CURLOPT_URL => $streamUrl,
    CURLOPT_RETURNTRANSFER => false,
    CURLOPT_WRITEFUNCTION => 'streamAudioData',
    CURLOPT_FOLLOWLOCATION => true,
    CURLOPT_MAXREDIRS => 5,
    CURLOPT_TIMEOUT => 0,
    CURLOPT_CONNECTTIMEOUT => 10,
    CURLOPT_USERAGENT => 'RadioRias-Proxy/1.0',
    CURLOPT_HTTPHEADER => [
        'Accept: audio/mpeg, audio/*',
        'Connection: keep-alive'
    ],
    CURLOPT_SSL_VERIFYPEER => false,
    CURLOPT_SSL_VERIFYHOST => false,
    CURLOPT_BUFFERSIZE => 8192, // 8KB buffer para streaming suave
    CURLOPT_TCP_NODELAY => true,
]);

// Ejecutar el streaming
$result = curl_exec($ch);

// Verificar errores
if ($result === false) {
    $error = curl_error($ch);
    error_log("Error en proxy RadioRías: " . $error);
    http_response_code(500);
    echo "Error de streaming: " . $error;
}

// Cerrar cURL
curl_close($ch);
?>
