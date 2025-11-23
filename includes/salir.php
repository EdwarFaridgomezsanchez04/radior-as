<?php
// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../config/conexion.php');
require_once(__DIR__ . '/Historial.php');
$conex = new database();
$con = $conex->conectar();
$historial = new Historial($con);

// Registrar evento de logout en el historial (antes de destruir la sesión)
if (isset($_SESSION['user_id'])) {
    $historial->registrar('logout', 'usuario', 'Usuario cerró sesión', [
        'usuario_id' => $_SESSION['user_id'],
        'usuario_nombre' => $_SESSION['username'],
        'detalles' => [
            'rol' => $_SESSION['rol'] ?? ''
        ]
    ]);
}

// Limpiar todas las variables de sesión
unset($_SESSION['user_id']);
unset($_SESSION['username']);
unset($_SESSION['nombre']);
unset($_SESSION['email']);
unset($_SESSION['rol']);

session_destroy();
session_write_close();

header("Location: ../login.php");
exit();
?>
