<?php
/**
 * Configuración de Favicon para RadioRías
 * Favicon circular con diseño de radio
 */

// Detectar ruta base
$base_path = (strpos($_SERVER['PHP_SELF'], '/admin/') !== false) ? '../' : '';
?>

<!-- Favicon circular para todos los navegadores -->
<link rel="icon" type="image/png" href="<?php echo $base_path; ?>assets/images/logo.png">
<link rel="shortcut icon" href="<?php echo $base_path; ?>assets/images/logo.png">

<!-- Meta tags para diferentes dispositivos -->
<meta name="theme-color" content="#00CED1">
<meta name="msapplication-TileColor" content="#00CED1">
