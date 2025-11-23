<?php
/**
 * Tracking automático de visitas
 * Incluir este archivo en todas las páginas públicas
 */

// Verificar si ya hay sesión iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Solo registrar si no es una petición AJAX y no es un bot
if (!empty($_SERVER['REQUEST_URI']) && 
    strpos($_SERVER['REQUEST_URI'], '/api/') === false &&
    strpos($_SERVER['REQUEST_URI'], '/admin/') === false) {
    
    try {
        // Incluir clase Tracker
        require_once(__DIR__ . '/Tracker.php');
        require_once(__DIR__ . '/../config/conexion.php');
        
        $conex = new database();
        $con = $conex->conectar();
        $tracker = new Tracker($con);
        
        // Obtener URL y título de la página
        $page_url = $_SERVER['REQUEST_URI'] ?? '';
        $page_title = isset($GLOBALS['page_title']) ? $GLOBALS['page_title'] : '';
        
        // Registrar visita
        $tracker->trackVisit($page_url, $page_title);
        
    } catch (Exception $e) {
        // Silenciar errores para no afectar el sitio
        error_log("Error en tracking: " . $e->getMessage());
    }
}

