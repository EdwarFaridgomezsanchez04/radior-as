<?php
// Detectar si se está accediendo directamente o a través de index.php
$is_direct_access = basename($_SERVER['PHP_SELF']) === 'podcasts.php';
$base_path = $is_direct_access ? '../' : '';

// Incluir archivos de configuración
if ($is_direct_access) {
    require_once(__DIR__ . '/../config/conexion.php');
    require_once(__DIR__ . '/../config/config.php');
    require_once(__DIR__ . '/../config/translations.php');
    $lang = 'es'; // Idioma por defecto para acceso directo
    $t = $translations[$lang];
} else {
    // Ya están incluidos desde index.php
    require_once('config/conexion.php');
    require_once('config/config.php');
}

$conex = new database();
$con = $conex->conectar();

// Incluir Historial para registro de eventos
require_once('config/conexion.php');
require_once('includes/Historial.php');
$historial = new Historial($con);

// Iniciar sesión si no está iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Obtener categorías de podcasts
$categorias_sql = "SELECT * FROM categorias_podcast WHERE activo = 1 ORDER BY nombre_es";
$categorias = $con->query($categorias_sql)->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$categoria_filter = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Construir consulta con filtros
$where_conditions = ["p.activo = 1"];
$params = [];

if ($categoria_filter > 0) {
    $where_conditions[] = "p.categoria_id = ?";
    $params[] = $categoria_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(p.titulo_es LIKE ? OR p.titulo_gl LIKE ? OR p.descripcion_es LIKE ?)";
    $search_param = "%$search%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Obtener podcasts
$sql = "SELECT p.*, 
               CASE WHEN pr.nombre_es IS NOT NULL THEN pr.nombre_es ELSE 'Sin programa' END as programa_nombre,
               CASE WHEN cp.nombre_es IS NOT NULL THEN cp.nombre_es ELSE 'General' END as categoria_nombre
        FROM podcasts p 
        LEFT JOIN programas pr ON p.programa_id = pr.id 
        LEFT JOIN categorias_podcast cp ON p.categoria_id = cp.id 
        $where_clause 
        ORDER BY p.destacado DESC, p.fecha DESC, p.fecha_creacion DESC";

$stmt = $con->prepare($sql);
$stmt->execute($params);
$podcasts_db = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Obtener videos de YouTube automáticamente
$youtube_videos = [];
try {
    if (defined('YOUTUBE_API_KEY') && defined('YOUTUBE_CHANNEL_ID')) {
        $apiKey = YOUTUBE_API_KEY;
        $channelId = YOUTUBE_CHANNEL_ID;
        
        if (!empty($apiKey) && !empty($channelId)) {
            // Obtener videos del canal de YouTube
            $url = "https://www.googleapis.com/youtube/v3/search?key={$apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults=50";
            
            $context = stream_context_create([
                'http' => [
                    'timeout' => 10,
                    'ignore_errors' => true
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response !== false) {
                $data = json_decode($response, true);
                
                if (isset($data['items']) && is_array($data['items'])) {
                    foreach ($data['items'] as $item) {
                        if (isset($item['id']['kind']) && $item['id']['kind'] === 'youtube#video') {
                            $videoId = $item['id']['videoId'] ?? '';
                            $thumbnail = '';
                            if (isset($item['snippet']['thumbnails']['high']['url'])) {
                                $thumbnail = $item['snippet']['thumbnails']['high']['url'];
                            } elseif (isset($item['snippet']['thumbnails']['default']['url'])) {
                                $thumbnail = $item['snippet']['thumbnails']['default']['url'];
                            }
                            
                            $youtube_videos[] = [
                                'id' => 'youtube_' . $videoId,
                                'titulo_es' => $item['snippet']['title'] ?? 'Sin título',
                                'titulo_gl' => $item['snippet']['title'] ?? 'Sin título',
                                'descripcion_es' => $item['snippet']['description'] ?? '',
                                'fecha' => isset($item['snippet']['publishedAt']) ? date('Y-m-d', strtotime($item['snippet']['publishedAt'])) : date('Y-m-d'),
                                'imagen' => $thumbnail,
                                'youtube_id' => $videoId,
                                'youtube_url' => 'https://www.youtube.com/watch?v=' . $videoId,
                                'reproducciones' => 0,
                                'descargas' => 0,
                                'destacado' => 0,
                                'categoria_nombre' => 'YouTube',
                                'programa_nombre' => 'YouTube',
                                'duracion' => null,
                                'archivo' => null
                            ];
                        }
                    }
                }
            }
        }
    }
} catch (Exception $e) {
    // Silenciar errores de YouTube
}

// Combinar podcasts de BD con videos de YouTube
$podcasts = array_merge($youtube_videos, $podcasts_db);

// Ordenar por fecha (más recientes primero)
usort($podcasts, function($a, $b) {
    return strtotime($b['fecha']) - strtotime($a['fecha']);
});

// Actualizar reproducciones si se solicita
if (isset($_POST['play_podcast'])) {
    $podcast_id = (int)$_POST['podcast_id'];
    $update_sql = "UPDATE podcasts SET reproducciones = reproducciones + 1 WHERE id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->execute([$podcast_id]);
    
    // Registrar en historial
    $podcast_info = $con->query("SELECT titulo_es FROM podcasts WHERE id = {$podcast_id}")->fetch(PDO::FETCH_ASSOC);
    $historial->registrar('play', 'podcast', 'Reprodujo podcast', [
        'entidad_id' => $podcast_id,
        'entidad_nombre' => $podcast_info['titulo_es'] ?? 'Podcast #' . $podcast_id,
        'usuario_nombre' => $_SESSION['username'] ?? 'Visitante'
    ]);
}

// Actualizar descargas si se solicita
if (isset($_POST['download_podcast'])) {
    $podcast_id = (int)$_POST['podcast_id'];
    $update_sql = "UPDATE podcasts SET descargas = descargas + 1 WHERE id = ?";
    $update_stmt = $con->prepare($update_sql);
    $update_stmt->execute([$podcast_id]);
    
    // Registrar en historial
    $podcast_info = $con->query("SELECT titulo_es FROM podcasts WHERE id = {$podcast_id}")->fetch(PDO::FETCH_ASSOC);
    $historial->registrar('download', 'podcast', 'Descargó podcast', [
        'entidad_id' => $podcast_id,
        'entidad_nombre' => $podcast_info['titulo_es'] ?? 'Podcast #' . $podcast_id,
        'usuario_nombre' => $_SESSION['username'] ?? 'Visitante'
    ]);
}

// Registrar visualización de video de YouTube
if (isset($_POST['view_youtube'])) {
    $youtube_id = $_POST['youtube_id'] ?? '';
    $title = $_POST['title'] ?? '';
    
    $historial->registrar('view', 'podcast', 'Visualizó video de YouTube', [
        'entidad_nombre' => $title ?: 'Video de YouTube',
        'usuario_nombre' => $_SESSION['username'] ?? 'Visitante',
        'detalles' => [
            'youtube_id' => $youtube_id,
            'tipo' => 'youtube'
        ]
    ]);
}
?>
<?php if ($is_direct_access): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podcasts - Radio Morrazo</title>
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

<!-- Podcasts Header -->
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
            <span class="text-white drop-shadow-2xl">PODCASTS</span>
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            Descubre nuestros episodios exclusivos
        </p>
        
        <!-- Búsqueda -->
        <form method="GET" class="flex justify-center mb-12 sm:mb-16 px-4">
            <div class="bg-white bg-opacity-20 backdrop-blur-lg p-2 rounded-2xl shadow-2xl flex gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Buscar podcasts..." 
                       class="px-4 sm:px-6 py-2 sm:py-3 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-300 w-80 sm:w-96 text-slate-800 placeholder-slate-500">
                <input type="hidden" name="categoria" value="<?php echo $categoria_filter; ?>">
                <button type="submit" class="bg-white text-radio-teal px-4 py-2 rounded-xl font-bold hover:bg-gray-100 transition-colors">
                    <i class="fas fa-search"></i>
                </button>
            </div>
        </form>
    </div>
</section>

<!-- Category Filter -->
<section class="py-8 sm:py-12 bg-gradient-to-br from-slate-50 via-gray-50 to-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6">Categorías</h3>
            <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
                <a href="?search=<?php echo urlencode($search); ?>" 
                   class="category-btn <?php echo $categoria_filter === 0 ? 'bg-radio-teal text-white' : 'bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white'; ?> px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    Todas las categorías
                </a>
                <?php foreach ($categorias as $categoria): ?>
                    <a href="?categoria=<?php echo $categoria['id']; ?>&search=<?php echo urlencode($search); ?>" 
                       class="category-btn <?php echo $categoria_filter === (int)$categoria['id'] ? 'bg-radio-teal text-white' : 'bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white'; ?> px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <?php echo htmlspecialchars($categoria['nombre_es']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Podcasts Grid -->
<section id="episodios" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-white via-slate-50 to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo empty($podcasts) ? 'No se encontraron podcasts' : (count($podcasts) . ' Episodios Disponibles'); ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
        </div>
        
        <?php if (empty($podcasts)): ?>
            <div class="text-center py-16">
                <i class="fas fa-podcast text-6xl text-gray-400 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-4">No hay podcasts disponibles</h3>
                <p class="text-gray-500 mb-8">Prueba con otros filtros de búsqueda o categorías.</p>
                <a href="podcasts.php" class="bg-radio-teal hover:bg-radio-cyan text-white px-6 py-3 rounded-xl font-bold transition-colors">
                    Ver todos los podcasts
                </a>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8" id="podcasts-grid">
                <?php 
                $colors = [
                    ['from-radio-teal', 'to-radio-cyan', 'radio-teal'],
                    ['from-purple-600', 'to-pink-600', 'purple-600'],
                    ['from-emerald-500', 'to-emerald-600', 'emerald-600'],
                    ['from-orange-500', 'to-orange-600', 'orange-600'],
                    ['from-pink-500', 'to-pink-600', 'pink-600'],
                    ['from-blue-500', 'to-blue-600', 'blue-600']
                ];
                
                foreach ($podcasts as $index => $podcast): 
                    $color = $colors[$index % count($colors)];
                ?>
                    <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" 
                         data-category="<?php echo strtolower($podcast['categoria_nombre']); ?>"
                         data-title="<?php echo htmlspecialchars($podcast['titulo_es']); ?>"
                         data-description="<?php echo htmlspecialchars($podcast['descripcion_es']); ?>">
                        
                        <!-- Imagen del podcast -->
                        <div class="h-48 sm:h-64 bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> flex items-center justify-center relative overflow-hidden">
                            <?php if (!empty($podcast['youtube_id']) && !empty($podcast['imagen'])): ?>
                                <!-- Imagen de YouTube -->
                                <img src="<?php echo htmlspecialchars($podcast['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($podcast['titulo_es']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300"
                                     onerror="this.parentElement.innerHTML='<div class=\\'w-full h-full bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> flex items-center justify-center relative\\'><div class=\\'w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300\\'><i class=\\'fas fa-podcast text-3xl sm:text-4xl text-<?php echo $color[2]; ?>\\'></i></div></div>'">
                            <?php elseif ($podcast['imagen'] && file_exists(($is_direct_access ? __DIR__ . '/../' : '') . 'uploads/podcasts/images/' . $podcast['imagen'])): ?>
                                <!-- Imagen local -->
                                <img src="<?php echo $base_path; ?>uploads/podcasts/images/<?php echo htmlspecialchars($podcast['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($podcast['titulo_es']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> flex items-center justify-center relative">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas fa-podcast text-3xl sm:text-4xl text-<?php echo $color[2]; ?>"></i>
                    </div>
                                    <!-- Ondas de sonido animadas -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-32 h-32 border-2 border-white border-opacity-30 rounded-full animate-ping"></div>
                                        <div class="absolute w-40 h-40 border-2 border-white border-opacity-20 rounded-full animate-pulse"></div>
                    </div>
                </div>
                            <?php endif; ?>
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 right-4 flex justify-between">
                                <?php if ($podcast['destacado']): ?>
                                    <div class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                                        <i class="fas fa-star mr-1"></i>DESTACADO
                    </div>
                                <?php else: ?>
                                    <div></div>
                                <?php endif; ?>
                                
                                <?php 
                                $fecha_podcast = new DateTime($podcast['fecha']);
                                $fecha_actual = new DateTime();
                                $diferencia = $fecha_actual->diff($fecha_podcast);
                                if ($diferencia->days <= 7): 
                                ?>
                                    <div class="bg-red-500 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                                        NUEVO
                    </div>
                                <?php endif; ?>
                </div>
            </div>

                        <!-- Contenido del podcast -->
                <div class="p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs sm:text-sm text-<?php echo $color[2]; ?> font-bold bg-<?php echo $color[2]; ?> bg-opacity-10 px-3 py-1 rounded-full">
                                    <?php echo strtoupper(htmlspecialchars($podcast['categoria_nombre'])); ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?php echo date('d/m/Y', strtotime($podcast['fecha'])); ?>
                                </span>
            </div>

                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3">
                                <?php echo htmlspecialchars($podcast['titulo_es']); ?>
                            </h3>
                            
                            <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">
                                <?php echo htmlspecialchars(substr($podcast['descripcion_es'], 0, 120)) . '...'; ?>
                            </p>
                            
                            <!-- Información adicional -->
                    <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <?php if ($podcast['duracion']): ?>
                                        <span class="border-2 border-<?php echo $color[2]; ?> px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-<?php echo $color[2]; ?>">
                                            <i class="fas fa-clock mr-1"></i><?php echo $podcast['duracion']; ?>
                                        </span>
                                    <?php endif; ?>
                                    
                                    <?php if ($podcast['programa_nombre'] !== 'Sin programa'): ?>
                                        <span class="text-xs text-gray-500">
                                            <?php echo htmlspecialchars($podcast['programa_nombre']); ?>
                                        </span>
                                    <?php endif; ?>
                </div>
            </div>

                            <!-- Estadísticas -->
                            <div class="flex items-center justify-between mb-4 text-xs text-gray-500">
                                <span><i class="fas fa-play mr-1"></i><?php echo number_format($podcast['reproducciones']); ?> reproducciones</span>
                                <span><i class="fas fa-download mr-1"></i><?php echo number_format($podcast['descargas']); ?> descargas</span>
                    </div>
                            
                            <!-- Botones de acción -->
                    <div class="flex gap-2">
                                <?php if (!empty($podcast['youtube_id'])): ?>
                                    <!-- Botón ver video en modal -->
                                    <button onclick="showVideoModal('<?php echo htmlspecialchars($podcast['youtube_id']); ?>', '<?php echo htmlspecialchars($podcast['titulo_es']); ?>', '<?php echo $podcast['id']; ?>')" 
                                            class="flex-1 bg-gradient-to-r <?php echo $color[0] . ' ' . $color[1]; ?> hover:opacity-90 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl text-center">
                                        <i class="fab fa-youtube mr-2"></i>Ver Video
                                    </button>
                                    
                                    <!-- Botón alternativo: Abrir en YouTube -->
                                    <a href="<?php echo htmlspecialchars($podcast['youtube_url']); ?>" 
                                       target="_blank"
                                       onclick="updatePodcastViews('<?php echo $podcast['id']; ?>')"
                                       class="border-2 border-<?php echo $color[2]; ?> text-<?php echo $color[2]; ?> hover:bg-<?php echo $color[2]; ?> hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all flex items-center justify-center">
                                        <i class="fab fa-youtube"></i>
                                    </a>
                                <?php elseif (!empty($podcast['archivo']) && file_exists(($is_direct_access ? __DIR__ . '/../' : '') . 'uploads/podcasts/audio/' . $podcast['archivo'])): ?>
                                    <!-- Fallback para archivos de audio existentes -->
                                    <button onclick="playPodcast(<?php echo $podcast['id']; ?>, '<?php echo $base_path; ?>uploads/podcasts/audio/<?php echo htmlspecialchars($podcast['archivo']); ?>', '<?php echo htmlspecialchars($podcast['titulo_es']); ?>')" 
                                            class="flex-1 bg-gradient-to-r <?php echo $color[0] . ' ' . $color[1]; ?> hover:opacity-90 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl">
                                        <i class="fas fa-play mr-2"></i>Reproducir
                                    </button>
                                    
                                    <a href="<?php echo $base_path; ?>uploads/podcasts/audio/<?php echo htmlspecialchars($podcast['archivo']); ?>" 
                                       download="<?php echo htmlspecialchars($podcast['titulo_es']); ?>.<?php echo pathinfo($podcast['archivo'], PATHINFO_EXTENSION); ?>"
                                       onclick="downloadPodcast(<?php echo $podcast['id']; ?>)"
                                       class="border-2 border-<?php echo $color[2]; ?> text-<?php echo $color[2]; ?> hover:bg-<?php echo $color[2]; ?> hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all flex items-center justify-center">
                                        <i class="fas fa-download"></i>
                                    </a>
                                <?php else: ?>
                                    <div class="flex-1 bg-gray-300 text-gray-500 py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm text-center">
                                        <i class="fas fa-exclamation-triangle mr-2"></i>Video no disponible
                                    </div>
                                <?php endif; ?>
                </div>
            </div>
                    </div>
                <?php endforeach; ?>
                </div>
        <?php endif; ?>
                    </div>
</section>

<!-- Modal para reproductor de video de YouTube -->
<div id="video-modal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-white rounded-2xl shadow-2xl max-w-4xl w-full max-h-[90vh] overflow-hidden">
        <!-- Header del modal -->
        <div class="bg-gradient-to-r from-red-500 to-red-600 text-white px-6 py-4 flex items-center justify-between">
            <h3 id="video-title" class="text-lg font-bold truncate pr-4"></h3>
            <button id="close-video-modal" class="text-white hover:text-gray-200 transition-colors">
                <i class="fas fa-times text-xl"></i>
            </button>
        </div>
        
        <!-- Contenido del video -->
        <div class="p-6">
            <div class="aspect-video bg-gray-900 rounded-lg overflow-hidden relative">
                <iframe id="youtube-player" 
                        src="" 
                        title="YouTube video player" 
                        frameborder="0" 
                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" 
                        allowfullscreen
                        class="w-full h-full"
                        onerror="handleVideoError()">
                </iframe>
                
                <!-- Mensaje cuando el video no está disponible -->
                <div id="video-error-message" class="absolute inset-0 bg-gray-900 flex items-center justify-center text-white text-center p-6 hidden">
                    <div>
                        <i class="fas fa-exclamation-triangle text-4xl text-yellow-500 mb-4"></i>
                        <h3 class="text-xl font-bold mb-2">Video no disponible</h3>
                        <p class="text-gray-300 mb-4">Este video no se puede reproducir en nuestro sitio web.</p>
                        <p class="text-sm text-gray-400 mb-6">Esto puede deberse a restricciones de derechos de autor.</p>
                        <a id="youtube-direct-link" 
                           href="#" 
                           target="_blank"
                           class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-bold transition-colors inline-flex items-center">
                            <i class="fab fa-youtube mr-2"></i>
                            Ver en YouTube
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Botones adicionales -->
            <div class="mt-6 flex justify-center gap-4">
                <a id="open-youtube-link" 
                   href="#" 
                   target="_blank"
                   class="bg-red-500 hover:bg-red-600 text-white px-6 py-3 rounded-xl font-bold transition-colors flex items-center">
                    <i class="fab fa-youtube mr-2"></i>
                    Abrir en YouTube
                </a>
                <button onclick="closeVideoModal()" 
                        class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-xl font-bold transition-colors">
                    Cerrar
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Reproductor de audio flotante -->
<div id="audio-player" class="fixed bottom-4 right-4 bg-white rounded-2xl shadow-2xl hidden z-50 max-w-sm select-none" style="cursor: move;">
    <!-- Header del reproductor (área de arrastre) -->
    <div id="player-header" class="bg-gradient-to-r from-radio-teal to-radio-cyan text-white px-4 py-2 rounded-t-2xl flex items-center justify-between cursor-move">
        <div class="flex items-center gap-2">
            <i class="fas fa-podcast text-sm"></i>
            <span class="text-sm font-bold">Reproductor</span>
        </div>
        <div class="flex items-center gap-2">
            <button id="minimize-player" class="text-white hover:text-gray-200 transition-colors" title="Minimizar">
                <i class="fas fa-minus text-xs"></i>
                        </button>
            <button id="close-player" class="text-white hover:text-gray-200 transition-colors" title="Cerrar">
                <i class="fas fa-times text-xs"></i>
                        </button>
                    </div>
                </div>
    
    <!-- Contenido del reproductor -->
    <div id="player-content" class="p-4">
        <!-- Información de la canción -->
        <div class="mb-3">
            <div id="current-title" class="font-bold text-sm text-gray-800 truncate mb-1"></div>
            <div id="current-time" class="text-xs text-gray-500">0:00 / 0:00</div>
            </div>

        <!-- Barra de progreso -->
        <div class="mb-3">
            <div id="progress-container" class="bg-gray-200 rounded-full h-2 cursor-pointer">
                <div id="progress-bar" class="bg-radio-teal h-2 rounded-full transition-all duration-150" style="width: 0%"></div>
                    </div>
                </div>
        
        <!-- Controles principales -->
        <div class="flex items-center justify-between mb-3">
            <button id="rewind-btn" class="text-gray-600 hover:text-radio-teal transition-colors" title="Retroceder 10s">
                <i class="fas fa-backward text-lg"></i>
            </button>
            
            <button id="play-pause-btn" class="bg-radio-teal hover:bg-radio-cyan text-white w-12 h-12 rounded-full flex items-center justify-center transition-all shadow-lg">
                <i class="fas fa-pause text-lg"></i>
                        </button>
            
            <button id="forward-btn" class="text-gray-600 hover:text-radio-teal transition-colors" title="Avanzar 10s">
                <i class="fas fa-forward text-lg"></i>
                        </button>
                    </div>
        
        <!-- Control de volumen -->
        <div class="flex items-center gap-2">
            <button id="mute-btn" class="text-gray-600 hover:text-radio-teal transition-colors" title="Silenciar">
                <i class="fas fa-volume-up text-sm"></i>
            </button>
            <div class="flex-1">
                <input type="range" id="volume-slider" min="0" max="100" value="100" 
                       class="w-full h-1 bg-gray-200 rounded-lg appearance-none cursor-pointer slider">
            </div>
            <span id="volume-display" class="text-xs text-gray-500 w-8 text-right">100%</span>
        </div>
    </div>
    
    <audio id="audio-element" preload="metadata"></audio>
</div>

<!-- Estilos para el slider de volumen -->
<style>
.slider::-webkit-slider-thumb {
    appearance: none;
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #20B2AA;
    cursor: pointer;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

.slider::-moz-range-thumb {
    height: 16px;
    width: 16px;
    border-radius: 50%;
    background: #20B2AA;
    cursor: pointer;
    border: none;
    box-shadow: 0 2px 4px rgba(0,0,0,0.2);
}

#audio-player.minimized #player-content {
    display: none;
}

#audio-player.minimized {
    max-width: 200px;
}
</style>

<script>
let currentAudio = null;
let currentPodcastId = null;
let isDragging = false;
let dragOffset = { x: 0, y: 0 };

// Función para mostrar el modal de video de YouTube
function showVideoModal(youtubeId, title, podcastId) {
    console.log('Mostrando video:', youtubeId, title, podcastId);
    
    // Registrar en historial
    if (podcastId) {
        fetch('pages/podcasts.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'view_youtube=1&youtube_id=' + youtubeId + '&title=' + encodeURIComponent(title)
        });
    }
    
    // Obtener elementos del modal
    const modal = document.getElementById('video-modal');
    const iframe = document.getElementById('youtube-player');
    const videoTitle = document.getElementById('video-title');
    const openYoutubeLink = document.getElementById('open-youtube-link');
    const youtubeDirectLink = document.getElementById('youtube-direct-link');
    const errorMessage = document.getElementById('video-error-message');
    
    // Configurar el título del video
    videoTitle.textContent = title;
    
    // Configurar URL del iframe (formato embed de YouTube)
    const embedUrl = `https://www.youtube.com/embed/${youtubeId}?autoplay=1&rel=0`;
    iframe.src = embedUrl;
    iframe.style.display = 'block';
    
    // Configurar enlaces directos a YouTube
    const youtubeUrl = `https://www.youtube.com/watch?v=${youtubeId}`;
    openYoutubeLink.href = youtubeUrl;
    youtubeDirectLink.href = youtubeUrl;
    
    // Ocultar mensaje de error
    errorMessage.classList.add('hidden');
    
    // Mostrar el modal
    modal.classList.remove('hidden');
    document.body.style.overflow = 'hidden'; // Prevenir scroll del body
    
    // Actualizar contador de reproducciones solo si es un podcast de la BD
    if (podcastId && !podcastId.toString().startsWith('youtube_')) {
        updatePodcastViews(podcastId);
    }
}

// Función para cerrar el modal de video
function closeVideoModal() {
    const modal = document.getElementById('video-modal');
    const iframe = document.getElementById('youtube-player');
    
    // Pausar el video
    iframe.src = '';
    
    // Ocultar mensaje de error
    document.getElementById('video-error-message').classList.add('hidden');
    
    // Ocultar el modal
    modal.classList.add('hidden');
    document.body.style.overflow = ''; // Restaurar scroll del body
}

// Función para verificar disponibilidad del video
function checkVideoAvailability(youtubeId) {
    const iframe = document.getElementById('youtube-player');
    const errorMessage = document.getElementById('video-error-message');
    
    try {
        // Intentar acceder al contenido del iframe (esto puede fallar si el video está bloqueado)
        iframe.contentWindow.document;
    } catch (e) {
        // Si hay error, probablemente el video está bloqueado
        console.log('Video posiblemente bloqueado:', youtubeId);
        // Mostrar mensaje de error
        iframe.style.display = 'none';
        errorMessage.classList.remove('hidden');
    }
}

// Función para manejar errores del iframe
function handleVideoError() {
    console.log('Error al cargar el video');
    const iframe = document.getElementById('youtube-player');
    const errorMessage = document.getElementById('video-error-message');
    
    iframe.style.display = 'none';
    errorMessage.classList.remove('hidden');
}

// Función para actualizar contador de reproducciones
function updatePodcastViews(podcastId) {
    if (podcastId && !podcastId.toString().startsWith('youtube_')) {
        fetch('<?php echo $is_direct_access ? "podcasts.php" : "pages/podcasts.php"; ?>', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'play_podcast=1&podcast_id=' + podcastId
        }).catch(function(error) {
            console.error('Error al actualizar contador:', error);
        });
    }
}

// Función auxiliar para obtener ID del podcast desde el título (ya no es necesaria)
// function getPodcastIdFromTitle(title) {
//     return null;
// }

function playPodcast(podcastId, audioUrl, title) {
    console.log('Intentando reproducir:', audioUrl);
    
    // Detener audio actual si existe
    if (currentAudio) {
        currentAudio.pause();
    }
    
    // Crear nuevo elemento audio
    currentAudio = document.getElementById('audio-element');
    currentAudio.src = audioUrl;
    currentPodcastId = podcastId;
    
    // Actualizar interfaz del reproductor
    document.getElementById('current-title').textContent = title;
    const player = document.getElementById('audio-player');
    player.classList.remove('hidden', 'minimized');
    
    // Configurar volumen inicial
    currentAudio.volume = document.getElementById('volume-slider').value / 100;
    
    // Event listeners para errores
    currentAudio.addEventListener('error', function(e) {
        console.error('Error de audio:', e);
        alert('Error al cargar el archivo de audio. Verifica que el archivo existe.');
        document.getElementById('audio-player').classList.add('hidden');
    });
    
    currentAudio.addEventListener('loadstart', function() {
        console.log('Iniciando carga del audio...');
    });
    
    currentAudio.addEventListener('canplay', function() {
        console.log('Audio listo para reproducir');
    });
    
    // Reproducir
    currentAudio.play().catch(function(error) {
        console.error('Error al reproducir:', error);
        alert('Error al reproducir el audio: ' + error.message);
    });
    
    // Actualizar contador de reproducciones
    fetch('<?php echo $is_direct_access ? "podcasts.php" : "pages/podcasts.php"; ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'play_podcast=1&podcast_id=' + podcastId
    }).catch(function(error) {
        console.error('Error al actualizar contador:', error);
    });
    
    // Event listeners para el reproductor
    currentAudio.addEventListener('timeupdate', updateProgress);
    currentAudio.addEventListener('ended', function() {
        document.getElementById('play-pause-btn').innerHTML = '<i class="fas fa-play text-lg"></i>';
    });
}

function downloadPodcast(podcastId) {
    // Actualizar contador de descargas
    fetch('<?php echo $is_direct_access ? "podcasts.php" : "pages/podcasts.php"; ?>', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: 'download_podcast=1&podcast_id=' + podcastId
    });
}

function updateProgress() {
    if (currentAudio) {
        const progress = (currentAudio.currentTime / currentAudio.duration) * 100;
        document.getElementById('progress-bar').style.width = progress + '%';
        
        const currentTime = formatTime(currentAudio.currentTime);
        const duration = formatTime(currentAudio.duration);
        document.getElementById('current-time').textContent = currentTime + ' / ' + duration;
    }
}

function formatTime(seconds) {
    if (isNaN(seconds)) return '0:00';
    const mins = Math.floor(seconds / 60);
    const secs = Math.floor(seconds % 60);
    return mins + ':' + (secs < 10 ? '0' : '') + secs;
}

// Inicializar controles del reproductor cuando el DOM esté listo
document.addEventListener('DOMContentLoaded', function() {
    initializePlayer();
});

function initializePlayer() {
    // Control de play/pause
    document.getElementById('play-pause-btn').addEventListener('click', function() {
        if (currentAudio) {
            if (currentAudio.paused) {
                currentAudio.play();
                this.innerHTML = '<i class="fas fa-pause text-lg"></i>';
            } else {
                currentAudio.pause();
                this.innerHTML = '<i class="fas fa-play text-lg"></i>';
            }
        }
    });

    // Cerrar reproductor
    document.getElementById('close-player').addEventListener('click', function() {
        if (currentAudio) {
            currentAudio.pause();
        }
        document.getElementById('audio-player').classList.add('hidden');
    });

    // Minimizar/maximizar reproductor
    document.getElementById('minimize-player').addEventListener('click', function() {
        const player = document.getElementById('audio-player');
        const icon = this.querySelector('i');
        
        if (player.classList.contains('minimized')) {
            player.classList.remove('minimized');
            icon.className = 'fas fa-minus text-xs';
            this.title = 'Minimizar';
        } else {
            player.classList.add('minimized');
            icon.className = 'fas fa-plus text-xs';
            this.title = 'Maximizar';
        }
    });

    // Control de retroceso (10 segundos)
    document.getElementById('rewind-btn').addEventListener('click', function() {
        if (currentAudio) {
            currentAudio.currentTime = Math.max(0, currentAudio.currentTime - 10);
        }
    });

    // Control de avance (10 segundos)
    document.getElementById('forward-btn').addEventListener('click', function() {
        if (currentAudio) {
            currentAudio.currentTime = Math.min(currentAudio.duration, currentAudio.currentTime + 10);
        }
    });

    // Control de volumen
    const volumeSlider = document.getElementById('volume-slider');
    const volumeDisplay = document.getElementById('volume-display');
    const muteBtn = document.getElementById('mute-btn');
    let previousVolume = 100;

    volumeSlider.addEventListener('input', function() {
        const volume = this.value / 100;
        if (currentAudio) {
            currentAudio.volume = volume;
        }
        volumeDisplay.textContent = this.value + '%';
        
        // Actualizar icono de volumen
        const icon = muteBtn.querySelector('i');
        if (this.value == 0) {
            icon.className = 'fas fa-volume-mute text-sm';
        } else if (this.value < 50) {
            icon.className = 'fas fa-volume-down text-sm';
        } else {
            icon.className = 'fas fa-volume-up text-sm';
        }
    });

    // Botón de silenciar
    muteBtn.addEventListener('click', function() {
        const icon = this.querySelector('i');
        
        if (currentAudio && currentAudio.volume > 0) {
            previousVolume = volumeSlider.value;
            currentAudio.volume = 0;
            volumeSlider.value = 0;
            volumeDisplay.textContent = '0%';
            icon.className = 'fas fa-volume-mute text-sm';
        } else {
            currentAudio.volume = previousVolume / 100;
            volumeSlider.value = previousVolume;
            volumeDisplay.textContent = previousVolume + '%';
            
            if (previousVolume < 50) {
                icon.className = 'fas fa-volume-down text-sm';
            } else {
                icon.className = 'fas fa-volume-up text-sm';
            }
        }
    });

    // Click en barra de progreso para saltar
    document.getElementById('progress-container').addEventListener('click', function(e) {
        if (currentAudio && currentAudio.duration) {
            const rect = this.getBoundingClientRect();
            const clickX = e.clientX - rect.left;
            const percentage = clickX / rect.width;
            currentAudio.currentTime = currentAudio.duration * percentage;
        }
    });

    // Event listeners para el modal de video
    document.getElementById('close-video-modal').addEventListener('click', closeVideoModal);
    
    // Cerrar modal al hacer clic fuera del contenido
    document.getElementById('video-modal').addEventListener('click', function(e) {
        if (e.target === this) {
            closeVideoModal();
        }
    });
    
    // Cerrar modal con tecla ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeVideoModal();
        }
    });

    // Funcionalidad de arrastre
    const player = document.getElementById('audio-player');
    const header = document.getElementById('player-header');

    header.addEventListener('mousedown', startDrag);
    document.addEventListener('mousemove', drag);
    document.addEventListener('mouseup', stopDrag);

    // Touch events para móviles
    header.addEventListener('touchstart', startDrag);
    document.addEventListener('touchmove', drag);
    document.addEventListener('touchend', stopDrag);

    function startDrag(e) {
        isDragging = true;
        const rect = player.getBoundingClientRect();
        const clientX = e.clientX || e.touches[0].clientX;
        const clientY = e.clientY || e.touches[0].clientY;
        
        dragOffset.x = clientX - rect.left;
        dragOffset.y = clientY - rect.top;
        
        player.style.transition = 'none';
        e.preventDefault();
    }

    function drag(e) {
        if (!isDragging) return;
        
        const clientX = e.clientX || e.touches[0].clientX;
        const clientY = e.clientY || e.touches[0].clientY;
        
        let newX = clientX - dragOffset.x;
        let newY = clientY - dragOffset.y;
        
        // Limitar a los bordes de la ventana
        const maxX = window.innerWidth - player.offsetWidth;
        const maxY = window.innerHeight - player.offsetHeight;
        
        newX = Math.max(0, Math.min(newX, maxX));
        newY = Math.max(0, Math.min(newY, maxY));
        
        player.style.left = newX + 'px';
        player.style.top = newY + 'px';
        player.style.right = 'auto';
        player.style.bottom = 'auto';
        
        e.preventDefault();
    }

    function stopDrag() {
        if (isDragging) {
            isDragging = false;
            player.style.transition = '';
        }
    }
}

// Búsqueda en tiempo real
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const podcastItems = document.querySelectorAll('.media-item');
            
            podcastItems.forEach(function(item) {
                const title = item.dataset.title.toLowerCase();
                const description = item.dataset.description.toLowerCase();
                
                if (title.includes(searchTerm) || description.includes(searchTerm)) {
                    item.style.display = 'block';
                } else {
                    item.style.display = 'none';
                }
            });
        });
    }
});
</script>

<?php if ($is_direct_access): ?>
</body>
</html>
<?php endif; ?>