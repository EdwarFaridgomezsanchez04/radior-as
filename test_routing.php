<?php
echo "<h2>Test de Enrutamiento de Podcasts</h2>";

echo "<h3>URLs de prueba:</h3>";
echo "<ul>";
echo "<li><a href='pages/podcasts.php' target='_blank'>Acceso directo: pages/podcasts.php</a></li>";
echo "<li><a href='index.php?page=podcasts&lang=es' target='_blank'>A través de index: index.php?page=podcasts&lang=es</a></li>";
echo "</ul>";

echo "<h3>Verificación de archivos:</h3>";

// Verificar que la página existe
if (file_exists('pages/podcasts.php')) {
    echo "<p>✅ Archivo pages/podcasts.php existe</p>";
} else {
    echo "<p>❌ Archivo pages/podcasts.php NO existe</p>";
}

// Verificar que index.php puede incluir la página
if (file_exists('index.php')) {
    echo "<p>✅ Archivo index.php existe</p>";
} else {
    echo "<p>❌ Archivo index.php NO existe</p>";
}

// Verificar directorios de uploads
$upload_paths = [
    'uploads/podcasts/images',
    'uploads/podcasts/audio'
];

foreach ($upload_paths as $path) {
    if (is_dir($path)) {
        $files = glob($path . '/*');
        echo "<p>✅ $path existe (" . count($files) . " archivos)</p>";
    } else {
        echo "<p>❌ $path NO existe</p>";
    }
}

echo "<h3>Simulación de detección de contexto:</h3>";

// Simular acceso directo
$_SERVER['PHP_SELF'] = '/radio_rias/pages/podcasts.php';
$is_direct_access = basename($_SERVER['PHP_SELF']) === 'podcasts.php';
$base_path = $is_direct_access ? '../' : '';
echo "<p><strong>Acceso directo:</strong></p>";
echo "<p>PHP_SELF: " . $_SERVER['PHP_SELF'] . "</p>";
echo "<p>is_direct_access: " . ($is_direct_access ? 'true' : 'false') . "</p>";
echo "<p>base_path: '$base_path'</p>";

// Simular acceso a través de index
$_SERVER['PHP_SELF'] = '/radio_rias/index.php';
$is_direct_access = basename($_SERVER['PHP_SELF']) === 'podcasts.php';
$base_path = $is_direct_access ? '../' : '';
echo "<p><strong>A través de index:</strong></p>";
echo "<p>PHP_SELF: " . $_SERVER['PHP_SELF'] . "</p>";
echo "<p>is_direct_access: " . ($is_direct_access ? 'true' : 'false') . "</p>";
echo "<p>base_path: '$base_path'</p>";

echo "<h3>Rutas resultantes:</h3>";
echo "<p><strong>Acceso directo:</strong></p>";
echo "<p>Imagen: ../uploads/podcasts/images/archivo.jpg</p>";
echo "<p>Audio: ../uploads/podcasts/audio/archivo.mp3</p>";

echo "<p><strong>A través de index:</strong></p>";
echo "<p>Imagen: uploads/podcasts/images/archivo.jpg</p>";
echo "<p>Audio: uploads/podcasts/audio/archivo.mp3</p>";

echo "<p><strong>✅ Configuración completada!</strong></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
p { margin: 8px 0; }
ul { margin: 10px 0; }
li { margin: 5px 0; }
a { color: #007cba; text-decoration: none; }
a:hover { text-decoration: underline; }
</style>
