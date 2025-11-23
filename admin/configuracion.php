<?php
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');

// Solo administradores pueden acceder a configuración
if ($_SESSION['rol'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$conex = new database();
$con = $conex->conectar();

$success = '';
$errors = [];

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    switch ($action) {
        case 'site_settings':
            $site_name = trim($_POST['site_name']);
            $site_description = trim($_POST['site_description']);
            $site_email = trim($_POST['site_email']);
            $site_phone = trim($_POST['site_phone']);
            
            // Validaciones
            if (empty($site_name)) $errors[] = "El nombre del sitio es obligatorio";
            if (empty($site_email) || !filter_var($site_email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email válido es obligatorio";
            
            if (empty($errors)) {
                // Aquí normalmente guardarías en una tabla de configuración
                // Por ahora simularemos que se guardó
                $success = "Configuración del sitio actualizada correctamente";
            }
            break;
            
        case 'radio_settings':
            $radio_name = trim($_POST['radio_name']);
            $stream_url = trim($_POST['stream_url']);
            $radio_description = trim($_POST['radio_description']);
            $radio_genre = trim($_POST['radio_genre']);
            
            if (empty($radio_name)) $errors[] = "El nombre de la radio es obligatorio";
            if (empty($stream_url)) $errors[] = "La URL del stream es obligatoria";
            
            if (empty($errors)) {
                $success = "Configuración de radio actualizada correctamente";
            }
            break;
            
        case 'user_settings':
            $allow_registration = isset($_POST['allow_registration']) ? 1 : 0;
            $default_role = $_POST['default_role'];
            $require_email_verification = isset($_POST['require_email_verification']) ? 1 : 0;
            
            if (empty($errors)) {
                $success = "Configuración de usuarios actualizada correctamente";
            }
            break;
            
        case 'system_settings':
            $maintenance_mode = isset($_POST['maintenance_mode']) ? 1 : 0;
            $debug_mode = isset($_POST['debug_mode']) ? 1 : 0;
            $cache_enabled = isset($_POST['cache_enabled']) ? 1 : 0;
            
            if (empty($errors)) {
                $success = "Configuración del sistema actualizada correctamente";
            }
            break;
    }
}

// Obtener configuraciones actuales (simuladas)
$site_config = [
    'site_name' => 'RadioRías',
    'site_description' => 'La mejor radio online de Galicia',
    'site_email' => 'info@radiorias.com',
    'site_phone' => '+34 981 123 456'
];

$radio_config = [
    'radio_name' => 'RadioRías',
    'stream_url' => 'https://ec7.yesstreaming.net:2325/stream',
    'radio_description' => 'Radio online con la mejor música y programas de Galicia',
    'radio_genre' => 'Variado'
];

$user_config = [
    'allow_registration' => true,
    'default_role' => 'editor',
    'require_email_verification' => false
];

$system_config = [
    'maintenance_mode' => false,
    'debug_mode' => false,
    'cache_enabled' => true
];
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración - RadioRías Admin</title>
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
                        <h1 class="text-2xl font-black text-white">Configuración del Sistema</h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="../includes/salir.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Mensajes -->
        <?php if (!empty($errors)): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Se encontraron errores:</h3>
                    <div class="mt-2 text-sm text-red-700">
                        <ul class="list-disc list-inside space-y-1">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <?php if ($success): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($success); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Navegación por pestañas -->
        <div class="mb-8">
            <div class="border-b border-gray-200">
                <nav class="-mb-px flex space-x-8">
                    <button onclick="showTab('site')" id="tab-site" class="tab-button active border-b-2 border-indigo-500 py-2 px-1 text-sm font-medium text-indigo-600">
                        <i class="fas fa-globe mr-2"></i>
                        Sitio Web
                    </button>
                    <button onclick="showTab('radio')" id="tab-radio" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-broadcast-tower mr-2"></i>
                        Radio
                    </button>
                    <button onclick="showTab('users')" id="tab-users" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-users mr-2"></i>
                        Usuarios
                    </button>
                    <button onclick="showTab('system')" id="tab-system" class="tab-button border-b-2 border-transparent py-2 px-1 text-sm font-medium text-gray-500 hover:text-gray-700 hover:border-gray-300">
                        <i class="fas fa-server mr-2"></i>
                        Sistema
                    </button>
                </nav>
            </div>
        </div>

        <!-- Configuración del Sitio Web -->
        <div id="content-site" class="tab-content">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-globe mr-2 text-indigo-600"></i>
                        Configuración del Sitio Web
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Información general y datos de contacto del sitio</p>
                </div>
                
                <form method="POST" class="p-6">
                    <input type="hidden" name="action" value="site_settings">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="site_name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-tag mr-1 text-gray-500"></i>
                                Nombre del Sitio *
                            </label>
                            <input type="text" id="site_name" name="site_name" 
                                   value="<?php echo htmlspecialchars($site_config['site_name']); ?>" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="site_email" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-envelope mr-1 text-gray-500"></i>
                                Email de Contacto *
                            </label>
                            <input type="email" id="site_email" name="site_email" 
                                   value="<?php echo htmlspecialchars($site_config['site_email']); ?>" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                        </div>

                        <div class="md:col-span-2">
                            <label for="site_description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-1 text-gray-500"></i>
                                Descripción del Sitio
                            </label>
                            <textarea id="site_description" name="site_description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"><?php echo htmlspecialchars($site_config['site_description']); ?></textarea>
                        </div>

                        <div>
                            <label for="site_phone" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-phone mr-1 text-gray-500"></i>
                                Teléfono de Contacto
                            </label>
                            <input type="text" id="site_phone" name="site_phone" 
                                   value="<?php echo htmlspecialchars($site_config['site_phone']); ?>" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Configuración de Radio -->
        <div id="content-radio" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-broadcast-tower mr-2 text-indigo-600"></i>
                        Configuración de Radio
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Configuración del streaming y datos de la radio</p>
                </div>
                
                <form method="POST" class="p-6">
                    <input type="hidden" name="action" value="radio_settings">
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="radio_name" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-radio mr-1 text-gray-500"></i>
                                Nombre de la Radio *
                            </label>
                            <input type="text" id="radio_name" name="radio_name" 
                                   value="<?php echo htmlspecialchars($radio_config['radio_name']); ?>" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                        </div>

                        <div>
                            <label for="radio_genre" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-music mr-1 text-gray-500"></i>
                                Género Musical
                            </label>
                            <input type="text" id="radio_genre" name="radio_genre" 
                                   value="<?php echo htmlspecialchars($radio_config['radio_genre']); ?>" 
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                        </div>

                        <div class="md:col-span-2">
                            <label for="stream_url" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-link mr-1 text-gray-500"></i>
                                URL del Stream *
                            </label>
                            <input type="url" id="stream_url" name="stream_url" 
                                   value="<?php echo htmlspecialchars($radio_config['stream_url']); ?>" 
                                   required
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                            <p class="text-xs text-gray-500 mt-1">URL completa del streaming de audio</p>
                        </div>

                        <div class="md:col-span-2">
                            <label for="radio_description" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-align-left mr-1 text-gray-500"></i>
                                Descripción de la Radio
                            </label>
                            <textarea id="radio_description" name="radio_description" rows="3"
                                      class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors"><?php echo htmlspecialchars($radio_config['radio_description']); ?></textarea>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Configuración de Usuarios -->
        <div id="content-users" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-users mr-2 text-indigo-600"></i>
                        Configuración de Usuarios
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Configuración de registro y permisos de usuarios</p>
                </div>
                
                <form method="POST" class="p-6">
                    <input type="hidden" name="action" value="user_settings">
                    
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Permitir Registro Público</h3>
                                <p class="text-sm text-gray-500">Los usuarios pueden registrarse sin invitación</p>
                            </div>
                            <input type="checkbox" name="allow_registration" 
                                   <?php echo $user_config['allow_registration'] ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>

                        <div>
                            <label for="default_role" class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="fas fa-user-tag mr-1 text-gray-500"></i>
                                Rol por Defecto para Nuevos Usuarios
                            </label>
                            <select id="default_role" name="default_role" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-colors">
                                <option value="editor" <?php echo $user_config['default_role'] === 'editor' ? 'selected' : ''; ?>>Editor</option>
                                <option value="admin" <?php echo $user_config['default_role'] === 'admin' ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Verificación de Email</h3>
                                <p class="text-sm text-gray-500">Requerir verificación de email para nuevos usuarios</p>
                            </div>
                            <input type="checkbox" name="require_email_verification" 
                                   <?php echo $user_config['require_email_verification'] ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Configuración del Sistema -->
        <div id="content-system" class="tab-content hidden">
            <div class="bg-white rounded-xl shadow-md overflow-hidden">
                <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">
                        <i class="fas fa-server mr-2 text-indigo-600"></i>
                        Configuración del Sistema
                    </h2>
                    <p class="text-sm text-gray-600 mt-1">Configuración técnica y de mantenimiento</p>
                </div>
                
                <form method="POST" class="p-6">
                    <input type="hidden" name="action" value="system_settings">
                    
                    <div class="space-y-6">
                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Modo Mantenimiento</h3>
                                <p class="text-sm text-gray-500">Mostrar página de mantenimiento a los visitantes</p>
                            </div>
                            <input type="checkbox" name="maintenance_mode" 
                                   <?php echo $system_config['maintenance_mode'] ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Modo Debug</h3>
                                <p class="text-sm text-gray-500">Mostrar errores detallados (solo para desarrollo)</p>
                            </div>
                            <input type="checkbox" name="debug_mode" 
                                   <?php echo $system_config['debug_mode'] ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>

                        <div class="flex items-center justify-between p-4 bg-gray-50 rounded-lg">
                            <div>
                                <h3 class="text-sm font-medium text-gray-900">Cache Habilitado</h3>
                                <p class="text-sm text-gray-500">Usar sistema de cache para mejorar rendimiento</p>
                            </div>
                            <input type="checkbox" name="cache_enabled" 
                                   <?php echo $system_config['cache_enabled'] ? 'checked' : ''; ?>
                                   class="h-4 w-4 text-indigo-600 focus:ring-indigo-500 border-gray-300 rounded">
                        </div>

                        <!-- Información del Sistema -->
                        <div class="border-t pt-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información del Sistema</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700">Versión PHP</h4>
                                    <p class="text-lg font-bold text-gray-900"><?php echo PHP_VERSION; ?></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700">Servidor Web</h4>
                                    <p class="text-lg font-bold text-gray-900"><?php echo $_SERVER['SERVER_SOFTWARE'] ?? 'Desconocido'; ?></p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700">Base de Datos</h4>
                                    <p class="text-lg font-bold text-gray-900">MySQL/MariaDB</p>
                                </div>
                                <div class="bg-gray-50 p-4 rounded-lg">
                                    <h4 class="text-sm font-medium text-gray-700">Zona Horaria</h4>
                                    <p class="text-lg font-bold text-gray-900"><?php echo date_default_timezone_get(); ?></p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-medium transition-colors flex items-center">
                            <i class="fas fa-save mr-2"></i>
                            Guardar Configuración
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function showTab(tabName) {
            // Ocultar todos los contenidos
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.add('hidden');
            });
            
            // Remover clase activa de todos los botones
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active', 'border-indigo-500', 'text-indigo-600');
                button.classList.add('border-transparent', 'text-gray-500');
            });
            
            // Mostrar contenido seleccionado
            document.getElementById('content-' + tabName).classList.remove('hidden');
            
            // Activar botón seleccionado
            const activeButton = document.getElementById('tab-' + tabName);
            activeButton.classList.add('active', 'border-indigo-500', 'text-indigo-600');
            activeButton.classList.remove('border-transparent', 'text-gray-500');
        }
        
        // Inicializar primera pestaña
        document.addEventListener('DOMContentLoaded', function() {
            showTab('site');
        });
    </script>
</body>
</html>
