<?php
session_start();
require_once(__DIR__ . '/../includes/validarsesion.php');
require_once(__DIR__ . '/../config/conexion.php');

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

// Parámetros de paginación y búsqueda
$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$role_filter = isset($_GET['role']) ? $_GET['role'] : '';

// Construir consulta con filtros
$where_conditions = [];
$params = [];

if (!empty($search)) {
    $where_conditions[] = "(username LIKE ? OR nombre LIKE ? OR email LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($role_filter)) {
    $where_conditions[] = "rol = ?";
    $params[] = $role_filter;
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Obtener usuarios con paginación
$sql = "SELECT * FROM usuarios $where_clause ORDER BY fecha_creacion DESC LIMIT $limit OFFSET $offset";
$stmt = $con->prepare($sql);
$stmt->execute($params);
$usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Contar total de usuarios para paginación
$count_sql = "SELECT COUNT(*) FROM usuarios $where_clause";
$count_stmt = $con->prepare($count_sql);
$count_stmt->execute($params);
$total_usuarios = $count_stmt->fetchColumn();
$total_pages = ceil($total_usuarios / $limit);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios - RadioRías</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'radio-red': '#DC2626',
                        'radio-dark': '#1F2937',
                        'radio-teal': '#0D9488',
                        'radio-cyan': '#0891B2'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <main class="min-h-screen py-8">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">Gestión de Usuarios</h1>
                        <p class="mt-2 text-gray-600">Administra los usuarios del sistema</p>
                    </div>
                    <div class="mt-4 sm:mt-0 flex space-x-3">
                        <a href="dashboard.php" 
                           class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors">
                            <i class="fas fa-arrow-left mr-2"></i>Volver al Dashboard
                        </a>
                        <a href="usuario_form.php" 
                           class="inline-flex items-center px-4 py-2 bg-radio-red text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                            <i class="fas fa-plus mr-2"></i>Nuevo Usuario
                        </a>
                    </div>
                </div>
            </div>

            <!-- Estadísticas -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <?php
                $stats_sql = "SELECT 
                    COUNT(*) as total,
                    SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos,
                    SUM(CASE WHEN rol = 'admin' THEN 1 ELSE 0 END) as admins,
                    SUM(CASE WHEN rol = 'editor' THEN 1 ELSE 0 END) as editores
                    FROM usuarios";
                $stats = $con->query($stats_sql)->fetch(PDO::FETCH_ASSOC);
                ?>
                <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-blue-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-blue-600 text-sm font-medium">Total Usuarios</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['total']; ?></p>
                        </div>
                        <i class="fas fa-users text-blue-500 text-2xl"></i>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-green-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-green-600 text-sm font-medium">Activos</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['activos']; ?></p>
                        </div>
                        <i class="fas fa-user-check text-green-500 text-2xl"></i>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-red-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-red-600 text-sm font-medium">Administradores</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['admins']; ?></p>
                        </div>
                        <i class="fas fa-user-shield text-red-500 text-2xl"></i>
                    </div>
                </div>
                
                <div class="bg-white rounded-lg p-6 shadow-md border-l-4 border-purple-500">
                    <div class="flex items-center">
                        <div class="flex-1">
                            <p class="text-purple-600 text-sm font-medium">Editores</p>
                            <p class="text-2xl font-bold text-gray-900"><?php echo $stats['editores']; ?></p>
                        </div>
                        <i class="fas fa-user-edit text-purple-500 text-2xl"></i>
                    </div>
                </div>
            </div>

            <!-- Filtros y búsqueda -->
            <div class="bg-white rounded-lg shadow-md p-6 mb-6">
                <form method="GET" class="flex flex-col sm:flex-row gap-4">
                    <div class="flex-1">
                        <input type="text" name="search" placeholder="Buscar por usuario, nombre o email..." 
                               value="<?php echo htmlspecialchars($search); ?>"
                               class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    <div>
                        <select name="role" class="px-4 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Todos los roles</option>
                            <option value="admin" <?php echo $role_filter === 'admin' ? 'selected' : ''; ?>>Administradores</option>
                            <option value="editor" <?php echo $role_filter === 'editor' ? 'selected' : ''; ?>>Editores</option>
                        </select>
                    </div>
                    <button type="submit" class="px-6 py-2 bg-radio-red text-white rounded-lg hover:bg-red-700 transition-colors">
                        <i class="fas fa-search mr-2"></i>Buscar
                    </button>
                    <?php if (!empty($search) || !empty($role_filter)): ?>
                        <a href="usuarios.php" class="px-6 py-2 bg-gray-500 text-white rounded-lg hover:bg-gray-600 transition-colors">
                            <i class="fas fa-times mr-2"></i>Limpiar
                        </a>
                    <?php endif; ?>
                </form>
            </div>

            <!-- Lista de usuarios -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Usuarios (<?php echo $total_usuarios; ?> total<?php echo $total_usuarios != 1 ? 'es' : ''; ?>)
                    </h3>
                </div>
                
                <?php if (empty($usuarios)): ?>
                    <div class="text-center py-12">
                        <i class="fas fa-users text-4xl text-gray-400 mb-4"></i>
                        <h3 class="text-lg font-medium text-gray-900 mb-2">No se encontraron usuarios</h3>
                        <p class="text-gray-500">
                            <?php if (!empty($search) || !empty($role_filter)): ?>
                                Intenta ajustar los filtros de búsqueda.
                            <?php else: ?>
                                Comienza creando el primer usuario.
                            <?php endif; ?>
                        </p>
                    </div>
                <?php else: ?>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Información</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rol</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha Creación</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
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
                                                    <div class="text-sm font-medium text-gray-900">
                                                        <?php echo htmlspecialchars($usuario['username']); ?>
                                                        <?php if ($usuario['id'] == $_SESSION['user_id']): ?>
                                                            <span class="ml-2 inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                                Tú
                                                            </span>
                                                        <?php endif; ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm text-gray-900"><?php echo htmlspecialchars($usuario['nombre']); ?></div>
                                            <div class="text-sm text-gray-500"><?php echo htmlspecialchars($usuario['email']); ?></div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $usuario['rol'] === 'admin' ? 'bg-red-100 text-red-800' : 'bg-purple-100 text-purple-800'; ?>">
                                                <?php echo $usuario['rol'] === 'admin' ? 'Administrador' : 'Editor'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $usuario['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                                <?php echo $usuario['activo'] ? 'Activo' : 'Inactivo'; ?>
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <?php echo date('d/m/Y H:i', strtotime($usuario['fecha_creacion'])); ?>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <div class="flex space-x-2">
                                                <a href="usuario_form.php?id=<?php echo $usuario['id']; ?>" 
                                                   class="text-blue-600 hover:text-blue-900 transition-colors" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <?php if ($usuario['id'] != $_SESSION['user_id']): ?>
                                                    <form method="POST" class="inline" onsubmit="return confirm('¿Cambiar estado del usuario?')">
                                                        <input type="hidden" name="action" value="toggle_status">
                                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                        <button type="submit" class="text-green-600 hover:text-green-900 transition-colors" title="Cambiar estado">
                                                            <i class="fas fa-toggle-<?php echo $usuario['activo'] ? 'on' : 'off'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" class="inline" onsubmit="return confirm('¿Cambiar rol del usuario?')">
                                                        <input type="hidden" name="action" value="change_role">
                                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                        <input type="hidden" name="role" value="<?php echo $usuario['rol'] === 'admin' ? 'editor' : 'admin'; ?>">
                                                        <button type="submit" class="text-purple-600 hover:text-purple-900 transition-colors" title="Cambiar rol">
                                                            <i class="fas fa-user-<?php echo $usuario['rol'] === 'admin' ? 'edit' : 'shield'; ?>"></i>
                                                        </button>
                                                    </form>
                                                    
                                                    <form method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar este usuario? Esta acción no se puede deshacer.')">
                                                        <input type="hidden" name="action" value="delete">
                                                        <input type="hidden" name="id" value="<?php echo $usuario['id']; ?>">
                                                        <button type="submit" class="text-red-600 hover:text-red-900 transition-colors" title="Eliminar">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
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
                                        <span class="font-medium"><?php echo min($offset + $limit, $total_usuarios); ?></span> de 
                                        <span class="font-medium"><?php echo $total_usuarios; ?></span> resultados
                                    </p>
                                </div>
                                <div>
                                    <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px">
                                        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                                            <a href="?page=<?php echo $i; ?>&search=<?php echo urlencode($search); ?>&role=<?php echo urlencode($role_filter); ?>" 
                                               class="<?php echo $i === $page ? 'bg-red-50 border-red-500 text-red-600' : 'bg-white border-gray-300 text-gray-500 hover:bg-gray-50'; ?> relative inline-flex items-center px-4 py-2 border text-sm font-medium">
                                                <?php echo $i; ?>
                                            </a>
                                        <?php endfor; ?>
                                    </nav>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    </main>
</body>
</html>
