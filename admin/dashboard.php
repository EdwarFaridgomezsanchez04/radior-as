<?php
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');
require_once('../includes/Tracker.php');

// Verificar que esté autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    echo "<script>alert('Debe iniciar sesión.');</script>";
    echo "<script>window.location='../login.php';</script>";
    exit();
}

// Obtener estadísticas reales
$conex = new database();
$con = $conex->conectar();
$tracker = new Tracker($con);

// Estadísticas generales
$stats = [];
try {
    // Usuarios totales
    $sql = "SELECT COUNT(*) as total, SUM(CASE WHEN activo = 1 THEN 1 ELSE 0 END) as activos FROM usuarios";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $usuarios_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_users'] = $usuarios_data['total'] ?? 0;
    $stats['active_users'] = $usuarios_data['activos'] ?? 0;
    
    // Programas
    $sql = "SELECT COUNT(*) as total FROM programas WHERE activo = 1";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $stats['total_programs'] = $stmt->fetchColumn();
    
    // Podcasts
    $sql = "SELECT COUNT(*) as total, SUM(reproducciones) as total_plays FROM podcasts WHERE activo = 1";
    $stmt = $con->prepare($sql);
    $stmt->execute();
    $podcasts_data = $stmt->fetch(PDO::FETCH_ASSOC);
    $stats['total_podcasts'] = $podcasts_data['total'] ?? 0;
    $stats['total_plays'] = $podcasts_data['total_plays'] ?? 0;
    
    // Datos del historial
    $history_stats = $tracker->getHistoryStats();
    $stats['unique_listeners'] = $history_stats['unique_users'] ?? 0;
    $stats['total_events'] = $history_stats['total_events'] ?? 0;
    
} catch (Exception $e) {
    error_log("Error al obtener stats: " . $e->getMessage());
    // Valores por defecto
    $stats['total_users'] = 0;
    $stats['active_users'] = 0;
    $stats['total_programs'] = 0;
    $stats['total_podcasts'] = 0;
    $stats['total_plays'] = 0;
    $stats['unique_listeners'] = 0;
    $stats['total_events'] = 0;
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - RadioRías</title>
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
<body class="bg-gradient-to-br from-slate-50 to-slate-100 min-h-screen">
    <!-- Header -->
    <header class="bg-gradient-to-r from-gray-800 to-gray-900 shadow-lg border-b border-gray-700">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <img src="../assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-10 h-10 rounded-full">
                    <div>
                        <h1 class="text-2xl font-black text-white">Dashboard RadioRías</h1>
                        <p class="text-gray-100">Bienvenido, <?php echo htmlspecialchars($_SESSION['nombre']); ?></p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <div class="hidden sm:flex items-center space-x-2 bg-gray-700 bg-opacity-50 text-gray-100 px-3 py-1 rounded-full">
                        <div class="w-2 h-2 bg-yellow-300 rounded-full animate-pulse"></div>
                        <span class="text-sm font-bold">ADMIN</span>
                    </div>
                    <a href="../includes/salir.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Cerrar Sesión</span>
                    </a>
                </div>
            </div>
        </div>
    </header>
    
    <!-- Contenido principal -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Información del administrador -->
        <div class="bg-white rounded-2xl shadow-lg p-6 mb-8 border-l-4 border-gray-600">
            <h2 class="text-xl font-bold text-slate-800 mb-4 flex items-center">
                <i class="fas fa-user mr-3 text-gray-600"></i>
                Información del Usuario
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <p class="text-gray-600 text-sm">ID de Usuario</p>
                    <p class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['user_id']); ?></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <p class="text-gray-600 text-sm">Nombre de Usuario</p>
                    <p class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['username']); ?></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <p class="text-gray-600 text-sm">Email</p>
                    <p class="font-bold text-slate-800"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                </div>
                <div class="bg-gray-50 p-4 rounded-xl border border-gray-200">
                    <p class="text-gray-600 text-sm">Rol</p>
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium <?php echo $_SESSION['rol'] === 'admin' ? 'bg-gray-100 text-gray-800' : 'bg-blue-100 text-blue-800'; ?>">
                        <i class="fas fa-<?php echo $_SESSION['rol'] === 'admin' ? 'crown' : 'user'; ?> mr-1"></i>
                        <?php echo strtoupper($_SESSION['rol']); ?>
                    </span>
                </div>
            </div>
        </div>
        
        <!-- Estadísticas de administración -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 mb-8" id="dashboard-stats">
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-blue-500">
                <div class="flex items-center mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-blue-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-users text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-1">
                    <?php echo number_format($stats['total_users']); ?>
                </h3>
                <p class="text-slate-500 text-sm">Usuarios totales (<?php echo $stats['active_users']; ?> activos)</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-purple-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-purple-500 to-purple-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-microphone text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-1">
                    <?php echo number_format($stats['total_programs']); ?>
                </h3>
                <p class="text-slate-500 text-sm">Programas activos</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-orange-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-orange-500 to-orange-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-podcast text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-1">
                    <?php echo number_format($stats['total_podcasts']); ?>
                </h3>
                <p class="text-slate-500 text-sm">Podcasts publicados (<?php echo number_format($stats['total_plays']); ?> reproducidos)</p>
            </div>
            
            <div class="bg-white rounded-2xl p-6 shadow-lg border-l-4 border-green-500">
                <div class="flex items-center justify-between mb-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-green-500 to-green-600 rounded-xl flex items-center justify-center">
                        <i class="fas fa-headphones text-white text-xl"></i>
                    </div>
                </div>
                <h3 class="text-2xl font-black text-slate-800 mb-1">
                    <?php echo number_format($stats['unique_listeners']); ?>
                </h3>
                <p class="text-slate-500 text-sm">Eventos en historial (30d)</p>
            </div>
        </div>
        
        <!-- Herramientas de administración -->
        <div class="bg-white rounded-2xl shadow-lg p-6">
            <h2 class="text-xl font-bold text-slate-800 mb-6 flex items-center">
                <i class="fas fa-tools mr-3 text-gray-600"></i>
                <?php echo $_SESSION['rol'] === 'admin' ? 'Herramientas de Administración' : 'Herramientas Disponibles'; ?>
            </h2>
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                <a href="usuarios.php" class="bg-gradient-to-br from-gray-700 to-gray-800 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-users-cog text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Gestionar Usuarios</h3>
                            <p class="text-sm opacity-90">Crear, editar y eliminar usuarios</p>
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                
                <a href="programas.php" class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-broadcast-tower text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Gestionar Programas</h3>
                            <p class="text-sm opacity-90">Programación y horarios</p>
                        </div>
                    </div>
                </a>
                
                <a href="podcasts.php" class="bg-gradient-to-br from-orange-500 to-orange-600 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-podcast text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Gestionar Podcasts</h3>
                            <p class="text-sm opacity-90">Subir y administrar contenido</p>
                        </div>
                    </div>
                </a>
                
                <a href="noticias.php" class="bg-gradient-to-br from-purple-500 to-purple-600 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-newspaper text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Gestionar Noticias</h3>
                            <p class="text-sm opacity-90">Crear y administrar noticias</p>
                        </div>
                    </div>
                </a>
                
                <a href="estadisticas-simple.php" class="bg-gradient-to-br from-green-500 to-green-600 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-chart-bar text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Estadísticas</h3>
                            <p class="text-sm opacity-90">Reportes y análisis</p>
                        </div>
                    </div>
                </a>
                
                <a href="configuracion.php" class="bg-gradient-to-br from-indigo-500 to-indigo-600 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-cogs text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Configuración</h3>
                            <p class="text-sm opacity-90">Ajustes del sistema</p>
                        </div>
                    </div>
                </a>
                
                <a href="mensajes.php" class="bg-gradient-to-br from-blue-500 to-blue-600 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-envelope text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Mensajes de Contacto</h3>
                            <p class="text-sm opacity-90">Ver formularios enviados</p>
                        </div>
                    </div>
                </a>
                
                <?php if ($_SESSION['rol'] === 'admin'): ?>
                <a href="historial.php" class="bg-gradient-to-br from-red-500 to-red-600 text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-history text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Historial de Actividades</h3>
                            <p class="text-sm opacity-90">Registro de todas las acciones</p>
                        </div>
                    </div>
                </a>
                <?php endif; ?>
                
                <a href="../index.php" class="bg-gradient-to-br from-radio-teal to-radio-cyan text-white p-6 rounded-xl hover:shadow-lg transition-all duration-300 transform hover:-translate-y-1">
                    <div class="flex items-center space-x-4">
                        <i class="fas fa-home text-2xl"></i>
                        <div>
                            <h3 class="font-bold">Ver Sitio Web</h3>
                            <p class="text-sm opacity-90">Ir al sitio público</p>
                        </div>
                    </div>
                </a>
            </div>
        </div>
    </main>

    <script>
        // Cargar estadísticas del dashboard desde la API
        async function loadDashboardStats() {
            try {
                const response = await fetch('../api/analytics.php?action=dashboard_stats');
                const result = await response.json();
                
                if (result.success) {
                    const data = result.data;
                    
                    // Actualizar estadísticas principales
                    document.getElementById('total-users').textContent = data.total_users || 0;
                    document.getElementById('total-programs').textContent = data.total_programs || 0;
                    document.getElementById('total-podcasts').textContent = data.total_podcasts || 0;
                    document.getElementById('total-listeners').textContent = (data.total_listeners || 0).toLocaleString();
                    
                    // Actualizar indicadores de crecimiento
                    document.getElementById('users-growth').textContent = '+' + (data.growth_users || 0);
                    document.getElementById('programs-growth').textContent = '+' + (data.growth_programs || 0);
                    document.getElementById('podcasts-growth').textContent = '+' + (data.growth_podcasts || 0);
                    document.getElementById('listeners-growth').textContent = '+' + (data.growth_listeners || 0) + '%';
                    
                    // Animación de conteo
                    animateCounters();
                } else {
                    console.error('Error al cargar estadísticas:', result.message);
                }
            } catch (error) {
                console.error('Error de conexión:', error);
                // Mostrar valores por defecto en caso de error
                document.getElementById('total-users').textContent = '0';
                document.getElementById('total-programs').textContent = '0';
                document.getElementById('total-podcasts').textContent = '0';
                document.getElementById('total-listeners').textContent = '0';
            }
        }
        
        // Animación de contadores
        function animateCounters() {
            const counters = document.querySelectorAll('#dashboard-stats h3');
            
            counters.forEach(counter => {
                const target = parseInt(counter.textContent.replace(/,/g, ''));
                if (isNaN(target)) return;
                
                let current = 0;
                const increment = target / 50;
                const timer = setInterval(() => {
                    current += increment;
                    if (current >= target) {
                        counter.textContent = target.toLocaleString();
                        clearInterval(timer);
                    } else {
                        counter.textContent = Math.floor(current).toLocaleString();
                    }
                }, 30);
            });
        }
        
        // Registrar vista de página
        async function trackPageView() {
            try {
                await fetch('../api/analytics.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        action: 'track_page_view',
                        page: 'admin_dashboard',
                        additional_data: {
                            user_role: '<?php echo $_SESSION['rol']; ?>',
                            timestamp: new Date().toISOString()
                        }
                    })
                });
            } catch (error) {
                console.error('Error al registrar vista:', error);
            }
        }
        
        // Cargar datos al cargar la página
        document.addEventListener('DOMContentLoaded', function() {
            loadDashboardStats();
            trackPageView();
            
            // Actualizar estadísticas cada 5 minutos
            setInterval(loadDashboardStats, 300000);
        });
    </script>
</body>
</html>
