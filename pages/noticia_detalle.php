<?php
// Detectar si se está accediendo directamente o a través de index.php
$is_direct_access = basename($_SERVER['PHP_SELF']) === 'noticia_detalle.php';
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

// Obtener ID de la noticia
$noticia_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if (!$noticia_id) {
    header('Location: ' . ($is_direct_access ? '../' : '') . 'pages/noticias.php');
    exit();
}

// Obtener la noticia
$sql = "SELECT n.*, 
               CASE WHEN cn.nombre_es IS NOT NULL THEN cn.nombre_es ELSE 'General' END as categoria_nombre,
               CASE WHEN cn.color IS NOT NULL THEN cn.color ELSE '#20B2AA' END as categoria_color,
               CASE WHEN cn.icono IS NOT NULL THEN cn.icono ELSE 'fas fa-newspaper' END as categoria_icono
        FROM noticias n 
        LEFT JOIN categorias_noticia cn ON n.categoria_id = cn.id 
        WHERE n.id = ? AND n.activo = 1 AND n.fecha_publicacion <= NOW()";

$stmt = $con->prepare($sql);
$stmt->execute([$noticia_id]);
$noticia = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$noticia) {
    header('Location: ' . ($is_direct_access ? '../' : '') . 'pages/noticias.php');
    exit();
}

// Incrementar contador de vistas
$update_views_sql = "UPDATE noticias SET vistas = vistas + 1 WHERE id = ?";
$update_stmt = $con->prepare($update_views_sql);
$update_stmt->execute([$noticia_id]);

// Obtener noticias relacionadas (misma categoría)
$related_sql = "SELECT n.*, 
                       CASE WHEN cn.nombre_es IS NOT NULL THEN cn.nombre_es ELSE 'General' END as categoria_nombre,
                       CASE WHEN cn.color IS NOT NULL THEN cn.color ELSE '#20B2AA' END as categoria_color
                FROM noticias n 
                LEFT JOIN categorias_noticia cn ON n.categoria_id = cn.id 
                WHERE n.categoria_id = ? AND n.id != ? AND n.activo = 1 AND n.fecha_publicacion <= NOW()
                ORDER BY n.fecha_publicacion DESC 
                LIMIT 3";

$related_stmt = $con->prepare($related_sql);
$related_stmt->execute([$noticia['categoria_id'], $noticia_id]);
$noticias_relacionadas = $related_stmt->fetchAll(PDO::FETCH_ASSOC);

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

$fecha_formateada = formatDate($noticia['fecha_publicacion'], $lang);
?>
<?php if ($is_direct_access): ?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($noticia['titulo_es']); ?> - Radio Morrazo</title>
    <meta name="description" content="<?php echo htmlspecialchars($noticia['meta_descripcion_es'] ?? substr(strip_tags($noticia['contenido_es']), 0, 160)); ?>">
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

<!-- Breadcrumb -->
<section class="py-4 bg-gray-50 border-b">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <nav class="flex items-center space-x-2 text-sm">
            <a href="<?php echo $base_path; ?>pages/noticias.php" class="text-radio-teal hover:text-radio-cyan transition-colors">
                <i class="fas fa-home mr-1"></i>Noticias
            </a>
            <i class="fas fa-chevron-right text-gray-400"></i>
            <span class="text-gray-600"><?php echo htmlspecialchars($noticia['titulo_es']); ?></span>
        </nav>
    </div>
</section>

<!-- Noticia Principal -->
<section class="py-8 sm:py-12 lg:py-16">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header de la noticia -->
        <div class="mb-8 sm:mb-12">
            <!-- Categoría -->
            <div class="mb-4">
                <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-bold text-white shadow-lg"
                      style="background-color: <?php echo $noticia['categoria_color']; ?>;">
                    <i class="<?php echo $noticia['categoria_icono']; ?> mr-2"></i>
                    <?php echo htmlspecialchars($noticia['categoria_nombre']); ?>
                </span>
            </div>
            
            <!-- Título -->
            <h1 class="text-3xl sm:text-4xl lg:text-5xl font-black text-gray-900 mb-4 sm:mb-6 leading-tight">
                <?php echo htmlspecialchars($noticia['titulo_es']); ?>
            </h1>
            
            <!-- Subtítulo -->
            <?php if ($noticia['subtitulo_es']): ?>
                <h2 class="text-lg sm:text-xl lg:text-2xl text-gray-600 mb-6 font-medium">
                    <?php echo htmlspecialchars($noticia['subtitulo_es']); ?>
                </h2>
            <?php endif; ?>
            
            <!-- Meta información -->
            <div class="flex flex-wrap items-center gap-4 sm:gap-6 text-sm text-gray-500 mb-6">
                <div class="flex items-center">
                    <i class="fas fa-calendar-alt mr-2"></i>
                    <?php echo $fecha_formateada; ?>
                </div>
                <?php if ($noticia['autor']): ?>
                    <div class="flex items-center">
                        <i class="fas fa-user mr-2"></i>
                        <?php echo htmlspecialchars($noticia['autor']); ?>
                    </div>
                <?php endif; ?>
                <div class="flex items-center">
                    <i class="fas fa-eye mr-2"></i>
                    <?php echo number_format($noticia['vistas']); ?> vistas
                </div>
                <?php if ($noticia['destacado']): ?>
                    <div class="flex items-center text-yellow-600">
                        <i class="fas fa-star mr-2"></i>
                        Destacado
                    </div>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- Imagen principal -->
        <?php if ($noticia['imagen'] && file_exists(($is_direct_access ? __DIR__ . '/../' : '') . 'uploads/noticias/images/' . $noticia['imagen'])): ?>
            <div class="mb-8 sm:mb-12">
                <img src="<?php echo $base_path; ?>uploads/noticias/images/<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                     alt="<?php echo htmlspecialchars($noticia['imagen_alt_es'] ?? $noticia['titulo_es']); ?>"
                     class="w-full h-64 sm:h-80 lg:h-96 object-cover rounded-2xl shadow-2xl">
            </div>
        <?php endif; ?>
        
        <!-- Contenido -->
        <div class="prose prose-lg max-w-none mb-8 sm:mb-12">
            <div class="text-gray-800 leading-relaxed text-base sm:text-lg">
                <?php echo nl2br(htmlspecialchars($noticia['contenido_es'])); ?>
            </div>
        </div>
        
        <!-- Compartir -->
        <div class="bg-gray-50 rounded-2xl p-6 mb-8 sm:mb-12">
            <h3 class="text-lg font-bold text-gray-900 mb-4">Compartir esta noticia</h3>
            <div class="flex flex-wrap gap-3">
                <button onclick="shareOnFacebook()" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-bold transition-colors flex items-center">
                    <i class="fab fa-facebook-f mr-2"></i>Facebook
                </button>
                <button onclick="shareOnTwitter()" class="bg-blue-400 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-bold transition-colors flex items-center">
                    <i class="fab fa-twitter mr-2"></i>Twitter
                </button>
                <button onclick="shareOnWhatsApp()" class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg font-bold transition-colors flex items-center">
                    <i class="fab fa-whatsapp mr-2"></i>WhatsApp
                </button>
                <button onclick="copyLink()" class="bg-gray-600 hover:bg-gray-700 text-white px-4 py-2 rounded-lg font-bold transition-colors flex items-center">
                    <i class="fas fa-link mr-2"></i>Copiar Enlace
                </button>
            </div>
        </div>
    </div>
</section>

<!-- Noticias Relacionadas -->
<?php if (!empty($noticias_relacionadas)): ?>
<section class="py-8 sm:py-12 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-8 sm:mb-12">
            <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-gray-900 mb-4">
                Noticias Relacionadas
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto"></div>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <?php foreach ($noticias_relacionadas as $relacionada): ?>
                <article class="bg-white rounded-2xl overflow-hidden shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                    <!-- Imagen -->
                    <div class="h-48 bg-gradient-to-br from-radio-teal to-radio-cyan flex items-center justify-center relative overflow-hidden">
                        <?php if ($relacionada['imagen'] && file_exists(($is_direct_access ? __DIR__ . '/../' : '') . 'uploads/noticias/images/' . $relacionada['imagen'])): ?>
                            <img src="<?php echo $base_path; ?>uploads/noticias/images/<?php echo htmlspecialchars($relacionada['imagen']); ?>" 
                                 alt="<?php echo htmlspecialchars($relacionada['titulo_es']); ?>"
                                 class="w-full h-full object-cover">
                        <?php else: ?>
                            <div class="w-16 h-16 bg-white rounded-full flex items-center justify-center shadow-lg">
                                <i class="fas fa-newspaper text-2xl text-radio-teal"></i>
                            </div>
                        <?php endif; ?>
                    </div>
                    
                    <!-- Contenido -->
                    <div class="p-6">
                        <div class="mb-3">
                            <span class="text-xs font-bold px-3 py-1 rounded-full text-white"
                                  style="background-color: <?php echo $relacionada['categoria_color']; ?>;">
                                <?php echo htmlspecialchars($relacionada['categoria_nombre']); ?>
                            </span>
                        </div>
                        
                        <h3 class="text-lg font-bold text-gray-900 mb-2 line-clamp-2">
                            <?php echo htmlspecialchars($relacionada['titulo_es']); ?>
                        </h3>
                        
                        <p class="text-gray-600 text-sm mb-4 line-clamp-3">
                            <?php echo htmlspecialchars(substr($relacionada['resumen_es'] ?? $relacionada['contenido_es'], 0, 100)) . '...'; ?>
                        </p>
                        
                        <div class="flex items-center justify-between text-xs text-gray-500 mb-4">
                            <span><?php echo formatDate($relacionada['fecha_publicacion'], $lang); ?></span>
                            <span><i class="fas fa-eye mr-1"></i><?php echo number_format($relacionada['vistas']); ?></span>
                        </div>
                        
                        <a href="<?php echo $base_path; ?>pages/noticia_detalle.php?id=<?php echo $relacionada['id']; ?>" 
                           class="block w-full bg-radio-teal hover:bg-radio-cyan text-white py-2 px-4 rounded-lg font-bold text-center transition-colors">
                            Leer Más
                        </a>
                    </div>
                </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?php endif; ?>

<!-- Botón volver -->
<section class="py-8 bg-white">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <a href="<?php echo $base_path; ?>pages/noticias.php" 
           class="inline-flex items-center bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-xl font-bold transition-colors">
            <i class="fas fa-arrow-left mr-2"></i>
            Volver a Noticias
        </a>
    </div>
</section>

<script>
// Funciones para compartir
function shareOnFacebook() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://www.facebook.com/sharer/sharer.php?u=${url}`, '_blank', 'width=600,height=400');
}

function shareOnTwitter() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://twitter.com/intent/tweet?url=${url}&text=${title}`, '_blank', 'width=600,height=400');
}

function shareOnWhatsApp() {
    const url = encodeURIComponent(window.location.href);
    const title = encodeURIComponent(document.title);
    window.open(`https://wa.me/?text=${title}%20${url}`, '_blank');
}

function copyLink() {
    navigator.clipboard.writeText(window.location.href).then(function() {
        alert('Enlace copiado al portapapeles');
    });
}
</script>

<style>
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
</style>

<?php if ($is_direct_access): ?>
</body>
</html>
<?php endif; ?>
