<?php
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');

// Solo administradores pueden gestionar usuarios
if ($_SESSION['rol'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$conex = new database();
$con = $conex->conectar();

// Procesar acciones
if (isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'toggle_status':
            $id = (int)$_POST['id'];
            // No permitir desactivar al propio usuario
            if ($id != $_SESSION['user_id']) {
                $stmt = $con->prepare("UPDATE usuarios SET activo = NOT activo WHERE id = ?");
                $stmt->execute([$id]);
            }
            break;
            
        case 'change_role':
            $id = (int)$_POST['id'];
            $new_role = $_POST['role'];
            // No permitir cambiar el rol del propio usuario
            if ($id != $_SESSION['user_id'] && in_array($new_role, ['admin', 'editor'])) {
                $stmt = $con->prepare("UPDATE usuarios SET rol = ? WHERE id = ?");
                $stmt->execute([$new_role, $id]);
            }
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            // No permitir eliminar al propio usuario
            if ($id != $_SESSION['user_id']) {
                $stmt = $con->prepare("DELETE FROM usuarios WHERE id = ?");
                $stmt->execute([$id]);
            }
            break;
    }
    
    // Redireccionar para evitar reenvío del formulario
    header('Location: usuarios.php');
    exit;
}

// Obtener usuarios con paginación
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : 'all';

// Construir consulta con filtros
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(username LIKE ? OR nombre LIKE ? OR email LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($role_filter !== 'all') {
    $where_conditions[] = "rol = ?";
    $params[] = $role_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Contar total de registros
$count_sql = "SELECT COUNT(*) FROM usuarios $where_clause";
$count_stmt = $con->prepare($count_sql);
$count_stmt->execute($params);
$total_records = $count_stmt->fetchColumn();
$total_pages = ceil($total_records / $limit);

// Obtener usuarios
$sql = "SELECT * FROM usuarios $where_clause ORDER BY fecha_creacion DESC LIMIT ? OFFSET ?";
$params[] = $limit;
$params[] = $offset;
$stmt = $con->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener estadísticas
$stats_sql = "SELECT 
    COUNT(*) as total,
    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
    SUM(CASE WHEN rol = 'admin' THEN 1 ELSE 0 END) as admins,
    SUM(CASE WHEN rol = 'editor' THEN 1 ELSE 0 END) as editores
    FROM usuarios";
$stats = $con->query($stats_sql)->fetch(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - RadioRías Admin</title>
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
                        <h1 class="text-2xl font-black text-white">Gestión de Usuarios</h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="usuario_form.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Nuevo Usuario</span>
                    </a>
                    <a href="../includes/salir.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Estadísticas -->
        <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-blue-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-blue-100 text-blue-600 mr-4">
                        <i class="fas fa-users text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Total Usuarios</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-green-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-green-100 text-green-600 mr-4">
                        <i class="fas fa-user-check text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Usuarios Activos</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['activos']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-purple-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-purple-100 text-purple-600 mr-4">
                        <i class="fas fa-crown text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Administradores</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['admins']; ?></p>
                    </div>
                </div>
            </div>
            
            <div class="bg-white rounded-xl shadow-md p-6 border-l-4 border-orange-500">
                <div class="flex items-center">
                    <div class="p-3 rounded-full bg-orange-100 text-orange-600 mr-4">
                        <i class="fas fa-user-edit text-xl"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-gray-600">Editores</p>
                        <p class="text-2xl font-bold text-gray-900"><?php echo $stats['editores']; ?></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Filtros y búsqueda -->
        <div class="bg-white rounded-xl shadow-md p-6 mb-8">
            <form method="GET" class="flex flex-col md:flex-row gap-4">
                <div class="flex-1">
                    <label for="search" class="block text-sm font-medium text-gray-700 mb-2">Buscar usuarios</label>
                    <input type="text" id="search" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                           placeholder="Buscar por nombre, usuario o email..." 
                           class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                </div>
                
                <div class="md:w-48">
                    <label for="role" class="block text-sm font-medium text-gray-700 mb-2">Filtrar por rol</label>
                    <select id="role" name="role" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent">
                        <option value="all" <?php echo $role_filter === 'all' ? 'selected' : ''; ?>>Todos los roles</option>
                        <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Administradores</option>
                        <option value="editor" <?php echo $role_filter === 'editor' ? 'selected' : ''; ?>>Editores</option>
                    </select>
                </div>
                
                <div class="flex items-end">
                    <button type="submit" class="bg-gray-700 hover:bg-gray-800 text-white px-6 py-2 rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-search mr-2"></i>
                        Buscar
                    </button>
                </div>
            </form>
        </div>

        <!-- Tabla de usuarios -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Lista de Usuarios</h3>
                <p class="text-sm text-gray-600">Total: <?php echo $total_records; ?> usuarios</p>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Información</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        <?php foreach ($usuarios as $usuario): ?>
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0 h-10 w-10">
                                        <div class="h-10 w-10 rounded-full bg-gray-300 flex items-center justify-center">
                                            <i class="fas fa-user text-gray-600"></i>
                                        </div>
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-gray-900"><?php echo htmlspecialchars($usuario['username']); ?></div>
                                        <div class="text-sm text-gray-500">ID: <?php echo $usuario['id']; ?></div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-gray-900"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
                                <div class="text-sm text-gray-500"><?php echo htmlspecialchars($usuario['email']); ?></div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $usuario['rol'] === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800'; ?>">
                                    <i class="fas fa-<?php echo $usuario['rol'] === 'admin' ? 'crown' : 'user-edit'; ?> mr-1"></i>
                                    <?php echo ucfirst($usuario['rol']); ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $usuario['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                    <i class="fas fa-<?php echo $usuario['activo'] ? 'check' : 'times'; ?> mr-1"></i>
                                    <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                <?php echo date('d/m/Y', strtotime($usuario['fecha_creacion'])); ?>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex items-center justify-end space-x-2">
                                    <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                        <!-- Cambiar estado -->
                                        <form method="POST" class="inline">
                                            <input type="hidden" name="action" value="toggle_status">
                                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                            <button type="submit" class="text-<?php echo $usuario['activo'] ? 'red' : 'green'; ?>-600 hover:text-<?php echo $usuario['activo'] ? 'red' : 'green'; ?>-900 transition-colors" 
                                                    title="<?php echo $usuario['activo'] ? 'Desactivar' : 'Activar'; ?>">
                                                <i class="fas fa-<?php echo $usuario['activo'] ? 'ban' : 'check'; ?>"></i>
                                            </button>
                                        </form>
                                        
                                        <!-- Editar -->
                                        <a href="usuario_form.php?id=<?php echo $usuario['id']; ?>" 
                                           class="text-blue-600 hover:text-blue-900 transition-colors" title="Editar">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        
                                        <!-- Eliminar -->
                                        <form method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de que quieres eliminar este usuario?')">
                                            <input type="hidden" name="action" value="delete">
                                            <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                            <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Eliminar">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    <?php else: ?>
                                        <span class="text-gray-400 text-xs">(Tu usuario)</span>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
            <div class="bg-white px-4 py-3 flex items-center justify-between border-t border-gray-200 sm:px-6">
                <div class="flex-1 flex justify-between sm:hidden">
                    <?php if ($page > 1): ?>
                        <a href="?page=<?php echo $page - 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>" 
                           class="relative inline-flex items-center px-4 py-2 border border-gray-300 text-sm font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50">
                            Anterior
                        </a>
                    <?php endif; ?>
                    <?php if ($page < $total_pages): ?>
                        <a href="?page=<?php echo $page + 1; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>" 
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
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>" 
                                   class="relative inline-flex items-center px-4 py-2 border text-sm font-medium <?php echo $i === $page ? 'z-10 bg-gray-700 border-gray-700 text-white' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?>">
                                    <?php echo $i; ?>
                                </a>
                            <?php endfor; ?>
                        </nav>
                    </div>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
