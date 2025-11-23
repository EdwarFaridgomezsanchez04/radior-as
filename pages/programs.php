<?php
// Detectar si se está accediendo directamente o a través de index.php
$is_direct_access = basename($_SERVER['PHP_SELF']) === 'programs.php';
$base_path = $is_direct_access ? '../' : '';

// Incluir archivos de configuración
if ($is_direct_access) {
    require_once(__DIR__ . '/../config/conexion.php');
    require_once(__DIR__ . '/../config/translations.php');
    $lang = 'es'; // Idioma por defecto para acceso directo
    $t = $translations[$lang];
} else {
    // Ya están incluidos desde index.php
    require_once('config/conexion.php');
}

$conex = new database();
$con = $conex->conectar();

// Obtener programas activos
$sql = "SELECT * FROM programas WHERE activo = 1 ORDER BY nombre_es";
$programas = $con->query($sql)->fetchAll(PDO::FETCH_ASSOC);

// Agrupar programas por días para la programación
$programacion = [];
foreach ($programas as $programa) {
    $dias_array = explode(',', $programa['dias']);
    foreach ($dias_array as $dia) {
        $dia = trim($dia);
        if (!isset($programacion[$dia])) {
            $programacion[$dia] = [];
        }
        $programacion[$dia][] = $programa;
    }
}
?>
<?php if ($is_direct_access): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programas - Radio Morrazo</title>
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
<body class="bg-white">
<?php endif; ?>

<!-- Programs Header -->
<section class="py-20 bg-gradient-to-br from-radio-teal via-radio-cyan to-teal-600 overflow-hidden relative">
    <!-- Animated background elements -->
    <div class="absolute inset-0">
        <div class="absolute top-10 sm:top-20 left-5 sm:left-10 w-16 sm:w-32 h-16 sm:h-32 bg-white bg-opacity-10 rounded-full animate-pulse"></div>
        <div class="absolute top-20 sm:top-40 right-10 sm:right-20 w-12 sm:w-24 h-12 sm:h-24 bg-white bg-opacity-5 rounded-full animate-bounce"></div>
        <div class="absolute bottom-16 sm:bottom-32 left-1/4 w-8 sm:w-16 h-8 sm:h-16 bg-white bg-opacity-15 rounded-full animate-ping"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl font-black mb-4 sm:mb-6 leading-none">
            <span class="text-white drop-shadow-2xl">PROGRAMAS</span>
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            Descubre nuestra programación completa
        </p>
        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center mb-12 sm:mb-16 px-4">
            <a href="#programacion" class="group bg-white text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                <i class="fas fa-calendar mr-2 sm:mr-3 group-hover:scale-110 transition-transform"></i>
                Ver Programación
            </a>
            <a href="#programas" class="group border-2 sm:border-3 border-white text-white hover:bg-white hover:text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                <i class="fas fa-microphone mr-2 sm:mr-3 group-hover:rotate-180 transition-transform duration-500"></i>
                Todos los Programas
            </a>
        </div>
    </div>
</section>

<!-- Programación Semanal -->
<section id="programacion" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-white via-slate-50 to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                Programación Semanal
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
                Conoce todos nuestros programas y sus horarios de emisión
            </p>
        </div>
        
        <?php if (empty($programas)): ?>
            <div class="text-center py-16">
                <i class="fas fa-microphone text-6xl text-gray-400 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-4">No hay programas disponibles</h3>
                <p class="text-gray-500">La programación se actualizará pronto.</p>
            </div>
        <?php else: ?>
            <!-- Días de la semana -->
            <div class="grid grid-cols-1 lg:grid-cols-7 gap-6">
                <?php 
                $dias_semana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado', 'Domingo'];
                $colores = [
                    'from-blue-500 to-blue-600',
                    'from-green-500 to-green-600', 
                    'from-purple-500 to-purple-600',
                    'from-orange-500 to-orange-600',
                    'from-red-500 to-red-600',
                    'from-pink-500 to-pink-600',
                    'from-indigo-500 to-indigo-600'
                ];
                
                foreach ($dias_semana as $index => $dia): 
                    $programas_dia = [];
                    foreach ($programas as $programa) {
                        if (stripos($programa['dias'], $dia) !== false) {
                            $programas_dia[] = $programa;
                        }
                    }
                ?>
                    <div class="bg-white rounded-2xl shadow-lg overflow-hidden">
                        <div class="bg-gradient-to-r <?php echo $colores[$index]; ?> text-white p-4 text-center">
                            <h3 class="font-bold text-lg"><?php echo $dia; ?></h3>
                        </div>
                        <div class="p-4 space-y-3">
                            <?php if (empty($programas_dia)): ?>
                                <p class="text-gray-500 text-sm text-center py-4">Sin programas</p>
                            <?php else: ?>
                                <?php foreach ($programas_dia as $programa): ?>
                                    <div class="border-l-4 border-radio-teal pl-3 py-2">
                                        <h4 class="font-bold text-sm text-gray-800"><?php echo htmlspecialchars($programa['nombre_es']); ?></h4>
                                        <p class="text-xs text-gray-600"><?php echo htmlspecialchars($programa['horario']); ?></p>
                                    </div>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Todos los Programas -->
<section id="programas" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-slate-50 via-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                Nuestros Programas
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-lg sm:text-xl text-gray-600 max-w-3xl mx-auto">
                Conoce en detalle todos los programas que conforman nuestra parrilla
            </p>
        </div>
        
        <?php if (empty($programas)): ?>
            <div class="text-center py-16">
                <i class="fas fa-broadcast-tower text-6xl text-gray-400 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-4">No hay programas disponibles</h3>
                <p class="text-gray-500">Pronto tendremos nuevos programas para ti.</p>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                <?php 
                $colores_programas = [
                    ['from-radio-teal', 'to-radio-cyan', 'radio-teal'],
                    ['from-purple-600', 'to-pink-600', 'purple-600'],
                    ['from-emerald-500', 'to-emerald-600', 'emerald-600'],
                    ['from-orange-500', 'to-orange-600', 'orange-600'],
                    ['from-blue-500', 'to-blue-600', 'blue-600'],
                    ['from-red-500', 'to-red-600', 'red-600']
                ];
                
                foreach ($programas as $index => $programa): 
                    $color = $colores_programas[$index % count($colores_programas)];
                ?>
                    <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                        <!-- Imagen del programa -->
                        <div class="h-48 sm:h-64 bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> flex items-center justify-center relative overflow-hidden">
                            <?php if ($programa['imagen'] && file_exists(($is_direct_access ? __DIR__ . '/../' : '') . 'uploads/programas/images/' . $programa['imagen'])): ?>
                                <img src="<?php echo $base_path; ?>uploads/programas/images/<?php echo htmlspecialchars($programa['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($programa['nombre_es']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> flex items-center justify-center relative">
                                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-microphone text-3xl sm:text-4xl text-<?php echo $color[2]; ?>"></i>
                                    </div>
                                    <!-- Ondas de sonido animadas -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-32 h-32 border-2 border-white border-opacity-30 rounded-full animate-ping"></div>
                                        <div class="absolute w-40 h-40 border-2 border-white border-opacity-20 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                        </div>
                        
                        <!-- Contenido del programa -->
                        <div class="p-6 sm:p-8">
                            <div class="mb-3">
                                <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-2">
                                    <?php echo htmlspecialchars($programa['nombre_es']); ?>
                                </h3>
                                <p class="text-sm text-gray-500 mb-3">
                                    <?php echo htmlspecialchars($programa['nombre_gl']); ?>
                                </p>
                            </div>
                            
                            <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">
                                <?php echo htmlspecialchars(substr($programa['descripcion_es'], 0, 120)) . '...'; ?>
                            </p>
                            
                            <!-- Información del horario -->
                            <div class="space-y-2 mb-4">
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-clock mr-2 text-<?php echo $color[2]; ?>"></i>
                                    <span><?php echo htmlspecialchars($programa['horario']); ?></span>
                                </div>
                                <div class="flex items-center text-sm text-gray-600">
                                    <i class="fas fa-calendar mr-2 text-<?php echo $color[2]; ?>"></i>
                                    <span><?php echo htmlspecialchars($programa['dias']); ?></span>
                                </div>
                            </div>
                            
                            <!-- Botón de más información -->
                            <button onclick="mostrarDetalles(<?php echo $programa['id']; ?>)" 
                                    class="w-full bg-gradient-to-r <?php echo $color[0] . ' ' . $color[1]; ?> hover:opacity-90 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl">
                                <i class="fas fa-info-circle mr-2"></i>Más Información
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </div>
</section>

<!-- Modal para detalles del programa -->
<div id="programa-modal" class="fixed inset-0 bg-black bg-opacity-50 hidden z-50 flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <div class="p-6">
            <div class="flex justify-between items-start mb-4">
                <h3 id="modal-title" class="text-2xl font-bold text-gray-900"></h3>
                <button onclick="cerrarModal()" class="text-gray-400 hover:text-gray-600">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
            <div id="modal-content" class="space-y-4">
                <!-- El contenido se llenará dinámicamente -->
            </div>
        </div>
    </div>
</div>

<script>
const programasData = <?php echo json_encode($programas); ?>;

function mostrarDetalles(programaId) {
    const programa = programasData.find(p => p.id == programaId);
    if (!programa) return;
    
    document.getElementById('modal-title').textContent = programa.nombre_es;
    
    const modalContent = document.getElementById('modal-content');
    modalContent.innerHTML = `
        <div class="space-y-4">
            <div>
                <h4 class="font-bold text-gray-800 mb-2">Nombre en Gallego:</h4>
                <p class="text-gray-600">${programa.nombre_gl}</p>
            </div>
            
            <div>
                <h4 class="font-bold text-gray-800 mb-2">Descripción:</h4>
                <p class="text-gray-600 leading-relaxed">${programa.descripcion_es}</p>
            </div>
            
            <div>
                <h4 class="font-bold text-gray-800 mb-2">Descripción en Gallego:</h4>
                <p class="text-gray-600 leading-relaxed">${programa.descripcion_gl}</p>
            </div>
            
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Horario:</h4>
                    <p class="text-gray-600">${programa.horario}</p>
                </div>
                <div>
                    <h4 class="font-bold text-gray-800 mb-2">Días:</h4>
                    <p class="text-gray-600">${programa.dias}</p>
                </div>
            </div>
        </div>
    `;
    
    document.getElementById('programa-modal').classList.remove('hidden');
}

function cerrarModal() {
    document.getElementById('programa-modal').classList.add('hidden');
}

// Cerrar modal al hacer clic fuera
document.getElementById('programa-modal').addEventListener('click', function(e) {
    if (e.target === this) {
        cerrarModal();
    }
});

// Cerrar modal con Escape
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        cerrarModal();
    }
});
</script>

<?php if ($is_direct_access): ?>
</body>
</html>
<?php endif; ?>