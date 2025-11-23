<?php
echo "<h2>Configuración de Directorios de Subida</h2>";

// Directorios necesarios
$directories = [
    'uploads',
    'uploads/podcasts',
    'uploads/podcasts/images',
    'uploads/podcasts/audio'
];

echo "<h3>Creando directorios necesarios:</h3>";
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        if (mkdir($dir, 0755, true)) {
            echo "<p>✅ Creado: $dir</p>";
        } else {
            echo "<p>❌ Error creando: $dir</p>";
        }
    } else {
        echo "<p>✅ Ya existe: $dir</p>";
    }
}

// Verificar permisos
echo "<h3>Verificando permisos:</h3>";
foreach ($directories as $dir) {
    if (is_dir($dir)) {
        $perms = substr(sprintf('%o', fileperms($dir)), -4);
        $writable = is_writable($dir) ? '✅ Escribible' : '❌ No escribible';
        echo "<p>$dir - Permisos: $perms - $writable</p>";
    }
}

// Crear archivo .htaccess si no existe
$htaccess_content = '# Proteger directorio de uploads
Options -Indexes

# Permitir solo ciertos tipos de archivos
<FilesMatch "\.(jpg|jpeg|png|gif|webp|mp3|wav|ogg|m4a|aac)$">
    Order Allow,Deny
    Allow from all
</FilesMatch>

# Denegar acceso a archivos PHP
<FilesMatch "\.php$">
    Order Deny,Allow
    Deny from all
</FilesMatch>

# Denegar acceso a archivos de configuración
<FilesMatch "\.(htaccess|htpasswd|ini|log|sh|sql)$">
    Order Deny,Allow
    Deny from all
</FilesMatch>';

if (!file_exists('uploads/.htaccess')) {
    if (file_put_contents('uploads/.htaccess', $htaccess_content)) {
        echo "<p>✅ Creado archivo de seguridad: uploads/.htaccess</p>";
    } else {
        echo "<p>❌ Error creando archivo de seguridad</p>";
    }
} else {
    echo "<p>✅ Ya existe archivo de seguridad: uploads/.htaccess</p>";
}

echo "<h3>Estructura final:</h3>";
echo "<pre>";
echo "uploads/\n";
echo "├── .htaccess (protección)\n";
echo "└── podcasts/\n";
echo "    ├── images/ (imágenes de podcasts)\n";
echo "    └── audio/ (archivos de audio)\n";
echo "</pre>";

echo "<p><strong>✅ Configuración completada!</strong></p>";
echo "<p><a href='admin/podcasts.php'>← Ir a gestión de podcasts</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
h2, h3 { color: #333; }
p { margin: 8px 0; }
pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style>
