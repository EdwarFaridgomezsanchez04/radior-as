<?php
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');

// Verificar que sea admin
if ($_SESSION['rol'] !== 'admin') {
    echo "<script>alert('Acceso denegado. Solo administradores.');</script>";
    echo "<script>window.location='../dashboard.php';</script>";
    exit();
}

$conex = new database();
$con = $conex->conectar();

// Procesar acciones
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'toggle_status':
            $id = (int)$_POST['id'];
            $stmt = $con->prepare("UPDATE podcasts SET activo = NOT activo WHERE id = ?");
            $stmt->execute([$id]);
            break;
            
        case 'toggle_featured':
            $id = (int)$_POST['id'];
            $stmt = $con->prepare("UPDATE podcasts SET destacado = NOT destacado WHERE id = ?");
            $stmt->execute([$id]);
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            // Obtener archivos para eliminar
            $stmt = $con->prepare("SELECT imagen, archivo FROM podcasts WHERE id = ?");
            $stmt->execute([$id]);
            $podcast = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Eliminar archivos físicos
            if ($podcast['imagen'] && file_exists(__DIR__ . '/../uploads/podcasts/images/' . $podcast['imagen'])) {
                unlink(__DIR__ . '/../uploads/podcasts/images/' . $podcast['imagen']);
            }
            if ($podcast['archivo'] && file_exists(__DIR__ . '/../uploads/podcasts/audio/' . $podcast['archivo'])) {
                unlink(__DIR__ . '/../uploads/podcasts/audio/' . $podcast['archivo']);
            }
            
            // Eliminar de la base de datos
            $stmt = $con->prepare("DELETE FROM podcasts WHERE id = ?");
            $stmt->execute([$id]);
            break;
    }
    
    // Redireccionar para evitar reenvío de formulario
    header("Location: podcasts.php");
    exit();
}

// Obtener podcasts con paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Construir consulta con filtros
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(titulo_es LIKE ? OR titulo_gl LIKE ? OR descripcion_es LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($status_filter !== 'all') {
    $where_conditions[] = "activo = ?";
    $params[] = ($status_filter === 'active') ? 1 : 0;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Contar total de registros
$count_sql = "SELECT COUNT(*) FROM podcasts $where_clause";
$count_stmt = $con->prepare($count_sql);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Obtener podcasts
$sql = "SELECT p.*, 
               CASE WHEN pr.nombre_es IS NOT NULL THEN pr.nombre_es ELSE 'Sin programa' END as programa_nombre,
               CASE WHEN cp.nombre_es IS NOT NULL THEN cp.nombre_es ELSE 'Sin categoría' END as categoria_nombre
        FROM podcasts p 
        LEFT JOIN programas pr ON p.programa_id = pr.id 
        LEFT JOIN categorias_podcast cp ON p.categoria_id = cp.id 
        $where_clause 
        ORDER BY p.fecha_creacion DESC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$stmt = $con->prepare($sql);
$stmt->execute($params);
$podcasts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Podcasts - RadioRías Admin</title>
    <?php include('../includes/favicon.php'); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'radio-gray': '#374151',
                        'radio-gray-dark': '#1f2937',
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-gray-700 to-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="dashboard.php" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <img src="../assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-10 h-10 rounded-full">
                    <div>
                        <h1 class="text-2xl font-black text-white">Gestión de Podcasts</h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="youtube_sync.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fab fa-youtube"></i>
                        <span>Sincronizar YouTube</span>
                    </a>
                    <a href="podcast_form.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Nuevo Podcast</span>
                    </a>
                    <a href="../includes/salir.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Buscar por título o descripción..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Todos los estados</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Activos</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactivos</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="podcasts.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-bold transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </div>
            </form>
        </div>

        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
            <?php
            $stats_sql = "SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
                SUM(CASE WHEN destacado = 1 THEN 1 ELSE 0 END) as destacados,
                SUM(reproducciones) as total_reproducciones
                FROM podcasts";
            $stats = $con->query($stats_sql)->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-blue-600 text-sm font-medium">Total Podcasts</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
                    </div>
                    <i class="fas fa-podcast text-blue-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-green-600 text-sm font-medium">Activos</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['activos']; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-yellow-600 text-sm font-medium">Destacados</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['destacados']; ?></p>
                    </div>
                    <i class="fas fa-star text-yellow-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-purple-600 text-sm font-medium">Reproducciones</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_reproducciones']); ?></p>
                    </div>
                    <i class="fas fa-play text-purple-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Lista de podcasts -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">
                    Podcasts (<?php echo $total_records; ?> resultados)
                </h2>
            </div>
            
            <?php if (empty($podcasts)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-podcast text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No se encontraron podcasts</p>
                    <a href="podcast_form.php" class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold transition-colors">
                        <i class="fas fa-plus mr-2"></i>Crear primer podcast
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Podcast</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Programa/Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estadísticas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($podcasts as $podcast): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <?php if ($podcast['imagen']): ?>
                                                    <img class="h-12 w-12 rounded-lg object-cover" 
                                                         src="../uploads/podcasts/images/<?php echo htmlspecialchars($podcast['imagen']); ?>" 
                                                         alt="<?php echo htmlspecialchars($podcast['titulo_es']); ?>">
                                                <?php else: ?>
                                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-podcast text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($podcast['titulo_es']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars(substr($podcast['descripcion_es'], 0, 60)) . '...'; ?>
                                                </div>
                                                <?php if ($podcast['duracion']): ?>
                                                    <div class="text-xs text-gray-400">
                                                        <i class="fas fa-clock mr-1"></i><?php echo $podcast['duracion']; ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo htmlspecialchars($podcast['programa_nombre']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($podcast['categoria_nombre']); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($podcast['fecha'])); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($podcast['fecha_creacion'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-play text-blue-500 mr-1"></i><?php echo number_format($podcast['reproducciones']); ?>
                                        </div>
                                        <div class="text-sm text-gray-500">
                                            <i class="fas fa-download text-green-500 mr-1"></i><?php echo number_format($podcast['descargas']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $podcast['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $podcast['activo'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                            <?php if ($podcast['destacado']): ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-star mr-1"></i>Destacado
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="podcast_form.php?id=<?php echo $podcast['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <form method="POST" class="inline" onsubmit="return confirm('¿Cambiar estado del podcast?')">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="id" value="<?php echo $podcast['id']; ?>">
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Cambiar estado">
                                                    <i class="fas fa-toggle-<?php echo $podcast['activo'] ? 'on' : 'off'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" class="inline" onsubmit="return confirm('¿Cambiar destacado?')">
                                                <input type="hidden" name="action" value="toggle_featured">
                                                <input type="hidden" name="id" value="<?php echo $podcast['id']; ?>">
                                                <button type="submit" class="text-orange-600 hover:text-orange-900 transition-colors" title="Destacar/Quitar destacado">
                                                    <i class="fas fa-star<?php echo $podcast['destacado'] ? '' : '-o'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este podcast? Esta acción no se puede deshacer.')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $podcast['id']; ?>">
                                                <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Eliminar">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginación -->
                <?php if ($total_pages > 1): ?>
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        <div class="flex items-center justify-between">
                            <div class="flex-1 flex justify-between sm:hidden">
                                <?php if ($page > 1): ?>
                                    <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" 
                                       class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Anterior
                                    </a>
                                <?php endif; ?>
                                <?php if ($page < $total_pages): ?>
                                    <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" 
                                       class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                                        Siguiente
                                    </a>
                                <?php endif; ?>
                            </div>
                            <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                                <div>
                                    <p class="text-sm text-gray-700">
                                        Mostrando <span class="font-medium"><?php echo $offset + 1; ?></span> a 
                                        <span class="font-medium"><?php echo min($offset + $limit, $total_records); ?></span> de 
                                        <span class="font-medium"><?php echo $total_records; ?></span> resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&status=<?php echo $status_filter; ?>" 
                                               class="<?php echo $i === $page ? 'bg-red-50 border-red-500 text-red-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endfor; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </main>

    <script>
        // Confirmar eliminación
        function confirmDelete(id) {
            if (confirm('¿Estás seguro de eliminar este podcast? Esta acción no se puede deshacer.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
</body>
</html>
