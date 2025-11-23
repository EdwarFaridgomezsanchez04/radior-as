<?php
echo "<h2>Test de Acceso a Archivos de Podcasts</h2>";

require_once('config/conexion.php');

$conex = new database();
$con = $conex->conectar();

// Obtener podcasts con archivos
$sql = "SELECT id, titulo_es, imagen, archivo FROM podcasts WHERE activo = 1 AND (imagen IS NOT NULL OR archivo IS NOT NULL) LIMIT 5";
$podcasts = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);

echo "<h3>Verificación de archivos:</h3>";

foreach ($podcasts as $podcast) {
    echo "<div style='border: 1px solid #ccc; margin: 10px 0; padding: 10px;'>";
    echo "<h4>Podcast: " . htmlspecialchars($podcast['titulo_es']) . "</h4>";
    
    // Verificar imagen
    if ($podcast['imagen']) {
        $imagen_path = "uploads/podcasts/images/" . $podcast['imagen'];
        $imagen_exists = file_exists($imagen_path);
        echo "<p><strong>Imagen:</strong> " . $podcast['imagen'] . "</p>";
        echo "<p><strong>Ruta:</strong> $imagen_path</p>";
        echo "<p><strong>Existe:</strong> " . ($imagen_exists ? '✅ SÍ' : '❌ NO') . "</p>";
        
        if ($imagen_exists) {
            echo "<p><strong>Tamaño:</strong> " . number_format(filesize($imagen_path) / 1024, 2) . " KB</p>";
            echo "<p><strong>Vista previa:</strong></p>";
            echo "<img src='$imagen_path' style='max-width: 200px; max-height: 150px; border: 1px solid #ddd;' alt='Preview'>";
        }
    } else {
        echo "<p><strong>Imagen:</strong> No tiene</p>";
    }
    
    echo "<hr>";
    
    // Verificar audio
    if ($podcast['archivo']) {
        $audio_path = "uploads/podcasts/audio/" . $podcast['archivo'];
        $audio_exists = file_exists($audio_path);
        echo "<p><strong>Audio:</strong> " . $podcast['archivo'] . "</p>";
        echo "<p><strong>Ruta:</strong> $audio_path</p>";
        echo "<p><strong>Existe:</strong> " . ($audio_exists ? '✅ SÍ' : '❌ NO') . "</p>";
        
        if ($audio_exists) {
            echo "<p><strong>Tamaño:</strong> " . number_format(filesize($audio_path) / 1024 / 1024, 2) . " MB</p>";
            echo "<p><strong>Tipo MIME:</strong> " . mime_content_type($audio_path) . "</p>";
            echo "<p><strong>Reproductor de prueba:</strong></p>";
            echo "<audio controls style='width: 100%;'>";
            echo "<source src='$audio_path' type='audio/mpeg'>";
            echo "Tu navegador no soporta el elemento audio.";
            echo "</audio>";
            echo "<p><a href='$audio_path' download='test_download.mp3' style='background: #007cba; color: white; padding: 5px 10px; text-decoration: none; border-radius: 3px;'>🔽 Descargar</a></p>";
        }
    } else {
        echo "<p><strong>Audio:</strong> No tiene</p>";
    }
    
    echo "</div>";
}

// Verificar estructura de directorios
echo "<h3>Estructura de directorios:</h3>";
$dirs_to_check = [
    'uploads',
    'uploads/podcasts',
    'uploads/podcasts/images',
    'uploads/podcasts/audio'
];

foreach ($dirs_to_check as $dir) {
    $exists = is_dir($dir);
    $writable = $exists ? is_writable($dir) : false;
    echo "<p><strong>$dir:</strong> ";
    echo ($exists ? '✅ Existe' : '❌ No existe');
    if ($exists) {
        echo " - " . ($writable ? '✅ Escribible' : '❌ No escribible');
        $files = glob($dir . '/*');
        echo " - " . count($files) . " archivos";
    }
    echo "</p>";
}

echo "<h3>Archivos en uploads/podcasts/images:</h3>";
if (is_dir('uploads/podcasts/images')) {
    $images = glob('uploads/podcasts/images/*');
    if (empty($images)) {
        echo "<p>No hay imágenes</p>";
    } else {
        foreach ($images as $img) {
            echo "<p>" . basename($img) . " (" . number_format(filesize($img) / 1024, 2) . " KB)</p>";
        }
    }
}

echo "<h3>Archivos en uploads/podcasts/audio:</h3>";
if (is_dir('uploads/podcasts/audio')) {
    $audios = glob('uploads/podcasts/audio/*');
    if (empty($audios)) {
        echo "<p>No hay archivos de audio</p>";
    } else {
        foreach ($audios as $audio) {
            echo "<p>" . basename($audio) . " (" . number_format(filesize($audio) / 1024 / 1024, 2) . " MB)</p>";
        }
    }
}

echo "<p><a href='pages/podcasts.php'>← Ir a página de podcasts</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3, h4 { color: #333; }
p { margin: 5px 0; }
hr { margin: 10px 0; }
</style>
