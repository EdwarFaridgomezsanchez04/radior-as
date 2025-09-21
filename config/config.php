<?php
// Configuración principal de RadioRías
// Este archivo contiene todas las configuraciones del sitio

// Configuración de la base de datos (si se necesita en el futuro)
define('DB_HOST', 'localhost');
define('DB_NAME', 'radio_rias');
define('DB_USER', 'root');
define('DB_PASS', '');

// Configuración del sitio
define('SITE_NAME', 'RadioRías');
define('SITE_URL', 'http://localhost/Radio_Rias');
define('SITE_DESCRIPTION', 'RadioRías - Tu radio online favorita');
define('SITE_ADDRESS', 'Rúa Uruguay 18, piso 2C, 36201 Vigo, Pontevedra, Galicia');
define('SITE_PHONE', '+34 986 123 456');
define('SITE_EMAIL', 'contacto@radiorias.com');

// Configuración de streaming de radio
define('RADIO_STREAM_URL', 'http://88.150.230.110:8950/stream');
define('RADIO_PROXY_URL', 'proxy.php');

// Configuración de redes sociales
define('FACEBOOK_URL', 'https://www.facebook.com/rmorrazo');
define('INSTAGRAM_URL', 'https://www.instagram.com/radiomorrazo');
define('EMAIL_CONTACT', 'contacto@radiorias.com');
define('PHONE_CONTACT', '+34986123456');

// Configuración de idiomas disponibles
$available_languages = [
    'es' => 'Español',
    'en' => 'English',
    'gl' => 'Galego'
];

// Configuración de páginas disponibles
$available_pages = [
    'home' => 'Inicio',
    'about' => 'Sobre Nosotros',
    'programs' => 'Programas',
    'podcasts' => 'Podcasts',
    'audiobooks' => 'Audiolibros',
    'contact' => 'Contacto'
];

// Configuración de desarrollo
define('DEBUG_MODE', true);
define('CACHE_ENABLED', false);

// Configuración de seguridad
define('SESSION_TIMEOUT', 3600); // 1 hora
define('MAX_LOGIN_ATTEMPTS', 5);

// Configuración de archivos
define('UPLOAD_MAX_SIZE', 10485760); // 10MB
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'gif', 'mp3', 'wav']);

// Configuración de email
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_USER', 'contacto@radiorias.com');
define('SMTP_PASS', ''); // Configurar cuando sea necesario

// Configuración de cache
define('CACHE_DIR', 'cache/');
define('CACHE_TIME', 3600); // 1 hora

// Configuración de logs
define('LOG_DIR', 'logs/');
define('LOG_LEVEL', 'INFO'); // DEBUG, INFO, WARNING, ERROR

// Función para obtener configuración
function getConfig($key, $default = null) {
    return defined($key) ? constant($key) : $default;
}

// Función para verificar si estamos en desarrollo
function isDevelopment() {
    return DEBUG_MODE && (isset($_SERVER['HTTP_HOST']) && strpos($_SERVER['HTTP_HOST'], 'localhost') !== false);
}

// Función para generar URL completa
function fullUrl($path = '') {
    if (!isset($_SERVER['HTTP_HOST'])) {
        return SITE_URL . '/' . ltrim($path, '/');
    }
    $protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'];
    $base = rtrim(dirname($_SERVER['SCRIPT_NAME']), '/');
    return $protocol . '://' . $host . $base . '/' . ltrim($path, '/');
}

// Función para obtener la URL del stream de radio
function getRadioStreamUrl() {
    return fullUrl(RADIO_PROXY_URL);
}

// Configuración de timezone
date_default_timezone_set('Europe/Madrid');

// Configuración de errores (solo en desarrollo)
if (isDevelopment()) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
}

// Crear directorios necesarios si no existen
$directories = [CACHE_DIR, LOG_DIR];
foreach ($directories as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}
?>
