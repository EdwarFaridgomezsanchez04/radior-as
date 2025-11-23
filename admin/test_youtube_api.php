<?php
/**
 * Script de prueba para verificar la conexión con YouTube API
 * Acceder desde: admin/test_youtube_api.php
 */

require_once('../config/config.php');

// Configuración
$apiKey = YOUTUBE_API_KEY;
$channelId = YOUTUBE_CHANNEL_ID;

// Probar conexión básica
echo "<h1>Test de YouTube API</h1>";
echo "<hr>";

echo "<h2>1. Configuración</h2>";
echo "<p><strong>API Key:</strong> " . substr($apiKey, 0, 20) . "..." . substr($apiKey, -5) . "</p>";
echo "<p><strong>Channel ID:</strong> {$channelId}</p>";
echo "<hr>";

echo "<h2>2. Probando conexión con YouTube API</h2>";

// URL para obtener información del canal
$url = "https://www.googleapis.com/youtube/v3/channels?key={$apiKey}&id={$channelId}&part=snippet,statistics";

echo "<p><strong>URL de prueba:</strong><br><code style='word-break: break-all;'>{$url}</code></p>";

// Realizar solicitud
$response = @file_get_contents($url);

if ($response === false) {
    $error = error_get_last();
    echo "<p style='color: red;'><strong>❌ Error:</strong> No se pudo conectar con la API de YouTube</p>";
    echo "<p>Detalles: " . $error['message'] . "</p>";
    
    // Verificar si allow_url_fopen está habilitado
    if (!ini_get('allow_url_fopen')) {
        echo "<p style='color: orange;'><strong>⚠️ Advertencia:</strong> allow_url_fopen está deshabilitado en tu configuración de PHP. Necesitas habilitarlo en php.ini</p>";
    }
    exit();
}

// Decodificar respuesta
$data = json_decode($response, true);

if (isset($data['error'])) {
    echo "<p style='color: red;'><strong>❌ Error de API:</strong></p>";
    echo "<pre>" . print_r($data['error'], true) . "</pre>";
    
    if (isset($data['error']['code'])) {
        switch ($data['error']['code']) {
            case 400:
                echo "<p><strong>Posibles causas:</strong> API Key inválida o mal formada</p>";
                break;
            case 403:
                echo "<p><strong>Posibles causas:</strong> API Key sin permisos o cuota excedida</p>";
                break;
            case 404:
                echo "<p><strong>Posibles causas:</strong> Canal no encontrado</p>";
                break;
        }
    }
    exit();
}

// Verificar si hay información del canal
if (!isset($data['items']) || empty($data['items'])) {
    echo "<p style='color: orange;'><strong>⚠️ Advertencia:</strong> No se encontró información del canal</p>";
    echo "<p>Respuesta completa:</p>";
    echo "<pre>" . print_r($data, true) . "</pre>";
    exit();
}

// Mostrar información del canal
$channel = $data['items'][0];
echo "<p style='color: green;'><strong>✅ Conexión exitosa!</strong></p>";
echo "<hr>";

echo "<h2>3. Información del Canal</h2>";
echo "<p><strong>Nombre:</strong> " . $channel['snippet']['title'] . "</p>";
echo "<p><strong>Descripción:</strong> " . substr($channel['snippet']['description'], 0, 200) . "...</p>";
echo "<p><strong>Fecha de creación:</strong> " . date('d/m/Y', strtotime($channel['snippet']['publishedAt'])) . "</p>";

if (isset($channel['statistics'])) {
    echo "<h3>Estadísticas:</h3>";
    echo "<ul>";
    echo "<li><strong>Suscriptores:</strong> " . number_format($channel['statistics']['subscriberCount'] ?? 0) . "</li>";
    echo "<li><strong>Videos:</strong> " . number_format($channel['statistics']['videoCount'] ?? 0) . "</li>";
    echo "<li><strong>Vistas totales:</strong> " . number_format($channel['statistics']['viewCount'] ?? 0) . "</li>";
    echo "</ul>";
}

echo "<hr>";

// Probar obtención de videos
echo "<h2>4. Probando obtención de videos</h2>";

$url = "https://www.googleapis.com/youtube/v3/search?key={$apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults=5";

$response = @file_get_contents($url);

if ($response === false) {
    echo "<p style='color: red;'><strong>❌ Error:</strong> No se pudieron obtener los videos</p>";
    exit();
}

$data = json_decode($response, true);

if (isset($data['error'])) {
    echo "<p style='color: red;'><strong>❌ Error de API:</strong></p>";
    echo "<pre>" . print_r($data['error'], true) . "</pre>";
    exit();
}

if (!isset($data['items']) || empty($data['items'])) {
    echo "<p style='color: orange;'><strong>⚠️ No se encontraron videos</strong></p>";
    exit();
}

echo "<p style='color: green;'><strong>✅ Se encontraron " . count($data['items']) . " videos</strong></p>";
echo "<h3>Últimos 5 videos:</h3>";
echo "<ul>";

foreach ($data['items'] as $item) {
    if ($item['id']['kind'] === 'youtube#video') {
        echo "<li>";
        echo "<strong>" . $item['snippet']['title'] . "</strong><br>";
        echo "ID: " . $item['id']['videoId'] . "<br>";
        echo "Fecha: " . date('d/m/Y', strtotime($item['snippet']['publishedAt']));
        echo "</li><br>";
    }
}

echo "</ul>";
echo "<hr>";

echo "<h2>5. Resultado Final</h2>";
echo "<p style='color: green; font-size: 18px;'><strong>✅ ¡Todo está funcionando correctamente!</strong></p>";
echo "<p>Ahora puedes usar la sincronización de YouTube sin problemas.</p>";
echo "<p><a href='youtube_sync.php' style='background: #dc2626; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px; display: inline-block; margin-top: 10px;'>Ir a Sincronización de YouTube</a></p>";

?>

<style>
    body {
        font-family: Arial, sans-serif;
        max-width: 900px;
        margin: 20px auto;
        padding: 20px;
        background: #f5f5f5;
    }
    h1 { color: #333; }
    h2 { color: #666; margin-top: 20px; }
    h3 { color: #888; }
    code {
        background: #f0f0f0;
        padding: 2px 6px;
        border-radius: 3px;
        font-size: 12px;
    }
    pre {
        background: #f0f0f0;
        padding: 10px;
        border-radius: 5px;
        overflow-x: auto;
    }
    ul {
        line-height: 1.8;
    }
    hr {
        border: none;
        border-top: 2px solid #ddd;
        margin: 20px 0;
    }
</style>

