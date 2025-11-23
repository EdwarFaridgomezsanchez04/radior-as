<?php
echo "<h2>Test de Conexión para Podcasts</h2>";

// Verificar que el archivo de conexión existe
$conexion_path = __DIR__ . '/config/conexion.php';
echo "<p><strong>Ruta de conexión:</strong> $conexion_path</p>";
echo "<p><strong>Archivo existe:</strong> " . (file_exists($conexion_path) ? 'SÍ' : 'NO') . "</p>";

if (file_exists($conexion_path)) {
    require_once($conexion_path);
    
    try {
        $conex = new database();
        $con = $conex->conectar();
        echo "<p><strong>Conexión a BD:</strong> ✅ EXITOSA</p>";
        
        // Probar consulta de podcasts
        $stmt = $con->query("SELECT COUNT(*) as total FROM podcasts WHERE activo = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p><strong>Podcasts activos:</strong> {$result['total']}</p>";
        
        // Probar consulta de categorías
        $stmt = $con->query("SELECT COUNT(*) as total FROM categorias_podcast WHERE activo = 1");
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        echo "<p><strong>Categorías activas:</strong> {$result['total']}</p>";
        
    } catch (Exception $e) {
        echo "<p><strong>Error de conexión:</strong> " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p><strong>❌ Error:</strong> No se encuentra el archivo de conexión</p>";
    echo "<p>Archivos en config/:</p>";
    if (is_dir('config')) {
        $files = scandir('config');
        foreach ($files as $file) {
            if ($file != '.' && $file != '..') {
                echo "<li>$file</li>";
            }
        }
    } else {
        echo "<p>La carpeta config/ no existe</p>";
    }
}

echo "<p><a href='pages/podcasts.php'>← Ir a página de podcasts</a></p>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; }
p { margin: 10px 0; }
</style>
