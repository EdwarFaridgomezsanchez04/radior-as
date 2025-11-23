<?php
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');
require_once('../includes/Historial.php');
require_once('../config/config.php');
require_once('../config/translations.php');

$conex = new database();
$con = $conex->conectar();

// Verificar si la tabla existe y crearla si no existe
try {
    // Verificar si la tabla existe
    $result = $con->query("SHOW TABLES LIKE 'historial'");
    if ($result->rowCount() == 0) {
        // Crear la tabla si no existe
        $create_table_sql = "CREATE TABLE historial (
            id INT AUTO_INCREMENT PRIMARY KEY,
            tipo_evento VARCHAR(50) NOT NULL,
            categoria VARCHAR(50) NOT NULL,
            usuario_id INT DEFAULT NULL,
            usuario_nombre VARCHAR(100) DEFAULT NULL,
            ip_address VARCHAR(45) DEFAULT NULL,
            user_agent TEXT,
            entidad_id INT DEFAULT NULL,
            entidad_nombre VARCHAR(255) DEFAULT NULL,
            accion VARCHAR(100) NOT NULL,
            detalles TEXT DEFAULT NULL,
            fecha_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_tipo_evento (tipo_evento),
            INDEX idx_categoria (categoria),
            INDEX idx_usuario_id (usuario_id),
            INDEX idx_fecha_hora (fecha_hora)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci";
        
        $con->exec($create_table_sql);
    }
    
    $historial = new Historial($con);
} catch (Exception $e) {
    // Mostrar error detallado
    $error_message = $e->getMessage();
    error_log("Error en historial: " . $error_message);
    die('
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Error - Historial</title>
        <script src="https://cdn.tailwindcss.com"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    </head>
    <body class="bg-gray-100">
        <div class="min-h-screen flex items-center justify-center p-4">
            <div class="bg-white rounded-2xl shadow-xl p-8 max-w-3xl w-full">
                <div class="text-center mb-6">
                    <i class="fas fa-exclamation-triangle text-6xl text-red-500 mb-4"></i>
                    <h1 class="text-3xl font-black text-gray-900">Error en Historial</h1>
                </div>
                
                <div class="bg-red-50 border border-red-200 rounded-xl p-4 mb-6">
                    <p class="text-red-800"><strong>Error:</strong> ' . htmlspecialchars($error_message) . '</p>
                </div>
                
                <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-6 mb-6">
                    <h2 class="font-bold text-yellow-900 mb-3">✅ Tabla Creada Automáticamente</h2>
                    <p class="text-yellow-800 mb-4">La tabla <code class="bg-yellow-100 px-2 py-1 rounded">historial</code> fue creada automáticamente.</p>
                    <p class="text-sm text-yellow-700">Si el error persiste, por favor recarga la página.</p>
                </div>
                
                <div class="flex gap-3">
                    <a href="historial.php" class="flex-1 bg-radio-teal text-white px-6 py-3 rounded-xl font-bold text-center hover:bg-radio-cyan transition-colors">
                        <i class="fas fa-redo mr-2"></i>Recargar
                    </a>
                    <a href="dashboard.php" class="flex-1 bg-gray-500 text-white px-6 py-3 rounded-xl font-bold text-center hover:bg-gray-600 transition-colors">
                        <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
                    </a>
                </div>
            </div>
        </div>
    </body>
    </html>');
}

// Obtener idioma
$lang = isset($_GET['lang']) ? $_GET['lang'] : 'es';
$t = $translations[$lang];

// Filtros
$filtros = [];
if (isset($_GET['tipo_evento']) && !empty($_GET['tipo_evento'])) {
    $filtros['tipo_evento'] = $_GET['tipo_evento'];
}
if (isset($_GET['categoria']) && !empty($_GET['categoria'])) {
    $filtros['categoria'] = $_GET['categoria'];
}
if (isset($_GET['usuario_id']) && !empty($_GET['usuario_id'])) {
    $filtros['usuario_id'] = $_GET['usuario_id'];
}

// Paginación
$total_registros = $historial->contar($filtros);
$registros_por_pagina = 50;
$total_paginas = ceil($total_registros / $registros_por_pagina);
$pagina_actual = isset($_GET['pagina']) ? max(1, intval($_GET['pagina'])) : 1;
$offset = ($pagina_actual - 1) * $registros_por_pagina;

// Obtener historial
$eventos = $historial->obtenerHistorial($filtros, $registros_por_pagina, $offset);

// Obtener estadísticas
$estadisticas = $historial->obtenerEstadisticas();

// Iconos para tipos de eventos
$iconos_eventos = [
    'login' => 'fa-sign-in-alt',
    'logout' => 'fa-sign-out-alt',
    'create' => 'fa-plus-circle',
    'update' => 'fa-edit',
    'delete' => 'fa-trash',
    'view' => 'fa-eye',
    'download' => 'fa-download',
    'play' => 'fa-play',
    'contact' => 'fa-envelope',
    'click' => 'fa-mouse-pointer'
];

// Colores para tipos de eventos
$colores_eventos = [
    'login' => 'text-green-600',
    'logout' => 'text-gray-600',
    'create' => 'text-blue-600',
    'update' => 'text-yellow-600',
    'delete' => 'text-red-600',
    'view' => 'text-purple-600',
    'download' => 'text-indigo-600',
    'play' => 'text-pink-600',
    'contact' => 'text-teal-600',
    'click' => 'text-orange-600'
];
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Actividades - Admin</title>
    <?php include('../includes/favicon.php'); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'radio-teal': '#20B2AA',
                        'radio-cyan': '#00CED1',
                        'radio-dark': '#1a202c',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Header simple para admin -->
    <header class="bg-gradient-to-r from-gray-800 to-gray-900 shadow-lg border-b border-gray-700 mb-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <h1 class="text-2xl font-black text-white">Historial de Actividades</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300">
                        <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
                    </a>
                    <a href="../includes/salir.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300">
                        <i class="fas fa-sign-out-alt mr-2"></i>Cerrar Sesión
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <div class="min-h-screen p-6">
        <div class="max-w-7xl mx-auto">
            <!-- Estadísticas principales -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <div class="flex items-center justify-between">
                    <div>
                        <p class="text-gray-600">Registro completo de todas las acciones del sistema</p>
                    </div>
                    <div class="text-right">
                        <p class="text-2xl font-bold text-radio-teal"><?php echo number_format($total_registros); ?></p>
                        <p class="text-sm text-gray-500">Total de eventos</p>
                    </div>
                </div>
            </div>
            
            <!-- Estadísticas -->
            <?php if (!empty($estadisticas)): ?>
            <div class="grid grid-cols-2 md:grid-cols-5 lg:grid-cols-10 gap-4 mb-6">
                <?php foreach ($estadisticas as $stat): ?>
                <div class="bg-white rounded-xl p-4 shadow-md hover:shadow-lg transition-shadow">
                    <div class="text-center">
                        <?php 
                        $icono = $iconos_eventos[$stat['tipo_evento']] ?? 'fa-circle';
                        $color = $colores_eventos[$stat['tipo_evento']] ?? 'text-gray-600';
                        ?>
                        <i class="fas <?php echo $icono; ?> <?php echo $color; ?> text-2xl mb-2"></i>
                        <p class="text-2xl font-bold <?php echo $color; ?>"><?php echo number_format($stat['total']); ?></p>
                        <p class="text-xs text-gray-500 mt-1"><?php echo ucfirst($stat['tipo_evento']); ?></p>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
            
            <!-- Filtros -->
            <div class="bg-white rounded-2xl shadow-lg p-6 mb-6">
                <form method="GET" action="" class="grid grid-cols-1 md:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipo de Evento</label>
                        <select name="tipo_evento" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-radio-teal focus:border-transparent">
                            <option value="">Todos</option>
                            <option value="login">Login</option>
                            <option value="logout">Logout</option>
                            <option value="create">Crear</option>
                            <option value="update">Editar</option>
                            <option value="delete">Eliminar</option>
                            <option value="view">Visualizar</option>
                            <option value="download">Descargar</option>
                            <option value="play">Reproducir</option>
                            <option value="contact">Contacto</option>
                            <option value="click">Click</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Categoría</label>
                        <select name="categoria" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-radio-teal focus:border-transparent">
                            <option value="">Todas</option>
                            <option value="usuario">Usuario</option>
                            <option value="podcast">Podcast</option>
                            <option value="noticia">Noticia</option>
                            <option value="programa">Programa</option>
                            <option value="contacto">Contacto</option>
                        </select>
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Usuario</label>
                        <select name="usuario_id" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-radio-teal focus:border-transparent">
                            <option value="">Todos</option>
                            <?php
                            $usuarios = $con->query("SELECT id, username, nombre FROM usuarios ORDER BY username")->fetchAll(PDO::FETCH_ASSOC);
                            foreach ($usuarios as $usuario):
                            ?>
                            <option value="<?php echo $usuario['id']; ?>"><?php echo htmlspecialchars($usuario['username']); ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div class="flex items-end">
                        <button type="submit" class="w-full bg-radio-teal text-white px-6 py-2 rounded-lg font-bold hover:bg-radio-cyan transition-colors">
                            <i class="fas fa-filter mr-2"></i>Filtrar
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Lista de eventos -->
            <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Fecha/Hora</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Evento</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Categoría</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Usuario</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Entidad</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">Acción</th>
                                <th class="px-6 py-4 text-left text-xs font-bold text-gray-600 uppercase">IP</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-200">
                            <?php if (empty($eventos)): ?>
                            <tr>
                                <td colspan="7" class="px-6 py-12 text-center">
                                    <i class="fas fa-history text-6xl text-gray-300 mb-4"></i>
                                    <h3 class="text-xl font-bold text-gray-700 mb-2">No hay eventos aún</h3>
                                    <p class="text-gray-500 mb-4">El historial aparecerá aquí cuando ocurran acciones en el sistema.</p>
                                    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 max-w-2xl mx-auto">
                                        <p class="text-sm text-blue-800"><strong>💡 Tip:</strong> Para ver eventos, realiza acciones como:</p>
                                        <ul class="text-sm text-blue-700 mt-2 space-y-1 list-disc list-inside">
                                            <li>Editar o crear un usuario</li>
                                            <li>Reproducir o descargar un podcast</li>
                                            <li>Enviar un formulario de contacto</li>
                                            <li>Cerrar sesión y volver a iniciar</li>
                                        </ul>
                                    </div>
                                </td>
                            </tr>
                            <?php else: ?>
                                <?php foreach ($eventos as $evento): ?>
                                <tr class="hover:bg-gray-50 transition-colors">
                                    <td class="px-6 py-4 text-sm text-gray-600">
                                        <?php echo date('d/m/Y H:i:s', strtotime($evento['fecha_hora'])); ?>
                                    </td>
                                    <td class="px-6 py-4">
                                        <?php 
                                        $icono = $iconos_eventos[$evento['tipo_evento']] ?? 'fa-circle';
                                        $color = $colores_eventos[$evento['tipo_evento']] ?? 'text-gray-600';
                                        ?>
                                        <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold <?php echo $color; ?> bg-opacity-10">
                                            <i class="fas <?php echo $icono; ?> mr-2"></i>
                                            <?php echo ucfirst($evento['tipo_evento']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php echo ucfirst($evento['categoria']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php if ($evento['usuario_nombre']): ?>
                                            <div class="flex items-center">
                                                <i class="fas fa-user-circle mr-2 text-gray-400"></i>
                                                <?php echo htmlspecialchars($evento['usuario_nombre']); ?>
                                            </div>
                                        <?php else: ?>
                                            <span class="text-gray-400">Visitante</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php if ($evento['entidad_nombre']): ?>
                                            <?php echo htmlspecialchars($evento['entidad_nombre']); ?>
                                        <?php else: ?>
                                            <span class="text-gray-400">-</span>
                                        <?php endif; ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-700">
                                        <?php echo htmlspecialchars($evento['accion']); ?>
                                    </td>
                                    <td class="px-6 py-4 text-sm text-gray-500 font-mono">
                                        <?php echo htmlspecialchars($evento['ip_address']); ?>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_paginas > 1): ?>
                <div class="px-6 py-4 border-t border-gray-200 flex items-center justify-between">
                    <div class="text-sm text-gray-600">
                        Mostrando <?php echo $offset + 1; ?> - <?php echo min($offset + $registros_por_pagina, $total_registros); ?> de <?php echo $total_registros; ?> eventos
                    </div>
                    <div class="flex gap-2">
                        <?php if ($pagina_actual > 1): ?>
                        <a href="?pagina=<?php echo $pagina_actual - 1; ?>&<?php echo http_build_query($filtros); ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-chevron-left"></i>
                        </a>
                        <?php endif; ?>
                        
                        <?php
                        $start = max(1, $pagina_actual - 2);
                        $end = min($total_paginas, $pagina_actual + 2);
                        
                        for ($i = $start; $i <= $end; $i++):
                        ?>
                        <a href="?pagina=<?php echo $i; ?>&<?php echo http_build_query($filtros); ?>" 
                           class="px-4 py-2 rounded-lg <?php echo $i == $pagina_actual ? 'bg-radio-teal text-white' : 'bg-white text-gray-700 border border-gray-300 hover:bg-gray-50'; ?>">
                            <?php echo $i; ?>
                        </a>
                        <?php endfor; ?>
                        
                        <?php if ($pagina_actual < $total_paginas): ?>
                        <a href="?pagina=<?php echo $pagina_actual + 1; ?>&<?php echo http_build_query($filtros); ?>" 
                           class="px-4 py-2 bg-white border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50">
                            <i class="fas fa-chevron-right"></i>
                        </a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
</body>
</html>

