<?php
// Crear tabla de contactos
include __DIR__ . '/../config/config.php';

try {
    $pdo = new PDO("mysql:host=" . DB_HOST, DB_USER, DB_PASS);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    
    // Crear base de datos si no existe
    $pdo->exec("CREATE DATABASE IF NOT EXISTS " . DB_NAME . " CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE " . DB_NAME);
    
    // Crear tabla de contactos
    $sql = "CREATE TABLE IF NOT EXISTS contacts (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        email VARCHAR(255) NOT NULL,
        subject VARCHAR(500) NOT NULL,
        message TEXT NOT NULL,
        ip_address VARCHAR(45),
        user_agent TEXT,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        status ENUM('new', 'read', 'replied') DEFAULT 'new',
        INDEX idx_created_at (created_at),
        INDEX idx_status (status),
        INDEX idx_email (email)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
    
    $pdo->exec($sql);
    
    echo "✅ Tabla 'contacts' creada exitosamente\n";
    echo "📊 Estructura de la tabla:\n";
    echo "- id: ID único autoincremental\n";
    echo "- name: Nombre del contacto\n";
    echo "- email: Email del contacto\n";
    echo "- subject: Asunto del mensaje\n";
    echo "- message: Mensaje completo\n";
    echo "- ip_address: IP del usuario\n";
    echo "- user_agent: Navegador del usuario\n";
    echo "- created_at: Fecha de creación\n";
    echo "- status: Estado del mensaje (new/read/replied)\n";
    
} catch(PDOException $e) {
    echo "❌ Error: " . $e->getMessage() . "\n";
}
?>
