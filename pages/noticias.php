<?php
// Detectar si se está accediendo directamente o a través de index.php
$is_direct_access = basename($_SERVER['PHP_SELF']) === 'noticias.php';
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
    // Usar el idioma de la sesión global
    $lang = isset($GLOBALS['lang']) ? $GLOBALS['lang'] : 'es';
}

$conex = new database();
$con = $conex->conectar();

// Obtener categorías de noticias
$categorias_sql = "SELECT * FROM categorias_noticia WHERE activo = 1 ORDER BY orden, nombre_es";
$categorias = $con->query($categorias_sql)->fetchAll(PDO::FETCH_ASSOC);

// Filtros
$categoria_filter = isset($_GET['categoria']) ? (int)$_GET['categoria'] : 0;
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$page_num = isset($_GET['p']) ? (int)$_GET['p'] : 1; // Cambiar 'page' por 'p' para evitar conflicto con routing
$limit = 6; // Noticias por página
$offset = ($page_num - 1) * $limit;

// Construir consulta con filtros
$where_conditions = ["n.activo = 1", "n.fecha_publicacion <= NOW()"];
$params = [];
$param_index = 1;

if ($categoria_filter > 0) {
    $where_conditions[] = "n.categoria_id = :cat_filter";
    $params[':cat_filter'] = $categoria_filter;
}

if (!empty($search)) {
    $where_conditions[] = "(n.titulo_es LIKE :search OR n.titulo_gl LIKE :search OR n.contenido_es LIKE :search OR n.resumen_es LIKE :search)";
    $params[':search'] = "%$search%";
}

$where_clause = "WHERE " . implode(" AND ", $where_conditions);

// Contar total de noticias
$count_sql = "SELECT COUNT(*) FROM noticias n $where_clause";
$count_stmt = $con->prepare($count_sql);
foreach ($params as $key => $value) {
    $count_stmt->bindValue($key, $value);
}
$count_stmt->execute();
$total_noticias = $count_stmt->fetchColumn();
$total_pages = ceil($total_noticias / $limit);

// Obtener noticias
$sql = "SELECT n.*, 
               CASE WHEN cn.nombre_es IS NOT NULL THEN cn.nombre_es ELSE 'General' END as categoria_nombre,
               CASE WHEN cn.color IS NOT NULL THEN cn.color ELSE '#20B2AA' END as categoria_color,
               CASE WHEN cn.icono IS NOT NULL THEN cn.icono ELSE 'fas fa-newspaper' END as categoria_icono
        FROM noticias n 
        LEFT JOIN categorias_noticia cn ON n.categoria_id = cn.id 
        $where_clause 
        ORDER BY n.destacado DESC, n.fecha_publicacion DESC 
        LIMIT :limit OFFSET :offset";

$stmt = $con->prepare($sql);
// Bind all parameters
foreach ($params as $key => $value) {
    $stmt->bindValue($key, $value);
}
$stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
$stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
$stmt->execute();
$noticias = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Función para generar slug
function generateSlug($text) {
    $text = strtolower($text);
    $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
    $text = preg_replace('/[\s-]+/', '-', $text);
    return trim($text, '-');
}

// Función para formatear fecha
function formatDate($date, $lang = 'es') {
    $timestamp = strtotime($date);
    if ($lang === 'gl') {
        $months = [
            1 => 'xaneiro', 2 => 'febreiro', 3 => 'marzo', 4 => 'abril',
            5 => 'maio', 6 => 'xuño', 7 => 'xullo', 8 => 'agosto',
            9 => 'setembro', 10 => 'outubro', 11 => 'novembro', 12 => 'decembro'
        ];
        return date('j', $timestamp) . ' de ' . $months[date('n', $timestamp)] . ' de ' . date('Y', $timestamp);
    } else {
        $months = [
            1 => 'enero', 2 => 'febrero', 3 => 'marzo', 4 => 'abril',
            5 => 'mayo', 6 => 'junio', 7 => 'julio', 8 => 'agosto',
            9 => 'septiembre', 10 => 'octubre', 11 => 'noviembre', 12 => 'diciembre'
        ];
        return date('j', $timestamp) . ' de ' . $months[date('n', $timestamp)] . ' de ' . date('Y', $timestamp);
    }
}
?>
<?php if ($is_direct_access): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Noticias - Radio Morrazo</title>
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

<!-- Noticias Header -->
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
            <span class="text-white drop-shadow-2xl">NOTICIAS</span>
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            Mantente informado con las últimas novedades
        </p>
        
        <!-- Búsqueda -->
        <form method="GET" class="flex justify-center mb-12 sm:mb-16 px-4">
            <div class="bg-white bg-opacity-20 backdrop-blur-lg p-2 rounded-2xl shadow-2xl flex gap-2">
                <input type="text" name="search" value="<?php echo htmlspecialchars($search); ?>" 
                       placeholder="Buscar noticias..." 
                       class="px-4 sm:px-6 py-2 sm:py-3 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-300 w-80 sm:w-96 text-slate-800 placeholder-slate-500">
                <input type="hidden" name="page" value="noticias">
                <input type="hidden" name="lang" value="<?php echo $lang; ?>">
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
                <a href="?page=noticias&lang=<?php echo $lang; ?>&search=<?php echo urlencode($search); ?>" 
                   class="category-btn <?php echo $categoria_filter === 0 ? 'bg-radio-teal text-white' : 'bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white'; ?> px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                    Todas las categorías
                </a>
                <?php foreach ($categorias as $categoria): ?>
                    <a href="?page=noticias&lang=<?php echo $lang; ?>&categoria=<?php echo $categoria['id']; ?>&search=<?php echo urlencode($search); ?>" 
                       class="category-btn <?php echo $categoria_filter === (int)$categoria['id'] ? 'bg-white text-white' : 'bg-white text-slate-700 border-2 border-gray-300 hover:border-opacity-50'; ?> px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105"
                       style="<?php echo $categoria_filter === (int)$categoria['id'] ? 'background-color: ' . $categoria['color'] . '; border-color: ' . $categoria['color'] . ';' : ''; ?>">
                        <i class="<?php echo $categoria['icono']; ?> mr-2"></i>
                        <?php echo htmlspecialchars($categoria['nombre_es']); ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>

<!-- Noticias Grid -->
<section id="noticias" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-white via-slate-50 to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo empty($noticias) ? 'No se encontraron noticias' : (count($noticias) . ' Noticias Disponibles'); ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
        </div>
        
        <?php if (empty($noticias)): ?>
            <div class="text-center py-16">
                <i class="fas fa-newspaper text-6xl text-gray-400 mb-6"></i>
                <h3 class="text-2xl font-bold text-gray-600 mb-4">No hay noticias disponibles</h3>
                <p class="text-gray-500 mb-8">Prueba con otros filtros de búsqueda o categorías.</p>
                <a href="noticias.php" class="bg-radio-teal hover:bg-radio-cyan text-white px-6 py-3 rounded-xl font-bold transition-colors">
                    Ver todas las noticias
                </a>
            </div>
        <?php else: ?>
            <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8" id="noticias-grid">
                <?php 
                $colors = [
                    ['from-radio-teal', 'to-radio-cyan', 'radio-teal'],
                    ['from-purple-600', 'to-pink-600', 'purple-600'],
                    ['from-emerald-500', 'to-emerald-600', 'emerald-600'],
                    ['from-orange-500', 'to-orange-600', 'orange-600'],
                    ['from-pink-500', 'to-pink-600', 'pink-600'],
                    ['from-blue-500', 'to-blue-600', 'blue-600']
                ];
                
                foreach ($noticias as $index => $noticia): 
                    $color = $colors[$index % count($colors)];
                    $fecha_formateada = formatDate($noticia['fecha_publicacion'], $lang);
                ?>
                    <article class="news-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" 
                             data-category="<?php echo strtolower($noticia['categoria_nombre']); ?>"
                             data-title="<?php echo htmlspecialchars($noticia['titulo_es']); ?>"
                             data-content="<?php echo htmlspecialchars($noticia['contenido_es']); ?>">
                        
                        <!-- Imagen de la noticia -->
                        <div class="h-48 sm:h-64 bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> flex items-center justify-center relative overflow-hidden">
                            <?php if ($noticia['imagen'] && file_exists(($is_direct_access ? __DIR__ . '/../' : '') . 'uploads/noticias/images/' . $noticia['imagen'])): ?>
                                <img src="<?php echo $base_path; ?>uploads/noticias/images/<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                     alt="<?php echo htmlspecialchars($noticia['imagen_alt_es'] ?? $noticia['titulo_es']); ?>"
                                     class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-300">
                            <?php else: ?>
                                <div class="w-full h-full bg-gradient-to-br <?php echo $color[0] . ' ' . $color[1]; ?> flex items-center justify-center relative">
                                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                                        <i class="<?php echo $noticia['categoria_icono']; ?> text-3xl sm:text-4xl text-<?php echo $color[2]; ?>"></i>
                                    </div>
                                    <!-- Ondas de noticias animadas -->
                                    <div class="absolute inset-0 flex items-center justify-center">
                                        <div class="w-32 h-32 border-2 border-white border-opacity-30 rounded-full animate-ping"></div>
                                        <div class="absolute w-40 h-40 border-2 border-white border-opacity-20 rounded-full animate-pulse"></div>
                                    </div>
                                </div>
                            <?php endif; ?>
                            
                            <!-- Badges -->
                            <div class="absolute top-4 left-4 right-4 flex justify-between">
                                <?php if ($noticia['destacado']): ?>
                                    <div class="bg-yellow-500 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                                        <i class="fas fa-star mr-1"></i>DESTACADO
                                    </div>
                                <?php else: ?>
                                    <div></div>
                                <?php endif; ?>
                                
                                <?php 
                                $fecha_noticia = new DateTime($noticia['fecha_publicacion']);
                                $fecha_actual = new DateTime();
                                $diferencia = $fecha_actual->diff($fecha_noticia);
                                if ($diferencia->days <= 3): 
                                ?>
                                    <div class="bg-red-500 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                                        NUEVO
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>

                        <!-- Contenido de la noticia -->
                        <div class="p-6 sm:p-8">
                            <div class="flex items-center justify-between mb-3">
                                <span class="text-xs sm:text-sm font-bold px-3 py-1 rounded-full text-white shadow-lg"
                                      style="background-color: <?php echo $noticia['categoria_color']; ?>;">
                                    <i class="<?php echo $noticia['categoria_icono']; ?> mr-1"></i>
                                    <?php echo strtoupper(htmlspecialchars($noticia['categoria_nombre'])); ?>
                                </span>
                                <span class="text-xs text-gray-500">
                                    <?php echo $fecha_formateada; ?>
                                </span>
                            </div>

                            <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3">
                                <?php echo htmlspecialchars($noticia['titulo_es']); ?>
                            </h3>
                            
                            <?php if ($noticia['subtitulo_es']): ?>
                                <p class="text-slate-600 mb-3 text-sm sm:text-base font-medium">
                                    <?php echo htmlspecialchars($noticia['subtitulo_es']); ?>
                                </p>
                            <?php endif; ?>
                            
                            <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">
                                <?php echo htmlspecialchars(substr($noticia['resumen_es'] ?? $noticia['contenido_es'], 0, 120)) . '...'; ?>
                            </p>
                            
                            <!-- Información adicional -->
                            <div class="flex items-center justify-between mb-4">
                                <div class="flex items-center gap-3">
                                    <?php if ($noticia['autor']): ?>
                                        <span class="text-xs text-gray-500">
                                            <i class="fas fa-user mr-1"></i><?php echo htmlspecialchars($noticia['autor']); ?>
                                        </span>
                                    <?php endif; ?>
                                </div>
                                <div class="text-xs text-gray-500">
                                    <i class="fas fa-eye mr-1"></i><?php echo number_format($noticia['vistas']); ?> vistas
                                </div>
                            </div>
                            
                            <!-- Botón de leer más -->
                            <div class="flex gap-2">
                                <?php if ($is_direct_access): ?>
                                    <a href="<?php echo $base_path; ?>pages/noticia_detalle.php?id=<?php echo $noticia['id']; ?>" 
                                       class="flex-1 bg-gradient-to-r <?php echo $color[0] . ' ' . $color[1]; ?> hover:opacity-90 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl text-center">
                                        <i class="fas fa-readme mr-2"></i>Leer Más
                                    </a>
                                <?php else: ?>
                                    <a href="?page=noticia_detalle&id=<?php echo $noticia['id']; ?>&lang=<?php echo $lang; ?>" 
                                       class="flex-1 bg-gradient-to-r <?php echo $color[0] . ' ' . $color[1]; ?> hover:opacity-90 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl text-center">
                                        <i class="fas fa-readme mr-2"></i>Leer Más
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
            
            <!-- Paginación -->
            <?php if ($total_pages > 1): ?>
                <div class="mt-12 sm:mt-16 flex justify-center">
                    <nav class="flex items-center space-x-2">
                        <?php if ($page_num > 1): ?>
                            <a href="?page=noticias&lang=<?php echo $lang; ?>&p=<?php echo $page_num - 1; ?>&categoria=<?php echo $categoria_filter; ?>&search=<?php echo urlencode($search); ?>" 
                               class="px-4 py-2 bg-white text-radio-teal border-2 border-radio-teal rounded-xl font-bold hover:bg-radio-teal hover:text-white transition-all">
                                <i class="fas fa-chevron-left mr-1"></i>Anterior
                            </a>
                        <?php endif; ?>
                        
                        <?php for ($i = max(1, $page_num - 2); $i <= min($total_pages, $page_num + 2); $i++): ?>
                            <a href="?page=noticias&lang=<?php echo $lang; ?>&p=<?php echo $i; ?>&categoria=<?php echo $categoria_filter; ?>&search=<?php echo urlencode($search); ?>" 
                               class="px-4 py-2 rounded-xl font-bold transition-all <?php echo $i === $page_num ? 'bg-radio-teal text-white' : 'bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white'; ?>">
                                <?php echo $i; ?>
                            </a>
                        <?php endfor; ?>
                        
                        <?php if ($page_num < $total_pages): ?>
                            <a href="?page=noticias&lang=<?php echo $lang; ?>&p=<?php echo $page_num + 1; ?>&categoria=<?php echo $categoria_filter; ?>&search=<?php echo urlencode($search); ?>" 
                               class="px-4 py-2 bg-white text-radio-teal border-2 border-radio-teal rounded-xl font-bold hover:bg-radio-teal hover:text-white transition-all">
                                Siguiente<i class="fas fa-chevron-right ml-1"></i>
                            </a>
                        <?php endif; ?>
                    </nav>
                </div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</section>

<!-- Búsqueda en tiempo real -->
<script>
document.addEventListener('DOMContentLoaded', function() {
    const searchInput = document.querySelector('input[name="search"]');
    if (searchInput) {
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const newsItems = document.querySelectorAll('.news-item');
            
            newsItems.forEach(function(item) {
                const title = item.dataset.title.toLowerCase();
                const content = item.dataset.content.toLowerCase();
                
                if (title.includes(searchTerm) || content.includes(searchTerm)) {
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
