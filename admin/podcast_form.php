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

// Crear directorios si no existen
$upload_dirs = [
    __DIR__ . '/../uploads/podcasts/images',
    __DIR__ . '/../uploads/podcasts/audio'
];

foreach ($upload_dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

$editing = false;
$podcast = null;
$errors = [];
$success = '';

// Si estamos editando, cargar datos del podcast
if (isset($_GET['id'])) {
    $editing = true;
    $id = (int)$_GET['id'];
    $stmt = $con->prepare("SELECT * FROM podcasts WHERE id = ?");
    $stmt->execute([$id]);
    $podcast = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$podcast) {
        header("Location: podcasts.php");
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_es = trim($_POST['titulo_es']);
    $titulo_gl = trim($_POST['titulo_gl']);
    $descripcion_es = trim($_POST['descripcion_es']);
    $descripcion_gl = trim($_POST['descripcion_gl']);
    $fecha = $_POST['fecha'];
    $duracion = trim($_POST['duracion']);
    $programa_id = !empty($_POST['programa_id']) ? (int)$_POST['programa_id'] : null;
    $categoria_id = !empty($_POST['categoria_id']) ? (int)$_POST['categoria_id'] : null;
    $activo = isset($_POST['activo']) ? 1 : 0;
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    
    // Validaciones
    if (empty($titulo_es)) $errors[] = "El título en español es obligatorio";
    if (empty($titulo_gl)) $errors[] = "El título en gallego es obligatorio";
    if (empty($descripcion_es)) $errors[] = "La descripción en español es obligatoria";
    if (empty($descripcion_gl)) $errors[] = "La descripción en gallego es obligatoria";
    if (empty($fecha)) $errors[] = "La fecha es obligatoria";
    
    // Validar URL de YouTube (obligatorio solo para nuevos podcasts)
    $youtube_url = trim($_POST['youtube_url']);
    if (!$editing && empty($youtube_url)) {
        $errors[] = "La URL de YouTube es obligatoria";
    }
    
    // Validar formato de URL de YouTube
    if (!empty($youtube_url) && !preg_match('/^(https?:\/\/)?(www\.)?(youtube\.com\/(watch\?v=|embed\/)|youtu\.be\/)[a-zA-Z0-9_-]{11}/', $youtube_url)) {
        $errors[] = "La URL de YouTube no tiene un formato válido";
    }
    
    $imagen_filename = $editing ? $podcast['imagen'] : '';
    
    // Procesar imagen
    if (!empty($_FILES['imagen']['name'])) {
        $imagen_ext = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
        $allowed_img = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        
        if (!in_array($imagen_ext, $allowed_img)) {
            $errors[] = "Formato de imagen no válido. Use: " . implode(', ', $allowed_img);
        } elseif ($_FILES['imagen']['size'] > 5 * 1024 * 1024) {
            $errors[] = "La imagen no puede superar los 5MB";
        } else {
            $imagen_filename = uniqid() . '.' . $imagen_ext;
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], __DIR__ . '/../uploads/podcasts/images/' . $imagen_filename)) {
                $errors[] = "Error al subir la imagen";
            } else {
                // Eliminar imagen anterior si existe
                if ($editing && $podcast['imagen'] && file_exists(__DIR__ . '/../uploads/podcasts/images/' . $podcast['imagen'])) {
                    unlink(__DIR__ . '/../uploads/podcasts/images/' . $podcast['imagen']);
                }
            }
        }
    }
    
    // Extraer ID del video de YouTube
    $youtube_id = '';
    if (!empty($youtube_url)) {
        // Extraer ID de diferentes formatos de URL de YouTube
        if (preg_match('/(?:youtube\.com\/(?:[^\/]+\/.+\/|(?:v|e(?:mbed)?)\/|.*[?&]v=)|youtu\.be\/)([^"&?\/\s]{11})/', $youtube_url, $matches)) {
            $youtube_id = $matches[1];
        } else {
            $errors[] = "No se pudo extraer el ID del video de YouTube";
        }
    }
    
    // Si no hay errores, guardar en la base de datos
    if (empty($errors)) {
        try {
            if ($editing) {
                $sql = "UPDATE podcasts SET 
                        titulo_es = ?, titulo_gl = ?, descripcion_es = ?, descripcion_gl = ?,
                        fecha = ?, duracion = ?, imagen = ?, youtube_url = ?, youtube_id = ?, programa_id = ?, 
                        categoria_id = ?, activo = ?, destacado = ?, ultima_actualizacion = NOW()
                        WHERE id = ?";
                $params = [$titulo_es, $titulo_gl, $descripcion_es, $descripcion_gl, $fecha, 
                          $duracion, $imagen_filename, $youtube_url, $youtube_id, $programa_id, 
                          $categoria_id, $activo, $destacado, $id];
            } else {
                $sql = "INSERT INTO podcasts (titulo_es, titulo_gl, descripcion_es, descripcion_gl,
                        fecha, duracion, imagen, youtube_url, youtube_id, programa_id, categoria_id, activo, 
                        destacado, creado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [$titulo_es, $titulo_gl, $descripcion_es, $descripcion_gl, $fecha, 
                          $duracion, $imagen_filename, $youtube_url, $youtube_id, $programa_id, 
                          $categoria_id, $activo, $destacado, $_SESSION['user_id']];
            }
            
            $stmt = $con->prepare($sql);
            $stmt->execute($params);
            
            $success = $editing ? "Podcast actualizado correctamente" : "Podcast creado correctamente";
            
            // Redireccionar después de 2 segundos
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'podcasts.php';
                }, 2000);
            </script>";
            
        } catch (PDOException $e) {
            $errors[] = "Error en la base de datos: " . $e->getMessage();
        }
    }
}

// Obtener programas para el select
$programas = $con->query("SELECT id, nombre_es FROM programas WHERE activo = 1 ORDER BY nombre_es")->fetchAll(PDO::FETCH_ASSOC);

// Obtener categorías para el select
$categorias = $con->query("SELECT id, nombre_es FROM categorias_podcast WHERE activo = 1 ORDER BY nombre_es")->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Editar' : 'Nuevo'; ?> Podcast - RadioRías Admin</title>
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
                    <a href="podcasts.php" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <img src="../assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-10 h-10 rounded-full">
                    <div>
                        <h1 class="text-2xl font-black text-white">
                            <?php echo $editing ? 'Editar' : 'Nuevo'; ?> Podcast
                        </h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="podcasts.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-list"></i>
                        <span>Ver Lista</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mensajes -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-1"></i>
                    <div>
                        <h3 class="text-sm font-medium text-red-800">Se encontraron errores:</h3>
                        <ul class="mt-2 text-sm text-red-700 list-disc list-inside">
                            <?php foreach ($errors as $error): ?>
                                <li><?php echo htmlspecialchars($error); ?></li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <?php if ($success): ?>
            <div class="bg-green-50 border border-green-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <i class="fas fa-check-circle text-green-400 mr-3 mt-1"></i>
                    <div>
                        <h3 class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($success); ?></h3>
                        <p class="text-sm text-green-700 mt-1">Redirigiendo a la lista de podcasts...</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">
                    <?php echo $editing ? 'Editar Podcast' : 'Crear Nuevo Podcast'; ?>
                </h2>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <!-- Información básica -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="titulo_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Título (Español) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="titulo_es" name="titulo_es" required
                               value="<?php echo $editing ? htmlspecialchars($podcast['titulo_es']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="titulo_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Título (Gallego) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="titulo_gl" name="titulo_gl" required
                               value="<?php echo $editing ? htmlspecialchars($podcast['titulo_gl']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Descripciones -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="descripcion_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción (Español) <span class="text-red-500">*</span>
                        </label>
                        <textarea id="descripcion_es" name="descripcion_es" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"><?php echo $editing ? htmlspecialchars($podcast['descripcion_es']) : ''; ?></textarea>
                    </div>
                    
                    <div>
                        <label for="descripcion_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción (Gallego) <span class="text-red-500">*</span>
                        </label>
                        <textarea id="descripcion_gl" name="descripcion_gl" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"><?php echo $editing ? htmlspecialchars($podcast['descripcion_gl']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Fecha y duración -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <div>
                        <label for="fecha" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha <span class="text-red-500">*</span>
                        </label>
                        <input type="date" id="fecha" name="fecha" required
                               value="<?php echo $editing ? $podcast['fecha'] : date('Y-m-d'); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="duracion" class="block text-sm font-medium text-gray-700 mb-2">
                            Duración (ej: 45:30)
                        </label>
                        <input type="text" id="duracion" name="duracion" placeholder="mm:ss"
                               value="<?php echo $editing ? htmlspecialchars($podcast['duracion']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>

                <!-- Programa y categoría -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="programa_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Programa
                        </label>
                        <select id="programa_id" name="programa_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Seleccionar programa</option>
                            <?php foreach ($programas as $programa): ?>
                                <option value="<?php echo $programa['id']; ?>"
                                        <?php echo ($editing && $podcast['programa_id'] == $programa['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($programa['nombre_es']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría
                        </label>
                        <select id="categoria_id" name="categoria_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Seleccionar categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>"
                                        <?php echo ($editing && $podcast['categoria_id'] == $categoria['id']) ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nombre_es']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>

                <!-- Archivos -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="imagen" class="block text-sm font-medium text-gray-700 mb-2">
                            Imagen del Podcast
                        </label>
                        <?php if ($editing && $podcast['imagen']): ?>
                            <div class="mb-3">
                                <img src="../uploads/podcasts/images/<?php echo htmlspecialchars($podcast['imagen']); ?>" 
                                     alt="Imagen actual" class="w-32 h-32 object-cover rounded-lg">
                                <p class="text-sm text-gray-500 mt-1">Imagen actual</p>
                            </div>
                        <?php endif; ?>
                        <input type="file" id="imagen" name="imagen" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</p>
                    </div>
                    
                    <div>
                        <label for="youtube_url" class="block text-sm font-medium text-gray-700 mb-2">
                            URL de YouTube <?php echo !$editing ? '<span class="text-red-500">*</span>' : ''; ?>
                        </label>
                        <?php if ($editing && $podcast['youtube_url']): ?>
                            <div class="mb-3 p-3 bg-gray-50 rounded-lg">
                                <div class="flex items-center">
                                    <i class="fab fa-youtube text-red-500 mr-2"></i>
                                    <span class="text-sm text-gray-700"><?php echo htmlspecialchars($podcast['youtube_url']); ?></span>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">URL actual</p>
                            </div>
                        <?php endif; ?>
                        <input type="url" id="youtube_url" name="youtube_url" 
                               placeholder="https://www.youtube.com/watch?v=VIDEO_ID o https://youtu.be/VIDEO_ID"
                               value="<?php echo $editing ? htmlspecialchars($podcast['youtube_url']) : ''; ?>"
                               <?php echo !$editing ? 'required' : ''; ?>
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <p class="text-sm text-gray-500 mt-1">Formato: https://www.youtube.com/watch?v=VIDEO_ID o https://youtu.be/VIDEO_ID</p>
                    </div>
                </div>

                <!-- Opciones -->
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1"
                               <?php echo (!$editing || $podcast['activo']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                        <label for="activo" class="ml-2 block text-sm text-gray-900">
                            Activo
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="destacado" name="destacado" value="1"
                               <?php echo ($editing && $podcast['destacado']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                        <label for="destacado" class="ml-2 block text-sm text-gray-900">
                            Destacado
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="podcasts.php" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-bold transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <?php echo $editing ? 'Actualizar' : 'Crear'; ?> Podcast
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
        // Validación de duración
        document.getElementById('duracion').addEventListener('input', function(e) {
            let value = e.target.value;
            // Permitir solo números y dos puntos
            value = value.replace(/[^0-9:]/g, '');
            // Formato mm:ss
            if (value.length > 5) {
                value = value.substring(0, 5);
            }
            e.target.value = value;
        });

        // Preview de imagen
        document.getElementById('imagen').addEventListener('change', function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Crear preview si no existe
                    let preview = document.getElementById('imagen-preview');
                    if (!preview) {
                        preview = document.createElement('img');
                        preview.id = 'imagen-preview';
                        preview.className = 'w-32 h-32 object-cover rounded-lg mt-2';
                        document.getElementById('imagen').parentNode.appendChild(preview);
                    }
                    preview.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        });
    </script>
</body>
</html>
