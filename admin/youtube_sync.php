<?php
// Configuración de error handling y output buffering
error_reporting(E_ALL);
ini_set('display_errors', 0);
ini_set('log_errors', 1);

// Iniciar output buffering ANTES de cualquier otro código
ob_start();

// Incluir archivos necesarios
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');
require_once('../config/config.php');

// Verificar que sea admin
if (!isset($_SESSION['rol']) || $_SESSION['rol'] !== 'admin') {
    ob_end_clean();
    header('Content-Type: application/json');
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Acceso denegado']);
    exit();
}

// Configuración de YouTube
$apiKey = YOUTUBE_API_KEY;
$channelId = YOUTUBE_CHANNEL_ID;

/**
 * Obtiene los videos de un canal de YouTube
 */
function getYouTubeVideos($apiKey, $channelId, $maxResults = 50) {
    try {
        $videos = [];
        $nextPageToken = null;
        
        do {
            // URL de la API para obtener videos del canal
            $url = "https://www.googleapis.com/youtube/v3/search?key={$apiKey}&channelId={$channelId}&part=snippet,id&order=date&maxResults={$maxResults}";
            
            if ($nextPageToken) {
                $url .= "&pageToken={$nextPageToken}";
            }
            
            // Realizar la solicitud con mejor manejo de errores
            $context = stream_context_create([
                'http' => [
                    'timeout' => 30,
                    'ignore_errors' => true
                ]
            ]);
            
            $response = @file_get_contents($url, false, $context);
            
            if ($response === false) {
                return ['success' => false, 'error' => 'Error al conectar con la API de YouTube. Verifica tu conexión a internet.'];
            }
            
            $data = json_decode($response, true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                return ['success' => false, 'error' => 'Error al procesar respuesta de YouTube API: ' . json_last_error_msg()];
            }
            
            if (isset($data['error'])) {
                return [
                    'success' => false, 
                    'error' => 'Error de API: ' . ($data['error']['message'] ?? 'Desconocido'),
                    'details' => $data['error']
                ];
            }
            
            if (!isset($data['items']) || !is_array($data['items'])) {
                break;
            }
            
            foreach ($data['items'] as $item) {
                // Solo procesar videos (no playlists ni canales)
                if (isset($item['id']['kind']) && $item['id']['kind'] === 'youtube#video') {
                    $thumbnail = '';
                    if (isset($item['snippet']['thumbnails']['high']['url'])) {
                        $thumbnail = $item['snippet']['thumbnails']['high']['url'];
                    } elseif (isset($item['snippet']['thumbnails']['default']['url'])) {
                        $thumbnail = $item['snippet']['thumbnails']['default']['url'];
                    }
                    
                    $videos[] = [
                        'video_id' => $item['id']['videoId'] ?? '',
                        'title' => $item['snippet']['title'] ?? 'Sin título',
                        'description' => $item['snippet']['description'] ?? '',
                        'published_at' => $item['snippet']['publishedAt'] ?? date('Y-m-d'),
                        'thumbnail' => $thumbnail
                    ];
                }
            }
            
            $nextPageToken = $data['nextPageToken'] ?? null;
            
        } while ($nextPageToken && count($videos) < $maxResults);
        
        return ['success' => true, 'videos' => $videos];
        
    } catch (Exception $e) {
        return ['success' => false, 'error' => 'Excepción: ' . $e->getMessage()];
    }
}

/**
 * Obtiene detalles adicionales de los videos (duración, etc.)
 */
function getVideoDetails($apiKey, $videoIds) {
    try {
        if (empty($videoIds) || !is_array($videoIds)) {
            return [];
        }
        
        $videoIdsString = implode(',', $videoIds);
        $url = "https://www.googleapis.com/youtube/v3/videos?key={$apiKey}&id={$videoIdsString}&part=contentDetails,statistics";
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 30,
                'ignore_errors' => true
            ]
        ]);
        
        $response = @file_get_contents($url, false, $context);
        
        if ($response === false) {
            return [];
        }
        
        $data = json_decode($response, true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            return [];
        }
        
        $details = [];
        if (isset($data['items']) && is_array($data['items'])) {
            foreach ($data['items'] as $item) {
                if (isset($item['id']) && isset($item['contentDetails']['duration'])) {
                    $duration = $item['contentDetails']['duration'];
                    $details[$item['id']] = [
                        'duration' => formatYouTubeDuration($duration),
                        'view_count' => $item['statistics']['viewCount'] ?? 0
                    ];
                }
            }
        }
        
        return $details;
        
    } catch (Exception $e) {
        return [];
    }
}

/**
 * Convierte la duración de YouTube (PT1H2M3S) a formato legible (1:02:03)
 */
function formatYouTubeDuration($duration) {
    preg_match('/PT(\d+H)?(\d+M)?(\d+S)?/', $duration, $matches);
    
    $hours = isset($matches[1]) ? rtrim($matches[1], 'H') : 0;
    $minutes = isset($matches[2]) ? rtrim($matches[2], 'M') : 0;
    $seconds = isset($matches[3]) ? rtrim($matches[3], 'S') : 0;
    
    if ($hours > 0) {
        return sprintf('%d:%02d:%02d', $hours, $minutes, $seconds);
    } else {
        return sprintf('%d:%02d', $minutes, $seconds);
    }
}

/**
 * Descarga una imagen desde una URL
 */
function downloadImage($url, $destination) {
    try {
        if (empty($url)) {
            return false;
        }
        
        $context = stream_context_create([
            'http' => [
                'timeout' => 15,
                'ignore_errors' => true
            ]
        ]);
        
        $imageData = @file_get_contents($url, false, $context);
        if ($imageData === false) {
            return false;
        }
        
        $result = file_put_contents($destination, $imageData);
        return $result !== false;
        
    } catch (Exception $e) {
        return false;
    }
}

/**
 * Sincroniza los videos de YouTube con la base de datos
 */
function syncYouTubeVideos($apiKey, $channelId, $con, $userId) {
    $result = getYouTubeVideos($apiKey, $channelId);
    
    if (!$result['success']) {
        return $result;
    }
    
    $videos = $result['videos'];
    
    if (empty($videos)) {
        return ['success' => true, 'message' => 'No se encontraron videos nuevos', 'synced' => 0, 'updated' => 0, 'skipped' => 0];
    }
    
    // Obtener detalles de todos los videos
    $videoIds = array_column($videos, 'video_id');
    $videoDetails = getVideoDetails($apiKey, $videoIds);
    
    $synced = 0;
    $updated = 0;
    $skipped = 0;
    $errors = [];
    
    // Directorio de imágenes
    $imageDir = __DIR__ . '/../uploads/podcasts/images/';
    if (!is_dir($imageDir)) {
        mkdir($imageDir, 0755, true);
    }
    
    foreach ($videos as $video) {
        // Validar datos del video
        if (empty($video['video_id'])) {
            $errors[] = "Video con ID vacío ignorado";
            $skipped++;
            continue;
        }
        
        $videoId = $video['video_id'];
        $title = $video['title'] ?? 'Sin título';
        $description = $video['description'] ?? '';
        $publishedAt = !empty($video['published_at']) ? date('Y-m-d', strtotime($video['published_at'])) : date('Y-m-d');
        $youtubeUrl = 'https://www.youtube.com/watch?v=' . $videoId;
        $thumbnailUrl = $video['thumbnail'] ?? '';
        
        // Obtener duración y estadísticas
        $duration = $videoDetails[$videoId]['duration'] ?? null;
        $viewCount = $videoDetails[$videoId]['view_count'] ?? 0;
        
        try {
            // Verificar si el video ya existe en la base de datos
            $checkStmt = $con->prepare("SELECT id, imagen FROM podcasts WHERE youtube_id = ?");
            $checkStmt->execute([$videoId]);
            $existing = $checkStmt->fetch(PDO::FETCH_ASSOC);
            
            $imageName = null;
            
            if ($existing) {
                // Actualizar podcast existente
                $imageName = $existing['imagen'];
                
                $updateStmt = $con->prepare("
                    UPDATE podcasts SET 
                        titulo_es = ?,
                        titulo_gl = ?,
                        descripcion_es = ?,
                        descripcion_gl = ?,
                        fecha = ?,
                        duracion = ?,
                        youtube_url = ?,
                        reproducciones = ?,
                        activo = 1,
                        ultima_actualizacion = NOW()
                    WHERE youtube_id = ?
                ");
                
                $updateStmt->execute([
                    $title,
                    $title,
                    $description,
                    $description,
                    $publishedAt,
                    $duration,
                    $youtubeUrl,
                    $viewCount,
                    $videoId
                ]);
                
                $updated++;
            } else {
                // Descargar miniatura
                $imageName = uniqid() . '.jpg';
                $imagePath = $imageDir . $imageName;
                
                if (!downloadImage($thumbnailUrl, $imagePath)) {
                    $imageName = null;
                }
                
                // Insertar nuevo podcast
                $insertStmt = $con->prepare("
                    INSERT INTO podcasts (
                        titulo_es, titulo_gl, descripcion_es, descripcion_gl,
                        fecha, duracion, imagen, archivo, youtube_url, youtube_id,
                        activo, destacado, reproducciones, creado_por
                    ) VALUES (?, ?, ?, ?, ?, ?, ?, '', ?, ?, 1, 0, ?, ?)
                ");
                
                $insertStmt->execute([
                    $title,
                    $title,
                    $description,
                    $description,
                    $publishedAt,
                    $duration,
                    $imageName,
                    $youtubeUrl,
                    $videoId,
                    $viewCount,
                    $userId
                ]);
                
                $synced++;
            }
        } catch (Exception $e) {
            $errors[] = "Error procesando video {$videoId}: " . $e->getMessage();
            $skipped++;
        }
    }
    
    return [
        'success' => true,
        'message' => "Sincronización completada",
        'synced' => $synced,
        'updated' => $updated,
        'skipped' => $skipped,
        'total' => count($videos),
        'errors' => $errors
    ];
}

// Procesar solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Limpiar cualquier output previo del buffer
    ob_end_clean();
    
    // Establecer headers JSON
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-cache, must-revalidate');
    
    try {
        // Inicializar respuesta
        $response = ['success' => false, 'message' => 'Error desconocido'];
        
        // Verificar API Key y Channel ID
        if (empty($apiKey) || empty($channelId)) {
            throw new Exception('API Key o Channel ID no configurados');
        }
        
        // Conectar a la base de datos
        $conex = new database();
        $con = $conex->conectar();
        
        // Sincronizar videos
        $result = syncYouTubeVideos($apiKey, $channelId, $con, $_SESSION['id']);
        
        // Preparar respuesta
        $response = $result;
        
    } catch (PDOException $e) {
        $response = [
            'success' => false,
            'error' => 'Error de base de datos: ' . $e->getMessage()
        ];
    } catch (Exception $e) {
        $response = [
            'success' => false,
            'error' => 'Error del servidor: ' . $e->getMessage()
        ];
    }
    
    // Enviar respuesta JSON
    echo json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit();
}

// Si es una solicitud GET, mostrar la interfaz
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sincronización YouTube - RadioRías Admin</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Header -->
    <header class="bg-gradient-to-r from-red-600 to-red-700 shadow-lg">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center space-x-4">
                    <a href="podcasts.php" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <img src="../assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-10 h-10 rounded-full">
                    <div>
                        <h1 class="text-2xl font-black text-white">Sincronización con YouTube</h1>
                        <p class="text-red-100">Importar videos del canal</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="podcasts.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-podcast"></i>
                        <span>Ver Podcasts</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Información del canal -->
        <div class="bg-white rounded-lg shadow-md p-6 mb-6">
            <div class="flex items-center space-x-4 mb-4">
                <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center">
                    <i class="fab fa-youtube text-3xl text-red-600"></i>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-gray-900">Canal de YouTube</h2>
                    <p class="text-gray-600">ID: <?php echo htmlspecialchars($channelId); ?></p>
                </div>
            </div>
            
            <div class="border-t border-gray-200 pt-4">
                <p class="text-gray-700 mb-4">
                    <i class="fas fa-info-circle text-blue-500 mr-2"></i>
                    Esta herramienta sincroniza automáticamente los videos de tu canal de YouTube con la base de datos de podcasts.
                </p>
                <ul class="space-y-2 text-sm text-gray-600">
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Se importarán todos los videos públicos del canal</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Los videos existentes se actualizarán con la información más reciente</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Las miniaturas se descargarán automáticamente</li>
                    <li><i class="fas fa-check text-green-500 mr-2"></i>Se sincronizarán títulos, descripciones, fechas y duraciones</li>
                </ul>
            </div>
        </div>

        <!-- Botón de sincronización -->
        <div class="bg-white rounded-lg shadow-md p-8 text-center">
            <button id="sync-button" onclick="startSync()" class="bg-red-600 hover:bg-red-700 text-white px-8 py-4 rounded-lg font-bold text-lg transition-all duration-300 transform hover:scale-105 shadow-lg disabled:opacity-50 disabled:cursor-not-allowed">
                <i class="fab fa-youtube mr-2"></i>
                Sincronizar Ahora
            </button>
            
            <p class="text-gray-500 text-sm mt-4">
                Este proceso puede tardar varios minutos dependiendo de la cantidad de videos
            </p>
        </div>

        <!-- Área de resultados -->
        <div id="results-container" class="mt-6 hidden">
            <!-- Loader -->
            <div id="loader" class="bg-white rounded-lg shadow-md p-8 text-center">
                <div class="inline-block animate-spin rounded-full h-12 w-12 border-b-2 border-red-600 mb-4"></div>
                <p class="text-gray-700 font-medium">Sincronizando videos...</p>
                <p class="text-gray-500 text-sm mt-2">Por favor espera, esto puede tomar un momento</p>
            </div>

            <!-- Resultados -->
            <div id="results" class="hidden bg-white rounded-lg shadow-md p-6">
                <div class="text-center mb-6">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <i class="fas fa-check text-3xl text-green-600"></i>
                    </div>
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">Sincronización Completada</h3>
                    <p id="result-message" class="text-gray-600"></p>
                </div>

                <!-- Estadísticas -->
                <div class="grid grid-cols-3 gap-4 mb-6">
                    <div class="bg-green-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-green-600" id="synced-count">0</p>
                        <p class="text-sm text-gray-600">Nuevos</p>
                    </div>
                    <div class="bg-blue-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-blue-600" id="updated-count">0</p>
                        <p class="text-sm text-gray-600">Actualizados</p>
                    </div>
                    <div class="bg-gray-50 rounded-lg p-4 text-center">
                        <p class="text-3xl font-bold text-gray-600" id="total-count">0</p>
                        <p class="text-sm text-gray-600">Total Procesados</p>
                    </div>
                </div>

                <!-- Errores (si hay) -->
                <div id="errors-container" class="hidden">
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <h4 class="text-red-800 font-bold mb-2">
                            <i class="fas fa-exclamation-triangle mr-2"></i>Errores encontrados:
                        </h4>
                        <ul id="errors-list" class="text-sm text-red-700 space-y-1 pl-6"></ul>
                    </div>
                </div>

                <!-- Botones de acción -->
                <div class="flex justify-center gap-4 mt-6">
                    <a href="podcasts.php" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold transition-colors">
                        <i class="fas fa-podcast mr-2"></i>Ver Podcasts
                    </a>
                    <button onclick="location.reload()" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg font-bold transition-colors">
                        <i class="fas fa-sync mr-2"></i>Sincronizar de Nuevo
                    </button>
                </div>
            </div>
        </div>
    </main>

    <script>
        async function startSync() {
            const button = document.getElementById('sync-button');
            const resultsContainer = document.getElementById('results-container');
            const loader = document.getElementById('loader');
            const results = document.getElementById('results');
            
            // Deshabilitar botón
            button.disabled = true;
            
            // Mostrar loader
            resultsContainer.classList.remove('hidden');
            loader.classList.remove('hidden');
            results.classList.add('hidden');
            
            try {
                const response = await fetch('youtube_sync.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json'
                    }
                });
                
                const data = await response.json();
                
                // Ocultar loader
                loader.classList.add('hidden');
                
                if (data.success) {
                    // Mostrar resultados
                    results.classList.remove('hidden');
                    
                    document.getElementById('result-message').textContent = data.message;
                    document.getElementById('synced-count').textContent = data.synced;
                    document.getElementById('updated-count').textContent = data.updated;
                    document.getElementById('total-count').textContent = data.total;
                    
                    // Mostrar errores si hay
                    if (data.errors && data.errors.length > 0) {
                        const errorsContainer = document.getElementById('errors-container');
                        const errorsList = document.getElementById('errors-list');
                        
                        errorsContainer.classList.remove('hidden');
                        errorsList.innerHTML = '';
                        
                        data.errors.forEach(error => {
                            const li = document.createElement('li');
                            li.textContent = error;
                            errorsList.appendChild(li);
                        });
                    }
                } else {
                    // Mostrar error
                    results.classList.remove('hidden');
                    results.innerHTML = `
                        <div class="text-center">
                            <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <i class="fas fa-times text-3xl text-red-600"></i>
                            </div>
                            <h3 class="text-2xl font-bold text-gray-900 mb-2">Error en la Sincronización</h3>
                            <p class="text-red-600 mb-6">${data.error || 'Error desconocido'}</p>
                            ${data.details ? `<div class="bg-red-50 border border-red-200 rounded-lg p-4 text-left mb-6">
                                <p class="text-sm text-red-700"><strong>Detalles:</strong></p>
                                <pre class="text-xs text-red-600 mt-2 overflow-auto">${JSON.stringify(data.details, null, 2)}</pre>
                            </div>` : ''}
                            <button onclick="location.reload()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold transition-colors">
                                <i class="fas fa-sync mr-2"></i>Intentar de Nuevo
                            </button>
                        </div>
                    `;
                }
            } catch (error) {
                // Ocultar loader
                loader.classList.add('hidden');
                
                // Mostrar error
                results.classList.remove('hidden');
                results.innerHTML = `
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-times text-3xl text-red-600"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-2">Error de Conexión</h3>
                        <p class="text-red-600 mb-6">No se pudo conectar con el servidor: ${error.message}</p>
                        <button onclick="location.reload()" class="bg-red-600 hover:bg-red-700 text-white px-6 py-3 rounded-lg font-bold transition-colors">
                            <i class="fas fa-sync mr-2"></i>Intentar de Nuevo
                        </button>
                    </div>
                `;
            }
            
            // Habilitar botón
            button.disabled = false;
        }
    </script>
</body>
</html>

