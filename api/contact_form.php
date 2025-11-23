<?php
header('Content-Type: application/json');
require_once '../config/config.php';
require_once '../config/conexion.php';
require_once '../includes/Historial.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método no permitido']);
    exit;
}

// Incluir Historial
$conex = new database();
$con_historial = $conex->conectar();
$historial = new Historial($con_historial);

// Validar campos requeridos
$required_fields = ['name', 'email', 'subject', 'message'];
$errors = [];

foreach ($required_fields as $field) {
    if (empty($_POST[$field])) {
        $errors[] = "El campo '$field' es requerido";
    }
}

// Validar email
if (!empty($_POST['email']) && !filter_var($_POST['email'], FILTER_VALIDATE_EMAIL)) {
    $errors[] = "El email no es válido";
}

if (!empty($errors)) {
    echo json_encode(['success' => false, 'message' => implode(', ', $errors)]);
    exit;
}

try {
    $con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Preparar datos
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    $ip_address = $_SERVER['REMOTE_ADDR'] ?? null;
    $user_agent = $_SERVER['HTTP_USER_AGENT'] ?? null;
    
    // Insertar en la base de datos
    $sql = "INSERT INTO contacts (name, email, subject, message, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)";
    $stmt = $con->prepare($sql);
    $stmt->execute([$name, $email, $subject, $message, $ip_address, $user_agent]);
    $contact_id = $con->lastInsertId();
    
    // Registrar en historial
    $historial->registrar('contact', 'contacto', 'Envió mensaje de contacto', [
        'entidad_id' => $contact_id,
        'entidad_nombre' => $subject,
        'detalles' => [
            'nombre' => $name,
            'email' => $email,
            'mensaje' => substr($message, 0, 200) . '...'
        ]
    ]);
    
    echo json_encode([
        'success' => true, 
        'message' => 'Mensaje enviado correctamente. Te contactaremos pronto.'
    ]);
    
} catch(PDOException $e) {
    error_log("Error en contact_form.php: " . $e->getMessage());
    echo json_encode([
        'success' => false, 
        'message' => 'Error al enviar el mensaje. Inténtalo de nuevo.'
    ]);
}
?>
