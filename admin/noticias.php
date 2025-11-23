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
            $stmt = $con->prepare("UPDATE noticias SET activo = NOT activo WHERE id = ?");
            $stmt->execute([$id]);
            break;
            
        case 'toggle_featured':
            $id = (int)$_POST['id'];
            $stmt = $con->prepare("UPDATE noticias SET destacado = NOT destacado WHERE id = ?");
            $stmt->execute([$id]);
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            // Obtener archivos para eliminar
            $stmt = $con->prepare("SELECT imagen FROM noticias WHERE id = ?");
            $stmt->execute([$id]);
            $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
            
            // Eliminar archivos físicos
            if ($noticia['imagen'] && file_exists(__DIR__ . '/../uploads/noticias/images/' . $noticia['imagen'])) {
                unlink(__DIR__ . '/../uploads/noticias/images/' . $noticia['imagen']);
            }
            
            // Eliminar de la base de datos
            $stmt = $con->prepare("DELETE FROM noticias WHERE id = ?");
            $stmt->execute([$id]);
            break;
    }
    
    // Redireccionar para evitar reenvío de formulario
    header("Location: noticias.php");
    exit();
}

// Obtener noticias con paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Construir consulta con filtros
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(titulo_es LIKE ? OR titulo_gl LIKE ? OR contenido_es LIKE ? OR resumen_es LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
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
$count_sql = "SELECT COUNT(*) FROM noticias $where_clause";
$count_stmt = $con->prepare($count_sql);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Obtener noticias
$sql = "SELECT n.*, 
               CASE WHEN cn.nombre_es IS NOT NULL THEN cn.nombre_es ELSE 'Sin categoría' END as categoria_nombre,
               CASE WHEN cn.color IS NOT NULL THEN cn.color ELSE '#20B2AA' END as categoria_color
        FROM noticias n 
        LEFT JOIN categorias_noticia cn ON n.categoria_id = cn.id 
        $where_clause 
        ORDER BY n.fecha_publicacion DESC 
        LIMIT ? OFFSET ?";

$params[] = $limit;
$params[] = $offset;
$stmt = $con->prepare($sql);
$stmt->execute($params);
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Noticias - RadioRías Admin</title>
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
                        <h1 class="text-2xl font-black text-white">Gestión de Noticias</h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="noticia_form.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Nueva Noticia</span>
                    </a>
                    <a href="categorias_noticias.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-tags"></i>
                        <span>Categorías</span>
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
                           placeholder="Buscar por título o contenido..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                </div>
                <div>
                    <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <option value="all" <?php echo $status_filter === 'all' ? 'selected' : ''; ?>>Todos los estados</option>
                        <option value="active" <?php echo $status_filter === 'active' ? 'selected' : ''; ?>>Activas</option>
                        <option value="inactive" <?php echo $status_filter === 'inactive' ? 'selected' : ''; ?>>Inactivas</option>
                    </select>
                </div>
                <div class="flex gap-2">
                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="noticias.php" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-2 rounded-lg font-bold transition-colors">
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
                SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activas,
                SUM(CASE WHEN destacado = 1 THEN 1 ELSE 0 END) as destacadas,
                SUM(vistas) as total_vistas
                FROM noticias";
            $stats = $con->query($stats_sql)->fetch(PDO::FETCH_ASSOC);
            ?>
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-blue-600 text-sm font-medium">Total Noticias</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
                    </div>
                    <i class="fas fa-newspaper text-blue-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-green-600 text-sm font-medium">Activas</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['activas']; ?></p>
                    </div>
                    <i class="fas fa-check-circle text-green-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-yellow-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-yellow-600 text-sm font-medium">Destacadas</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['destacadas']; ?></p>
                    </div>
                    <i class="fas fa-star text-yellow-500 text-2xl"></i>
                </div>
            </div>
            
            <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="flex-1">
                        <p class="text-purple-600 text-sm font-medium">Total Vistas</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo number_format($stats['total_vistas']); ?></p>
                    </div>
                    <i class="fas fa-eye text-purple-500 text-2xl"></i>
                </div>
            </div>
        </div>

        <!-- Lista de noticias -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">
                    Noticias (<?php echo $total_records; ?> resultados)
                </h2>
            </div>
            
            <?php if (empty($noticias)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-newspaper text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No se encontraron noticias</p>
                    <a href="noticia_form.php" class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold transition-colors">
                        <i class="fas fa-plus mr-2"></i>Crear primera noticia
                    </a>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Noticia</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estadísticas</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($noticias as $noticia): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-12 w-12">
                                                <?php if ($noticia['imagen']): ?>
                                                    <img class="h-12 w-12 rounded-lg object-cover" 
                                                         src="../uploads/noticias/images/<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                                         alt="<?php echo htmlspecialchars($noticia['titulo_es']); ?>">
                                                <?php else: ?>
                                                    <div class="h-12 w-12 rounded-lg bg-gray-200 flex items-center justify-center">
                                                        <i class="fas fa-newspaper text-gray-400"></i>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($noticia['titulo_es']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars(substr($noticia['resumen_es'] ?? $noticia['contenido_es'], 0, 60)) . '...'; ?>
                                                </div>
                                                <?php if ($noticia['autor']): ?>
                                                    <div class="text-xs text-gray-400">
                                                        <i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($noticia['autor']); ?>
                                                    </div>
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full text-white"
                                              style="background-color: <?php echo $noticia['categoria_color']; ?>;">
                                            <?php echo htmlspecialchars($noticia['categoria_nombre']); ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900"><?php echo date('d/m/Y', strtotime($noticia['fecha_publicacion'])); ?></div>
                                        <div class="text-xs text-gray-500"><?php echo date('d/m/Y H:i', strtotime($noticia['fecha_creacion'])); ?></div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <i class="fas fa-eye text-blue-500 mr-1"></i><?php echo number_format($noticia['vistas']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex flex-col space-y-1">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $noticia['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $noticia['activo'] ? 'Activa' : 'Inactiva'; ?>
                                            </span>
                                            <?php if ($noticia['destacado']): ?>
                                                <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-yellow-100 text-yellow-800">
                                                    <i class="fas fa-star mr-1"></i>Destacada
                                                </span>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <a href="noticia_form.php?id=<?php echo $noticia['id']; ?>" 
                                               class="text-blue-600 hover:text-blue-900 transition-colors" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            
                                            <a href="../pages/noticia_detalle.php?id=<?php echo $noticia['id']; ?>" 
                                               target="_blank"
                                               class="text-green-600 hover:text-green-900 transition-colors" title="Ver">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            <form method="POST" class="inline" onsubmit="return confirm('¿Cambiar estado de la noticia?')">
                                                <input type="hidden" name="action" value="toggle_status">
                                                <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 transition-colors" title="Cambiar estado">
                                                    <i class="fas fa-toggle-<?php echo $noticia['activo'] ? 'on' : 'off'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" class="inline" onsubmit="return confirm('¿Cambiar destacado?')">
                                                <input type="hidden" name="action" value="toggle_featured">
                                                <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
                                                <button type="submit" class="text-orange-600 hover:text-orange-900 transition-colors" title="Destacar/Quitar destacado">
                                                    <i class="fas fa-star<?php echo $noticia['destacado'] ? '' : '-o'; ?>"></i>
                                                </button>
                                            </form>
                                            
                                            <form method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta noticia? Esta acción no se puede deshacer.')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $noticia['id']; ?>">
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
            if (confirm('¿Estás seguro de eliminar esta noticia? Esta acción no se puede deshacer.')) {
                document.getElementById('delete-form-' + id).submit();
            }
        }
    </script>
</body>
</html>
