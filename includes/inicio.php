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

// Función para responder con JSON
function jsonResponse($success, $message = '', $redirect = '') {
    header('Content-Type: application/json');
    echo json_encode([
        'success' => $success,
        'message' => $message,
        'redirect' => $redirect
    ]);
    exit();
}

// Detectar si es una petición AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    if (empty($username) || empty($password)) {
        if ($isAjax) {
            jsonResponse(false, 'Ningún dato puede estar vacío');
        } else {
            echo '<script>alert("Ningún dato puede estar vacío")</script>';
            echo '<script>window.location = "../login.php"</script>';
            exit();
        }
    }

    // Buscar usuario por username o email
    $sql = $con->prepare("SELECT * FROM usuarios WHERE username = :username OR email = :email");
    $sql->bindParam(':username', $username, PDO::PARAM_STR);
    $sql->bindParam(':email', $username, PDO::PARAM_STR);
    $sql->execute();
    $fila = $sql->fetch(PDO::FETCH_ASSOC);

    if ($fila && $fila['activo'] == 1) {
        if (password_verify($password, $fila['password'])) {
            $_SESSION['user_id'] = $fila['id'];
            $_SESSION['username'] = $fila['username'];
            $_SESSION['nombre'] = $fila['nombre'];
            $_SESSION['email'] = $fila['email'];
            $_SESSION['rol'] = $fila['rol'];
            
            // Registrar evento de login en el historial
            $historial->registrar('login', 'usuario', 'Usuario inició sesión', [
                'usuario_id' => $fila['id'],
                'usuario_nombre' => $fila['username'],
                'detalles' => [
                    'email' => $fila['email'],
                    'rol' => $fila['rol']
                ]
            ]);

            // Responder según el tipo de petición
            if ($isAjax) {
                jsonResponse(true, 'Login exitoso', 'admin/dashboard.php');
            } else {
                header("Location: ../admin/dashboard.php");
                exit();
            }
        } else {
            if ($isAjax) {
                jsonResponse(false, 'Contraseña incorrecta');
            } else {
                echo '<script>alert("Contraseña incorrecta")</script>';
                echo '<script>window.location = "../login.php"</script>';
                exit();
            }
        }
    } else {
        if ($isAjax) {
            jsonResponse(false, 'Usuario no encontrado o inactivo');
        } else {
            echo '<script>alert("Usuario no encontrado o inactivo")</script>';
            echo '<script>window.location = "../login.php"</script>';
            exit();
        }
    }
}

// Registro de nuevos usuarios
if (isset($_POST['register'])) {
    $username = trim($_POST['username']);
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (empty($username) || empty($nombre) || empty($email) || empty($password)) {
        echo '<script>alert("Todos los campos son obligatorios")</script>';
        echo '<script>window.location = "../register.php"</script>';
        exit();
    }

    if ($password !== $confirm_password) {
        echo '<script>alert("Las contraseñas no coinciden")</script>';
        echo '<script>window.location = "../register.php"</script>';
        exit();
    }

    // Verificar si el usuario ya existe
    $sql = $con->prepare("SELECT * FROM usuarios WHERE username = :username OR email = :email");
    $sql->bindParam(':username', $username, PDO::PARAM_STR);
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    $sql->execute();
    
    if ($sql->rowCount() > 0) {
        echo '<script>alert("El usuario o email ya existe")</script>';
        echo '<script>window.location = "../register.php"</script>';
        exit();
    }

    // Crear nuevo usuario
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $sql = $con->prepare("INSERT INTO usuarios (username, password, nombre, email, rol, activo) VALUES (:username, :password, :nombre, :email, 'editor', 1)");
    $sql->bindParam(':username', $username, PDO::PARAM_STR);
    $sql->bindParam(':password', $hashed_password, PDO::PARAM_STR);
    $sql->bindParam(':nombre', $nombre, PDO::PARAM_STR);
    $sql->bindParam(':email', $email, PDO::PARAM_STR);
    
    if ($sql->execute()) {
        echo '<script>alert("Usuario registrado exitosamente. Ahora puedes iniciar sesión.")</script>';
        echo '<script>window.location = "../login.php"</script>';
    } else {
        echo '<script>alert("Error al registrar usuario")</script>';
        echo '<script>window.location = "../register.php"</script>';
    }
    exit();
}
?>
