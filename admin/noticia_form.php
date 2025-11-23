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

// Obtener categorías
$categorias_sql = "SELECT * FROM categorias_noticia WHERE activo = 1 ORDER BY orden, nombre_es";
$categorias = $con->query($categorias_sql)->fetchAll(PDO::FETCH_ASSOC);

// Variables para el formulario
$noticia = null;
$is_edit = false;
$noticia_id = 0;

// Si es edición, obtener la noticia
if (isset($_GET['id'])) {
    $noticia_id = (int)$_GET['id'];
    $stmt = $con->prepare("SELECT * FROM noticias WHERE id = ?");
    $stmt->execute([$noticia_id]);
    $noticia = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($noticia) {
        $is_edit = true;
    } else {
        header("Location: noticias.php");
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo_es = trim($_POST['titulo_es']);
    $titulo_gl = trim($_POST['titulo_gl']);
    $subtitulo_es = trim($_POST['subtitulo_es']);
    $subtitulo_gl = trim($_POST['subtitulo_gl']);
    $contenido_es = trim($_POST['contenido_es']);
    $contenido_gl = trim($_POST['contenido_gl']);
    $resumen_es = trim($_POST['resumen_es']);
    $resumen_gl = trim($_POST['resumen_gl']);
    $autor = trim($_POST['autor']);
    $categoria_id = (int)$_POST['categoria_id'];
    $fecha_publicacion = $_POST['fecha_publicacion'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    $meta_titulo_es = trim($_POST['meta_titulo_es']);
    $meta_titulo_gl = trim($_POST['meta_titulo_gl']);
    $meta_descripcion_es = trim($_POST['meta_descripcion_es']);
    $meta_descripcion_gl = trim($_POST['meta_descripcion_gl']);
    
    // Validaciones básicas
    $errors = [];
    
    if (empty($titulo_es)) {
        $errors[] = "El título en español es obligatorio";
    }
    
    if (empty($contenido_es)) {
        $errors[] = "El contenido en español es obligatorio";
    }
    
    if (empty($fecha_publicacion)) {
        $errors[] = "La fecha de publicación es obligatoria";
    }
    
    // Si no hay errores, procesar
    if (empty($errors)) {
        try {
            // Procesar imagen si se subió
            $imagen_nombre = null;
            $imagen_alt_es = null;
            $imagen_alt_gl = null;
            
            if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
                $upload_dir = __DIR__ . '/../uploads/noticias/images/';
                
                // Crear directorio si no existe
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $file_extension = strtolower(pathinfo($_FILES['imagen']['name'], PATHINFO_EXTENSION));
                $allowed_extensions = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
                
                if (in_array($file_extension, $allowed_extensions)) {
                    $imagen_nombre = uniqid() . '.' . $file_extension;
                    $upload_path = $upload_dir . $imagen_nombre;
                    
                    if (move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_path)) {
                        $imagen_alt_es = $titulo_es;
                        $imagen_alt_gl = $titulo_gl;
                    }
                }
            }
            
            // Generar slugs
            function generateSlug($text) {
                $text = strtolower($text);
                $text = preg_replace('/[^a-z0-9\s-]/', '', $text);
                $text = preg_replace('/[\s-]+/', '-', $text);
                return trim($text, '-');
            }
            
            $slug_es = generateSlug($titulo_es);
            $slug_gl = generateSlug($titulo_gl);
            
            if ($is_edit) {
                // Actualizar noticia existente
                $sql = "UPDATE noticias SET 
                        titulo_es = ?, titulo_gl = ?, subtitulo_es = ?, subtitulo_gl = ?,
                        contenido_es = ?, contenido_gl = ?, resumen_es = ?, resumen_gl = ?,
                        autor = ?, categoria_id = ?, fecha_publicacion = ?, activo = ?, destacado = ?,
                        meta_titulo_es = ?, meta_titulo_gl = ?, meta_descripcion_es = ?, meta_descripcion_gl = ?,
                        slug_es = ?, slug_gl = ?, ultima_actualizacion = NOW()";
                
                $params = [
                    $titulo_es, $titulo_gl, $subtitulo_es, $subtitulo_gl,
                    $contenido_es, $contenido_gl, $resumen_es, $resumen_gl,
                    $autor, $categoria_id, $fecha_publicacion, $activo, $destacado,
                    $meta_titulo_es, $meta_titulo_gl, $meta_descripcion_es, $meta_descripcion_gl,
                    $slug_es, $slug_gl
                ];
                
                // Si hay nueva imagen, actualizarla
                if ($imagen_nombre) {
                    $sql .= ", imagen = ?, imagen_alt_es = ?, imagen_alt_gl = ?";
                    $params[] = $imagen_nombre;
                    $params[] = $imagen_alt_es;
                    $params[] = $imagen_alt_gl;
                }
                
                $sql .= " WHERE id = ?";
                $params[] = $noticia_id;
                
                $stmt = $con->prepare($sql);
                $stmt->execute($params);
                
                $success_message = "Noticia actualizada correctamente";
            } else {
                // Crear nueva noticia
                $sql = "INSERT INTO noticias (
                    titulo_es, titulo_gl, subtitulo_es, subtitulo_gl,
                    contenido_es, contenido_gl, resumen_es, resumen_gl,
                    autor, categoria_id, fecha_publicacion, activo, destacado,
                    meta_titulo_es, meta_titulo_gl, meta_descripcion_es, meta_descripcion_gl,
                    slug_es, slug_gl, imagen, imagen_alt_es, imagen_alt_gl, creado_por
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $con->prepare($sql);
                $stmt->execute([
                    $titulo_es, $titulo_gl, $subtitulo_es, $subtitulo_gl,
                    $contenido_es, $contenido_gl, $resumen_es, $resumen_gl,
                    $autor, $categoria_id, $fecha_publicacion, $activo, $destacado,
                    $meta_titulo_es, $meta_titulo_gl, $meta_descripcion_es, $meta_descripcion_gl,
                    $slug_es, $slug_gl, $imagen_nombre, $imagen_alt_es, $imagen_alt_gl,
                    $_SESSION['user_id']
                ]);
                
                $success_message = "Noticia creada correctamente";
            }
            
            // Redireccionar con mensaje de éxito
            header("Location: noticias.php?success=" . urlencode($success_message));
            exit();
            
        } catch (Exception $e) {
            $errors[] = "Error al guardar la noticia: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $is_edit ? 'Editar Noticia' : 'Nueva Noticia'; ?> - RadioRías Admin</title>
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
                        <h1 class="text-2xl font-black text-white"><?php echo $is_edit ? 'Editar Noticia' : 'Nueva Noticia'; ?></h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="noticias.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-list"></i>
                        <span>Ver Noticias</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <main class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <!-- Mostrar errores -->
        <?php if (!empty($errors)): ?>
            <div class="bg-red-50 border border-red-200 rounded-lg p-4 mb-6">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <i class="fas fa-exclamation-triangle text-red-400"></i>
                    </div>
                    <div class="ml-3">
                        <h3 class="text-sm font-medium text-red-800">Se encontraron los siguientes errores:</h3>
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

        <!-- Formulario -->
        <form method="POST" enctype="multipart/form-data" class="space-y-6">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Información Básica</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Título Español -->
                    <div>
                        <label for="titulo_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Título (Español) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="titulo_es" name="titulo_es" 
                               value="<?php echo htmlspecialchars($noticia['titulo_es'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               required>
                    </div>
                    
                    <!-- Título Gallego -->
                    <div>
                        <label for="titulo_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Título (Gallego)
                        </label>
                        <input type="text" id="titulo_gl" name="titulo_gl" 
                               value="<?php echo htmlspecialchars($noticia['titulo_gl'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Subtítulo Español -->
                    <div>
                        <label for="subtitulo_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Subtítulo (Español)
                        </label>
                        <input type="text" id="subtitulo_es" name="subtitulo_es" 
                               value="<?php echo htmlspecialchars($noticia['subtitulo_es'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Subtítulo Gallego -->
                    <div>
                        <label for="subtitulo_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Subtítulo (Gallego)
                        </label>
                        <input type="text" id="subtitulo_gl" name="subtitulo_gl" 
                               value="<?php echo htmlspecialchars($noticia['subtitulo_gl'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Contenido</h2>
                
                <!-- Resumen Español -->
                <div class="mb-6">
                    <label for="resumen_es" class="block text-sm font-medium text-gray-700 mb-2">
                        Resumen (Español)
                    </label>
                    <textarea id="resumen_es" name="resumen_es" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Breve resumen de la noticia..."><?php echo htmlspecialchars($noticia['resumen_es'] ?? ''); ?></textarea>
                </div>
                
                <!-- Resumen Gallego -->
                <div class="mb-6">
                    <label for="resumen_gl" class="block text-sm font-medium text-gray-700 mb-2">
                        Resumen (Gallego)
                    </label>
                    <textarea id="resumen_gl" name="resumen_gl" rows="3"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Breve resumen da noticia..."><?php echo htmlspecialchars($noticia['resumen_gl'] ?? ''); ?></textarea>
                </div>
                
                <!-- Contenido Español -->
                <div class="mb-6">
                    <label for="contenido_es" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenido (Español) <span class="text-red-500">*</span>
                    </label>
                    <textarea id="contenido_es" name="contenido_es" rows="10"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Contenido completo de la noticia..."
                              required><?php echo htmlspecialchars($noticia['contenido_es'] ?? ''); ?></textarea>
                </div>
                
                <!-- Contenido Gallego -->
                <div class="mb-6">
                    <label for="contenido_gl" class="block text-sm font-medium text-gray-700 mb-2">
                        Contenido (Gallego)
                    </label>
                    <textarea id="contenido_gl" name="contenido_gl" rows="10"
                              class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                              placeholder="Contido completo da noticia..."><?php echo htmlspecialchars($noticia['contenido_gl'] ?? ''); ?></textarea>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">Configuración</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Autor -->
                    <div>
                        <label for="autor" class="block text-sm font-medium text-gray-700 mb-2">
                            Autor
                        </label>
                        <input type="text" id="autor" name="autor" 
                               value="<?php echo htmlspecialchars($noticia['autor'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Categoría -->
                    <div>
                        <label for="categoria_id" class="block text-sm font-medium text-gray-700 mb-2">
                            Categoría
                        </label>
                        <select id="categoria_id" name="categoria_id" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <option value="">Seleccionar categoría</option>
                            <?php foreach ($categorias as $categoria): ?>
                                <option value="<?php echo $categoria['id']; ?>" 
                                        <?php echo ($noticia['categoria_id'] ?? '') == $categoria['id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($categoria['nombre_es']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    
                    <!-- Fecha de Publicación -->
                    <div>
                        <label for="fecha_publicacion" class="block text-sm font-medium text-gray-700 mb-2">
                            Fecha de Publicación <span class="text-red-500">*</span>
                        </label>
                        <input type="datetime-local" id="fecha_publicacion" name="fecha_publicacion" 
                               value="<?php echo $noticia['fecha_publicacion'] ? date('Y-m-d\TH:i', strtotime($noticia['fecha_publicacion'])) : date('Y-m-d\TH:i'); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"
                               required>
                    </div>
                    
                    <!-- Imagen -->
                    <div>
                        <label for="imagen" class="block text-sm font-medium text-gray-700 mb-2">
                            Imagen
                        </label>
                        <input type="file" id="imagen" name="imagen" accept="image/*"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        <?php if ($noticia && $noticia['imagen']): ?>
                            <div class="mt-2">
                                <img src="../uploads/noticias/images/<?php echo htmlspecialchars($noticia['imagen']); ?>" 
                                     alt="Imagen actual" class="w-32 h-32 object-cover rounded-lg">
                                <p class="text-sm text-gray-500 mt-1">Imagen actual</p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
                
                <!-- Estados -->
                <div class="mt-6 flex space-x-6">
                    <label class="flex items-center">
                        <input type="checkbox" name="activo" value="1" 
                               <?php echo ($noticia['activo'] ?? 1) ? 'checked' : ''; ?>
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Activa</span>
                    </label>
                    
                    <label class="flex items-center">
                        <input type="checkbox" name="destacado" value="1" 
                               <?php echo ($noticia['destacado'] ?? 0) ? 'checked' : ''; ?>
                               class="rounded border-gray-300 text-red-600 focus:ring-red-500">
                        <span class="ml-2 text-sm text-gray-700">Destacada</span>
                    </label>
                </div>
            </div>

            <div class="bg-white rounded-lg shadow-md p-6">
                <h2 class="text-lg font-bold text-gray-900 mb-4">SEO (Opcional)</h2>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Meta Título Español -->
                    <div>
                        <label for="meta_titulo_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Título (Español)
                        </label>
                        <input type="text" id="meta_titulo_es" name="meta_titulo_es" 
                               value="<?php echo htmlspecialchars($noticia['meta_titulo_es'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Meta Título Gallego -->
                    <div>
                        <label for="meta_titulo_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Título (Gallego)
                        </label>
                        <input type="text" id="meta_titulo_gl" name="meta_titulo_gl" 
                               value="<?php echo htmlspecialchars($noticia['meta_titulo_gl'] ?? ''); ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <!-- Meta Descripción Español -->
                    <div>
                        <label for="meta_descripcion_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Descripción (Español)
                        </label>
                        <textarea id="meta_descripcion_es" name="meta_descripcion_es" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"><?php echo htmlspecialchars($noticia['meta_descripcion_es'] ?? ''); ?></textarea>
                    </div>
                    
                    <!-- Meta Descripción Gallego -->
                    <div>
                        <label for="meta_descripcion_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Meta Descripción (Gallego)
                        </label>
                        <textarea id="meta_descripcion_gl" name="meta_descripcion_gl" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"><?php echo htmlspecialchars($noticia['meta_descripcion_gl'] ?? ''); ?></textarea>
                    </div>
                </div>
            </div>

            <!-- Botones -->
            <div class="flex justify-end space-x-4">
                <a href="noticias.php" 
                   class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 transition-colors">
                    Cancelar
                </a>
                <button type="submit" 
                        class="px-6 py-3 bg-red-600 hover:bg-red-700 text-white rounded-lg font-bold transition-colors">
                    <i class="fas fa-save mr-2"></i>
                    <?php echo $is_edit ? 'Actualizar Noticia' : 'Crear Noticia'; ?>
                </button>
            </div>
        </form>
    </main>

    <script>
        // Auto-generar meta título si está vacío
        document.getElementById('titulo_es').addEventListener('input', function() {
            const metaTitulo = document.getElementById('meta_titulo_es');
            if (!metaTitulo.value) {
                metaTitulo.value = this.value;
            }
        });
        
        // Auto-generar meta descripción si está vacío
        document.getElementById('resumen_es').addEventListener('input', function() {
            const metaDescripcion = document.getElementById('meta_descripcion_es');
            if (!metaDescripcion.value) {
                metaDescripcion.value = this.value;
            }
        });
    </script>
</body>
</html>
