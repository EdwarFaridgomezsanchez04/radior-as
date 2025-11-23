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
        case 'create':
            $nombre_es = trim($_POST['nombre_es']);
            $nombre_gl = trim($_POST['nombre_gl']);
            $descripcion_es = trim($_POST['descripcion_es']);
            $descripcion_gl = trim($_POST['descripcion_gl']);
            $color = $_POST['color'];
            $icono = $_POST['icono'];
            $orden = (int)$_POST['orden'];
            
            $stmt = $con->prepare("INSERT INTO categorias_noticia (nombre_es, nombre_gl, descripcion_es, descripcion_gl, color, icono, orden) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->execute([$nombre_es, $nombre_gl, $descripcion_es, $descripcion_gl, $color, $icono, $orden]);
            break;
            
        case 'update':
            $id = (int)$_POST['id'];
            $nombre_es = trim($_POST['nombre_es']);
            $nombre_gl = trim($_POST['nombre_gl']);
            $descripcion_es = trim($_POST['descripcion_es']);
            $descripcion_gl = trim($_POST['descripcion_gl']);
            $color = $_POST['color'];
            $icono = $_POST['icono'];
            $orden = (int)$_POST['orden'];
            $activo = isset($_POST['activo']) ? 1 : 0;
            
            $stmt = $con->prepare("UPDATE categorias_noticia SET nombre_es = ?, nombre_gl = ?, descripcion_es = ?, descripcion_gl = ?, color = ?, icono = ?, orden = ?, activo = ? WHERE id = ?");
            $stmt->execute([$nombre_es, $nombre_gl, $descripcion_es, $descripcion_gl, $color, $icono, $orden, $activo, $id]);
            break;
            
        case 'delete':
            $id = (int)$_POST['id'];
            $stmt = $con->prepare("DELETE FROM categorias_noticia WHERE id = ?");
            $stmt->execute([$id]);
            break;
    }
    
    header("Location: categorias_noticias.php");
    exit();
}

// Obtener categorías
$sql = "SELECT * FROM categorias_noticia ORDER BY orden, nombre_es";
$categorias = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Iconos disponibles
$iconos_disponibles = [
    'fas fa-newspaper' => 'Periódico',
    'fas fa-futbol' => 'Fútbol',
    'fas fa-palette' => 'Paleta',
    'fas fa-map-marker-alt' => 'Ubicación',
    'fas fa-microchip' => 'Tecnología',
    'fas fa-music' => 'Música',
    'fas fa-camera' => 'Cámara',
    'fas fa-car' => 'Automóvil',
    'fas fa-heart' => 'Corazón',
    'fas fa-star' => 'Estrella',
    'fas fa-fire' => 'Fuego',
    'fas fa-trophy' => 'Trofeo'
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías de Noticias - RadioRías Admin</title>
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
                    <a href="noticias.php" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <img src="../assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-10 h-10 rounded-full">
                    <div>
                        <h1 class="text-2xl font-black text-white">Categorías de Noticias</h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <button onclick="showCreateModal()" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-plus"></i>
                        <span>Nueva Categoría</span>
                    </button>
                    <a href="noticias.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-newspaper"></i>
                        <span>Ver Noticias</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Lista de categorías -->
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">
                    Categorías (<?php echo count($categorias); ?> total)
                </h2>
            </div>
            
            <?php if (empty($categorias)): ?>
                <div class="p-8 text-center">
                    <i class="fas fa-tags text-gray-400 text-4xl mb-4"></i>
                    <p class="text-gray-500 text-lg">No hay categorías creadas</p>
                    <button onclick="showCreateModal()" class="inline-block mt-4 bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-bold transition-colors">
                        <i class="fas fa-plus mr-2"></i>Crear primera categoría
                    </button>
                </div>
            <?php else: ?>
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Categoría</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Descripción</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Color/Icono</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Orden</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            <?php foreach ($categorias as $categoria): ?>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="flex-shrink-0 h-10 w-10">
                                                <div class="h-10 w-10 rounded-lg flex items-center justify-center text-white"
                                                     style="background-color: <?php echo $categoria['color']; ?>">
                                                    <i class="<?php echo $categoria['icono']; ?>"></i>
                                                </div>
                                            </div>
                                            <div class="ml-4">
                                                <div class="text-sm font-medium text-gray-900">
                                                    <?php echo htmlspecialchars($categoria['nombre_es']); ?>
                                                </div>
                                                <div class="text-sm text-gray-500">
                                                    <?php echo htmlspecialchars($categoria['nombre_gl']); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-900">
                                            <?php echo htmlspecialchars(substr($categoria['descripcion_es'], 0, 50)) . '...'; ?>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-6 h-6 rounded-full border-2 border-gray-300"
                                                 style="background-color: <?php echo $categoria['color']; ?>"></div>
                                            <span class="text-sm text-gray-500">
                                                <?php echo htmlspecialchars($categoria['icono']); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                        <?php echo $categoria['orden']; ?>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full <?php echo $categoria['activo'] ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'; ?>">
                                            <?php echo $categoria['activo'] ? 'Activa' : 'Inactiva'; ?>
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <div class="flex space-x-2">
                                            <button onclick="showEditModal(<?php echo htmlspecialchars(json_encode($categoria)); ?>)" 
                                                    class="text-blue-600 hover:text-blue-900 transition-colors" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            
                                            <form method="POST" class="inline" onsubmit="return confirm('¿Estás seguro de eliminar esta categoría?')">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?php echo $categoria['id']; ?>">
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
            <?php endif; ?>
        </div>
    </main>

    <!-- Modal Crear/Editar -->
    <div id="categoryModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 id="modalTitle" class="text-lg font-bold text-gray-900">Nueva Categoría</h3>
            </div>
            
            <form id="categoryForm" method="POST" class="p-6">
                <input type="hidden" name="action" value="create">
                <input type="hidden" name="id" id="categoryId">
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Nombre Español -->
                    <div>
                        <label for="nombre_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre (Español) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_es" name="nombre_es" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Nombre Gallego -->
                    <div>
                        <label for="nombre_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre (Gallego) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_gl" name="nombre_gl" required
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Descripción Español -->
                    <div>
                        <label for="descripcion_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción (Español)
                        </label>
                        <textarea id="descripcion_es" name="descripcion_es" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <!-- Descripción Gallego -->
                    <div>
                        <label for="descripcion_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción (Gallego)
                        </label>
                        <textarea id="descripcion_gl" name="descripcion_gl" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"></textarea>
                    </div>
                    
                    <!-- Color -->
                    <div>
                        <label for="color" class="block text-sm font-medium text-gray-700 mb-2">
                            Color <span class="text-red-500">*</span>
                        </label>
                        <input type="color" id="color" name="color" value="#20B2AA" required
                               class="w-full h-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Icono -->
                    <div>
                        <label for="icono" class="block text-sm font-medium text-gray-700 mb-2">
                            Icono <span class="text-red-500">*</span>
                        </label>
                        <select id="icono" name="icono" required
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <?php foreach ($iconos_disponibles as $icono => $nombre): ?>
                                <option value="<?php echo $icono; ?>"><?php echo $nombre; ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Orden -->
                    <div>
                        <label for="orden" class="block text-sm font-medium text-gray-700 mb-2">
                            Orden
                        </label>
                        <input type="number" id="orden" name="orden" value="0" min="0"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Estado (solo para edición) -->
                    <div id="estadoField" class="hidden">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Estado</label>
                        <label class="flex items-center">
                            <input type="checkbox" name="activo" value="1" checked
                                   class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                            <span class="ml-2 text-sm text-gray-700">Activa</span>
                        </label>
                    </div>
                </div>
                
                <!-- Botones -->
                <div class="flex justify-end space-x-4 mt-6">
                    <button type="button" onclick="hideModal()" 
                            class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                        Cancelar
                    </button>
                    <button type="submit" 
                            class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <span id="submitText">Crear Categoría</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        function showCreateModal() {
            document.getElementById('modalTitle').textContent = 'Nueva Categoría';
            document.getElementById('submitText').textContent = 'Crear Categoría';
            document.querySelector('input[name="action"]').value = 'create';
            document.getElementById('categoryId').value = '';
            document.getElementById('estadoField').classList.add('hidden');
            
            // Limpiar formulario
            document.getElementById('categoryForm').reset();
            document.getElementById('color').value = '#20B2AA';
            
            document.getElementById('categoryModal').classList.remove('hidden');
        }
        
        function showEditModal(categoria) {
            document.getElementById('modalTitle').textContent = 'Editar Categoría';
            document.getElementById('submitText').textContent = 'Actualizar Categoría';
            document.querySelector('input[name="action"]').value = 'update';
            document.getElementById('categoryId').value = categoria.id;
            document.getElementById('estadoField').classList.remove('hidden');
            
            // Llenar formulario
            document.getElementById('nombre_es').value = categoria.nombre_es;
            document.getElementById('nombre_gl').value = categoria.nombre_gl;
            document.getElementById('descripcion_es').value = categoria.descripcion_es || '';
            document.getElementById('descripcion_gl').value = categoria.descripcion_gl || '';
            document.getElementById('color').value = categoria.color;
            document.getElementById('icono').value = categoria.icono;
            document.getElementById('orden').value = categoria.orden;
            document.querySelector('input[name="activo"]').checked = categoria.activo == 1;
            
            document.getElementById('categoryModal').classList.remove('hidden');
        }
        
        function hideModal() {
            document.getElementById('categoryModal').classList.add('hidden');
        }
        
        // Cerrar modal al hacer clic fuera
        document.getElementById('categoryModal').addEventListener('click', function(e) {
            if (e.target === this) {
                hideModal();
            }
        });
    </script>
</body>
</html>
