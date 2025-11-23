<?php
// Iniciar sesión solo si no está ya iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
require_once(__DIR__ . '/../config/conexion.php');
$conex = new database();
$con = $conex->conectar();

if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    session_unset(); // Limpia todas las variables de sesión
    session_destroy(); // Destruye la sesión actual

    echo "<script>alert('INGRESE CREDENCIALES DE LOGIN');</script>";
    echo "<script>window.location='../login.php';</script>";
    exit();
}

// Verificar que el usuario sigue activo en la base de datos
$sql = $con->prepare("SELECT activo FROM usuarios WHERE id = :user_id");
$sql->bindParam(':user_id', $_SESSION['user_id'], PDO::PARAM_INT);
$sql->execute();
$user = $sql->fetch(PDO::FETCH_ASSOC);

if (!$user || $user['activo'] != 1) {
    session_unset();
    session_destroy();
    echo "<script>alert('Su cuenta ha sido desactivada');</script>";
    echo "<script>window.location='../login.php';</script>";
    exit();
}
?>
