<?php
require_once __DIR__ . '/../includes/validarsesion.php';
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Estadísticas - RadioRías</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        .hpanel-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
        }
        .radio-card {
            background: linear-gradient(135deg, #111827 0%, #374151 100%);
            color: white;
        }
        .pulse {
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
    </style>
</head>
<body class="bg-gray-100">
    <!-- Header -->
    <div class="bg-white shadow-sm border-b">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center py-4">
                <div class="flex items-center">
                    <a href="dashboard.php" class="text-gray-600 hover:text-gray-900 mr-4">
                        <i class="fas fa-arrow-left"></i> Volver al Dashboard
                    </a>
                    <h1 class="text-2xl font-bold text-gray-900">
                        <i class="fas fa-chart-line text-blue-600 mr-2"></i>
                        Estadísticas RadioRías
                    </h1>
                </div>
                <div class="text-sm text-gray-500">
                    Sistema híbrido: hPanel + Radio en vivo
                </div>
            </div>
        </div>
    </div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Estadísticas Principales -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            
            <!-- hPanel Analytics -->
            <div class="hpanel-card rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-globe mr-2"></i>
                        Estadísticas Generales
                    </h3>
                    <i class="fas fa-external-link-alt text-white/70"></i>
                </div>
                <p class="text-white/90 mb-4 text-sm">
                    Visitantes, páginas vistas, descargas, países, dispositivos y más.
                </p>
                <a href="https://hpanel.hostinger.com" target="_blank" 
                   class="inline-flex items-center px-4 py-2 bg-white/20 hover:bg-white/30 rounded-lg text-white font-medium transition-colors">
                    <i class="fas fa-chart-bar mr-2"></i>
                    Ver en hPanel
                </a>
                <div class="mt-4 text-xs text-white/70">
                    ✅ Automático • Sin programación • Datos confiables
                </div>
            </div>

            <!-- Radio en Vivo -->
            <div class="radio-card rounded-lg p-6 shadow-lg">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-semibold">
                        <i class="fas fa-broadcast-tower mr-2"></i>
                        Radio en Vivo
                    </h3>
                    <div class="pulse">
                        <i class="fas fa-circle text-red-300 text-xs"></i>
                    </div>
                </div>
                <div class="space-y-3">
                    <div class="flex justify-between">
                        <span class="text-white/90">Oyentes activos:</span>
                        <span id="active-listeners" class="font-bold">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/90">Reproducciones hoy:</span>
                        <span id="today-plays" class="font-bold">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/90">Oyentes únicos:</span>
                        <span id="unique-listeners" class="font-bold">-</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-white/90">Tiempo promedio:</span>
                        <span id="avg-duration" class="font-bold">-</span>
                    </div>
                </div>
                <div class="mt-4 text-xs text-white/70">
                    🔄 Actualización automática cada 30s
                </div>
            </div>

            <!-- Accesos Rápidos -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-link mr-2 text-blue-600"></i>
                    Accesos Rápidos
                </h3>
                <div class="space-y-3">
                    <a href="https://hpanel.hostinger.com/analytics" target="_blank" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-users text-blue-500 mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-900">Visitantes</div>
                            <div class="text-sm text-gray-500">Únicos, países, dispositivos</div>
                        </div>
                    </a>
                    <a href="https://hpanel.hostinger.com/analytics" target="_blank" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-eye text-green-500 mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-900">Páginas Vistas</div>
                            <div class="text-sm text-gray-500">Más populares, tiempo en sitio</div>
                        </div>
                    </a>
                    <a href="https://hpanel.hostinger.com/analytics" target="_blank" 
                       class="flex items-center p-3 bg-gray-50 hover:bg-gray-100 rounded-lg transition-colors">
                        <i class="fas fa-download text-purple-500 mr-3"></i>
                        <div>
                            <div class="font-medium text-gray-900">Descargas</div>
                            <div class="text-sm text-gray-500">Podcasts MP3, archivos</div>
                        </div>
                    </a>
                </div>
            </div>
        </div>

        <!-- Información del Sistema -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            
            <!-- Lo que rastrea hPanel -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-check-circle text-green-500 mr-2"></i>
                    hPanel rastrea automáticamente
                </h3>
                <div class="grid grid-cols-2 gap-4 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-users text-blue-500 mr-2"></i>
                        Visitantes únicos
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-eye text-green-500 mr-2"></i>
                        Páginas vistas
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-download text-purple-500 mr-2"></i>
                        Descargas de archivos
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-clock text-orange-500 mr-2"></i>
                        Tiempo en el sitio
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-mobile-alt text-indigo-500 mr-2"></i>
                        Dispositivos
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-browser text-red-500 mr-2"></i>
                        Navegadores
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-globe text-teal-500 mr-2"></i>
                        Países de origen
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-chart-line text-pink-500 mr-2"></i>
                        Tendencias
                    </div>
                </div>
                <div class="mt-4 p-3 bg-green-50 rounded-lg">
                    <div class="text-sm text-green-800">
                        <i class="fas fa-info-circle mr-1"></i>
                        <strong>Sin programación:</strong> hPanel recopila estos datos automáticamente
                    </div>
                </div>
            </div>

            <!-- Lo que rastrea nuestro sistema -->
            <div class="bg-white rounded-lg p-6 shadow-lg">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">
                    <i class="fas fa-broadcast-tower text-red-500 mr-2"></i>
                    Nuestro sistema rastrea
                </h3>
                <div class="space-y-3 text-sm">
                    <div class="flex items-center">
                        <i class="fas fa-play text-red-500 mr-2"></i>
                        Reproducciones de radio en vivo
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-headphones text-blue-500 mr-2"></i>
                        Oyentes activos en tiempo real
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-stopwatch text-green-500 mr-2"></i>
                        Tiempo de escucha de radio
                    </div>
                    <div class="flex items-center">
                        <i class="fas fa-mouse-pointer text-purple-500 mr-2"></i>
                        Interacciones con el reproductor
                    </div>
                </div>
                <div class="mt-4 p-3 bg-blue-50 rounded-lg">
                    <div class="text-sm text-blue-800">
                        <i class="fas fa-code mr-1"></i>
                        <strong>Sistema simple:</strong> Solo lo que hPanel no puede rastrear
                    </div>
                </div>
            </div>
        </div>

        <!-- Ventajas del Sistema Híbrido -->
        <div class="mt-8 bg-gradient-to-r from-blue-50 to-indigo-50 rounded-lg p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">
                <i class="fas fa-rocket text-blue-600 mr-2"></i>
                Ventajas del Sistema Híbrido
            </h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                <div class="flex items-start">
                    <i class="fas fa-tachometer-alt text-green-500 mr-2 mt-1"></i>
                    <div>
                        <div class="font-medium text-gray-900">Mejor Rendimiento</div>
                        <div class="text-gray-600">Menos consultas SQL, servidor más rápido</div>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-shield-alt text-blue-500 mr-2 mt-1"></i>
                    <div>
                        <div class="font-medium text-gray-900">Datos Confiables</div>
                        <div class="text-gray-600">Analytics profesionales de Hostinger</div>
                    </div>
                </div>
                <div class="flex items-start">
                    <i class="fas fa-tools text-purple-500 mr-2 mt-1"></i>
                    <div>
                        <div class="font-medium text-gray-900">Menos Mantenimiento</div>
                        <div class="text-gray-600">hPanel se actualiza automáticamente</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Cargar estadísticas de radio
        async function loadRadioStats() {
            try {
                const response = await fetch('../api/radio-counter.php?action=get_stats');
                const result = await response.json();
                
                if (result.success) {
                    const stats = result.data;
                    
                    document.getElementById('active-listeners').textContent = stats.active_listeners;
                    document.getElementById('today-plays').textContent = stats.today_plays;
                    document.getElementById('unique-listeners').textContent = stats.unique_listeners_today;
                    document.getElementById('avg-duration').textContent = formatDuration(stats.avg_duration);
                } else {
                    console.error('Error loading radio stats:', result.message);
                }
            } catch (error) {
                console.error('Error fetching radio stats:', error);
            }
        }

        // Formatear duración
        function formatDuration(seconds) {
            if (seconds < 60) {
                return seconds + 's';
            } else if (seconds < 3600) {
                const minutes = Math.floor(seconds / 60);
                const secs = seconds % 60;
                return minutes + 'm ' + secs + 's';
            } else {
                const hours = Math.floor(seconds / 3600);
                const minutes = Math.floor((seconds % 3600) / 60);
                return hours + 'h ' + minutes + 'm';
            }
        }

        // Cargar estadísticas al inicio
        loadRadioStats();

        // Actualizar cada 30 segundos
        setInterval(loadRadioStats, 30000);

        console.log('📊 Estadísticas híbridas cargadas - hPanel + Radio simple');
    </script>
</body>
</html>
