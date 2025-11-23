<?php
require_once '../includes/validarsesion.php';
require_once '../config/config.php';

// Conectar a la base de datos
try {
    $con = new PDO("mysql:host=" . DB_HOST . ";dbname=" . DB_NAME, DB_USER, DB_PASS);
    $con->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Error de conexión: " . $e->getMessage());
}

// Procesar acciones
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'mark_read':
            $id = (int)$_POST['id'];
            $stmt = $con->prepare("UPDATE contacts SET status = 'read' WHERE id = ?");
            $stmt->execute([$id]);
            break;
            
        case 'mark_replied':
            $id = (int)$_POST['id'];
            $stmt = $con->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?");
            $stmt->execute([$id]);
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $con->prepare("DELETE FROM contacts WHERE id = ?");
            $stmt->execute([$id]);
            break;
    }
    
    header("Location: mensajes.php");
    exit;
}

// Filtros
$status_filter = $_GET['status'] ?? '';
$search = $_GET['search'] ?? '';

// Paginación
$page = (int)($_GET['page'] ?? 1);
$per_page = 10;
$offset = ($page - 1) * $per_page;

// Construir consulta
$where_conditions = [];
$params = [];

if ($status_filter) {
    $where_conditions[] = "status = ?";
    $params[] = $status_filter;
}

if ($search) {
    $where_conditions[] = "(name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?)";
    $search_param = "%$search%";
    $params = array_merge($params, [$search_param, $search_param, $search_param, $search_param]);
}

$where_clause = $where_conditions ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Obtener mensajes
$sql = "SELECT * FROM contacts $where_clause ORDER BY created_at DESC LIMIT $per_page OFFSET $offset";
$stmt = $con->prepare($sql);
$stmt->execute($params);
$mensajes = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total para paginación
$count_sql = "SELECT COUNT(*) FROM contacts $where_clause";
$count_stmt = $con->prepare($count_sql);
$count_stmt->execute($params);
$total_mensajes = $count_stmt->fetchColumn();
$total_pages = ceil($total_mensajes / $per_page);

// Estadísticas
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN status = 'new' THEN 1 ELSE 0 END) as nuevos,
    SUM(CASE WHEN status = 'read' THEN 1 ELSE 0 END) as leidos,
    SUM(CASE WHEN status = 'replied' THEN 1 ELSE 0 END) as respondidos,
    SUM(CASE WHEN DATE(created_at) = CURDATE() THEN 1 ELSE 0 END) as hoy
FROM contacts";
$stats = $con->query($stats_sql)->fetch(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mensajes de Contacto - Admin</title>
    <?php include('../includes/favicon.php'); ?>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <header class="bg-gradient-to-r from-gray-700 to-gray-800 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-6">
                <div class="flex items-center">
                    <i class="fas fa-envelope text-3xl text-white mr-4"></i>
                    <div>
                        <h1 class="text-3xl font-bold text-white">Mensajes de Contacto</h1>
                        <p class="text-gray-300">Gestiona los mensajes enviados desde el formulario</p>
                    </div>
                </div>
                <a href="dashboard.php" class="bg-white text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors">
                    <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
                </a>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-5 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600">
                        <i class="fas fa-envelope text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Total</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['total']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600">
                        <i class="fas fa-star text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Nuevos</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['nuevos']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-yellow-100 text-yellow-600">
                        <i class="fas fa-eye text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Leídos</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['leidos']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600">
                        <i class="fas fa-reply text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Respondidos</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['respondidos']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-indigo-100 text-indigo-600">
                        <i class="fas fa-calendar-day text-xl"></i>
                    </div>
                    <div class="ml-4">
                        <p class="text-sm font-medium text-gray-600">Hoy</p>
                        <p class="text-2xl font-semibold text-gray-900"><?php echo $stats['hoy']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros -->
        <div class="bg-white rounded-lg shadow mb-6">
            <div class="p-6">
                <form method="GET" class="flex flex-wrap gap-4">
                    <div class="flex-1 min-w-64">
                        <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                               placeholder="Buscar por nombre, email, asunto o mensaje..."
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                    </div>
                    <div>
                        <select name="status" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-gray-500">
                            <option value="">Todos los estados</option>
                            <option value="new" <?php echo $status_filter === 'new' ? 'selected' : ''; ?>>Nuevos</option>
                            <option value="read" <?php echo $status_filter === 'read' ? 'selected' : ''; ?>>Leídos</option>
                            <option value="replied" <?php echo $status_filter === 'replied' ? 'selected' : ''; ?>>Respondidos</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-gray-700 text-white rounded-lg hover:bg-gray-800 transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <a href="mensajes.php" class="px-6 py-2 bg-gray-300 text-gray-700 rounded-lg hover:bg-gray-400 transition-colors">
                        <i class="fas fa-times mr-2"></i>Limpiar
                    </a>
                </form>
            </div>
        </div>

        <!-- Lista de mensajes -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Contacto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Asunto</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php if (empty($mensajes)): ?>
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-gray-500">
                                    <i class="fas fa-inbox text-4xl mb-4"></i>
                                    <p class="text-lg">No hay mensajes que mostrar</p>
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($mensajes as $mensaje): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <?php
                                        $status_classes = [
                                            'new' => 'bg-green-100 text-green-800',
                                            'read' => 'bg-yellow-100 text-yellow-800',
                                            'replied' => 'bg-purple-100 text-purple-800'
                                        ];
                                        $status_text = [
                                            'new' => 'Nuevo',
                                            'read' => 'Leído',
                                            'replied' => 'Respondido'
                                        ];
                                        ?>
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full <?php echo $status_classes[$mensaje['status']]; ?>">
                                            <?php echo $status_text[$mensaje['status']]; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($mensaje['name']); ?></div>
                                        <div class="text-sm text-gray-500"><?php echo htmlspecialchars($mensaje['email']); ?></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900 max-w-xs truncate" title="<?php echo htmlspecialchars($mensaje['subject']); ?>">
                                            <?php echo htmlspecialchars($mensaje['subject']); ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        <?php echo date('d/m/Y H:i', strtotime($mensaje['created_at'])); ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <button onclick="viewMessage(<?php echo $mensaje['id']; ?>)" 
                                                class="text-blue-600 hover:text-blue-900 mr-3">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                        
                                        <?php if ($mensaje['status'] === 'new'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="mark_read">
                                                <input type="hidden" name="id" value="<?php echo $mensaje['id']; ?>">
                                                <button type="submit" class="text-yellow-600 hover:text-yellow-900 mr-3" title="Marcar como leído">
                                                    <i class="fas fa-eye"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <?php if ($mensaje['status'] !== 'replied'): ?>
                                            <form method="POST" class="inline">
                                                <input type="hidden" name="action" value="mark_replied">
                                                <input type="hidden" name="id" value="<?php echo $mensaje['id']; ?>">
                                                <button type="submit" class="text-purple-600 hover:text-purple-900 mr-3" title="Marcar como respondido">
                                                    <i class="fas fa-reply"></i>
                                                </button>
                                            </form>
                                        <?php endif; ?>
                                        
                                        <form method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este mensaje?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $mensaje['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Paginación -->
        <?php if ($total_pages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6 mt-6 rounded-lg shadow">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Anterior
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>" 
                           class="ml-3 relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Siguiente
                        </a>
                    <?php endif; ?>
                </div>
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-gray-700">
                            Mostrando <span class="font-medium"><?php echo $offset + 1; ?></span> a 
                            <span class="font-medium"><?php echo min($offset + $per_page, $total_mensajes); ?></span> de 
                            <span class="font-medium"><?php echo $total_mensajes; ?></span> mensajes
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&status=<?php echo $status_filter; ?>&search=<?php echo urlencode($search); ?>" 
                                   class="<?php echo $i === $page ? 'bg-gray-700 text-white' : 'bg-white text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <!-- Modal para ver mensaje completo -->
    <div id="messageModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden">
        <div class="relative top-20 mx-auto p-5 border w-11/12 md:w-3/4 lg:w-1/2 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">Detalle del Mensaje</h3>
                    <button onclick="closeModal()" class="text-gray-400 hover:text-gray-600">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
                <div id="messageContent">
                    <!-- El contenido se cargará aquí -->
                </div>
            </div>
        </div>
    </div>

    <script>
        function viewMessage(id) {
            fetch(`mensaje_detalle.php?id=${id}`)
                .then(response => response.text())
                .then(html => {
                    document.getElementById('messageContent').innerHTML = html;
                    document.getElementById('messageModal').classList.remove('hidden');
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error al cargar el mensaje');
                });
        }

        function closeModal() {
            document.getElementById('messageModal').classList.add('hidden');
        }

        // Cerrar modal al hacer clic fuera
        document.getElementById('messageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeModal();
            }
        });
    </script>
</body>
</html>
