<?php
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');

// Verificar que esté autenticado
if (!isset($_SESSION['user_id']) || !isset($_SESSION['username'])) {
    echo "<script>alert('Debe iniciar sesión.');</script>";
    echo "<script>window.location='../login.php';</script>";
    exit();
}

$conex = new database();
$con = $conex->conectar();

// Crear directorio si no existe
$upload_dir = __DIR__ . '/../uploads/programas/images';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

$editing = false;
$programa = null;
$errors = [];
$success = '';

// Si estamos editando, cargar datos del programa
if (isset($_GET['id'])) {
    $editing = true;
    $id = (int)$_GET['id'];
    $stmt = $con->prepare("SELECT * FROM programas WHERE id = ?");
    $stmt->execute([$id]);
    $programa = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$programa) {
        header("Location: programas.php");
        exit();
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_es = trim($_POST['nombre_es']);
    $nombre_gl = trim($_POST['nombre_gl']);
    $descripcion_es = trim($_POST['descripcion_es']);
    $descripcion_gl = trim($_POST['descripcion_gl']);
    $horario = trim($_POST['horario']);
    $dias = isset($_POST['dias']) && is_array($_POST['dias']) ? implode(', ', $_POST['dias']) : '';
    $activo = isset($_POST['activo']) ? 1 : 0;
    $destacado = isset($_POST['destacado']) ? 1 : 0;
    
    // Validaciones
    if (empty($nombre_es)) $errors[] = "El nombre en español es obligatorio";
    if (empty($nombre_gl)) $errors[] = "El nombre en gallego es obligatorio";
    if (empty($descripcion_es)) $errors[] = "La descripción en español es obligatoria";
    if (empty($descripcion_gl)) $errors[] = "La descripción en gallego es obligatoria";
    if (empty($horario)) $errors[] = "El horario es obligatorio";
    if (empty($dias)) $errors[] = "Debe seleccionar al menos un día de emisión";
    
    $imagen_filename = $editing ? $programa['imagen'] : '';
    
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
            if (!move_uploaded_file($_FILES['imagen']['tmp_name'], $upload_dir . '/' . $imagen_filename)) {
                $errors[] = "Error al subir la imagen";
            } else {
                // Eliminar imagen anterior si existe
                if ($editing && $programa['imagen'] && file_exists($upload_dir . '/' . $programa['imagen'])) {
                    unlink($upload_dir . '/' . $programa['imagen']);
                }
            }
        }
    }
    
    // Si no hay errores, guardar en la base de datos
    if (empty($errors)) {
        try {
            if ($editing) {
                $sql = "UPDATE programas SET 
                        nombre_es = ?, nombre_gl = ?, descripcion_es = ?, descripcion_gl = ?,
                        horario = ?, dias = ?, imagen = ?, activo = ?, destacado = ?, ultima_actualizacion = NOW()
                        WHERE id = ?";
                $params = [$nombre_es, $nombre_gl, $descripcion_es, $descripcion_gl, 
                          $horario, $dias, $imagen_filename, $activo, $destacado, $id];
            } else {
                $sql = "INSERT INTO programas (nombre_es, nombre_gl, descripcion_es, descripcion_gl,
                        horario, dias, imagen, activo, destacado, creado_por) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                $params = [$nombre_es, $nombre_gl, $descripcion_es, $descripcion_gl, 
                          $horario, $dias, $imagen_filename, $activo, $destacado, $_SESSION['user_id']];
            }
            
            $stmt = $con->prepare($sql);
            $stmt->execute($params);
            
            $success = $editing ? "Programa actualizado correctamente" : "Programa creado correctamente";
            
            // Redireccionar después de 2 segundos
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'programas.php';
                }, 2000);
            </script>";
            
        } catch (PDOException $e) {
            $errors[] = "Error en la base de datos: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Editar' : 'Nuevo'; ?> Programa - RadioRías Admin</title>
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
                    <a href="programas.php" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <img src="../assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-10 h-10 rounded-full">
                    <div>
                        <h1 class="text-2xl font-black text-white">
                            <?php echo $editing ? 'Editar' : 'Nuevo'; ?> Programa
                        </h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="programas.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
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
                        <p class="text-sm text-green-700 mt-1">Redirigiendo a la lista de programas...</p>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="bg-white rounded-lg shadow-md">
            <div class="px-6 py-4 border-b border-gray-200">
                <h2 class="text-lg font-bold text-gray-900">
                    <?php echo $editing ? 'Editar Programa' : 'Crear Nuevo Programa'; ?>
                </h2>
            </div>
            
            <form method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                <!-- Nombres -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="nombre_es" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre (Español) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_es" name="nombre_es" required
                               value="<?php echo $editing ? htmlspecialchars($programa['nombre_es']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label for="nombre_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Nombre (Gallego) <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="nombre_gl" name="nombre_gl" required
                               value="<?php echo $editing ? htmlspecialchars($programa['nombre_gl']) : ''; ?>"
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
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"><?php echo $editing ? htmlspecialchars($programa['descripcion_es']) : ''; ?></textarea>
                    </div>
                    
                    <div>
                        <label for="descripcion_gl" class="block text-sm font-medium text-gray-700 mb-2">
                            Descripción (Gallego) <span class="text-red-500">*</span>
                        </label>
                        <textarea id="descripcion_gl" name="descripcion_gl" rows="4" required
                                  class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent"><?php echo $editing ? htmlspecialchars($programa['descripcion_gl']) : ''; ?></textarea>
                    </div>
                </div>

                <!-- Horario y días -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="horario" class="block text-sm font-medium text-gray-700 mb-2">
                            Horario <span class="text-red-500">*</span>
                        </label>
                        <input type="text" id="horario" name="horario" required
                               placeholder="ej: 10:00 - 12:00"
                               value="<?php echo $editing ? htmlspecialchars($programa['horario']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>
                    
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            Días de Emisión <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-3">
                            <?php 
                            $dias_semana = [
                                'Lunes' => 'Lunes',
                                'Martes' => 'Martes', 
                                'Miércoles' => 'Miércoles',
                                'Jueves' => 'Jueves',
                                'Viernes' => 'Viernes',
                                'Sábado' => 'Sábado',
                                'Domingo' => 'Domingo'
                            ];
                            
                            $dias_seleccionados = [];
                            if ($editing && $programa['dias']) {
                                $dias_seleccionados = array_map('trim', explode(',', $programa['dias']));
                            }
                            
                            foreach ($dias_semana as $key => $dia): 
                                $checked = in_array($dia, $dias_seleccionados) ? 'checked' : '';
                            ?>
                                <label class="flex items-center p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer transition-colors <?php echo $checked ? 'bg-gray-50 border-gray-300' : ''; ?>">
                                    <input type="checkbox" name="dias[]" value="<?php echo $dia; ?>" <?php echo $checked; ?>
                                           class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded mr-3">
                                    <span class="text-sm font-medium text-gray-700"><?php echo $dia; ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        <p class="text-sm text-gray-500 mt-2">Selecciona uno o más días de emisión</p>
                        
                        <!-- Botones de selección rápida -->
                        <div class="mt-3 flex flex-wrap gap-2">
                            <button type="button" onclick="seleccionarTodos()" 
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors">
                                Todos
                            </button>
                            <button type="button" onclick="seleccionarSemana()" 
                                    class="px-3 py-1 text-xs bg-blue-100 hover:bg-blue-200 text-blue-700 rounded-md transition-colors">
                                Lun-Vie
                            </button>
                            <button type="button" onclick="seleccionarFinSemana()" 
                                    class="px-3 py-1 text-xs bg-green-100 hover:bg-green-200 text-green-700 rounded-md transition-colors">
                                Sáb-Dom
                            </button>
                            <button type="button" onclick="limpiarSeleccion()" 
                                    class="px-3 py-1 text-xs bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition-colors">
                                Limpiar
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Imagen -->
                <div>
                    <label for="imagen" class="block text-sm font-medium text-gray-700 mb-2">
                        Imagen del Programa
                    </label>
                    <?php if ($editing && $programa['imagen']): ?>
                        <div class="mb-3">
                            <img src="../uploads/programas/images/<?php echo htmlspecialchars($programa['imagen']); ?>" 
                                 alt="Imagen actual" class="w-32 h-32 object-cover rounded-lg">
                            <p class="text-sm text-gray-500 mt-1">Imagen actual</p>
                        </div>
                    <?php endif; ?>
                    <input type="file" id="imagen" name="imagen" accept="image/*"
                           class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    <p class="text-sm text-gray-500 mt-1">Formatos: JPG, PNG, GIF, WEBP. Máximo 5MB.</p>
                </div>

                <!-- Estado -->
                <div class="flex flex-wrap gap-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" value="1"
                               <?php echo (!$editing || $programa['activo']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                        <label for="activo" class="ml-2 block text-sm text-gray-900">
                            Programa activo
                        </label>
                    </div>
                    
                    <div class="flex items-center">
                        <input type="checkbox" id="destacado" name="destacado" value="1"
                               <?php echo ($editing && $programa['destacado']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-yellow-600 focus:ring-yellow-500 border-gray-300 rounded">
                        <label for="destacado" class="ml-2 block text-sm text-gray-900">
                            <i class="fas fa-star text-yellow-500 mr-1"></i>Programa destacado
                        </label>
                    </div>
                </div>

                <!-- Botones -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                    <a href="programas.php" 
                       class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-2 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-bold transition-colors">
                        <i class="fas fa-save mr-2"></i>
                        <?php echo $editing ? 'Actualizar' : 'Crear'; ?> Programa
                    </button>
                </div>
            </form>
        </div>
    </main>

    <script>
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

        // Funciones para selección de días
        function seleccionarTodos() {
            const checkboxes = document.querySelectorAll('input[name="dias[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = true;
                actualizarEstiloCheckbox(checkbox);
            });
        }

        function seleccionarSemana() {
            const diasSemana = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes'];
            const checkboxes = document.querySelectorAll('input[name="dias[]"]');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = diasSemana.includes(checkbox.value);
                actualizarEstiloCheckbox(checkbox);
            });
        }

        function seleccionarFinSemana() {
            const diasFinSemana = ['Sábado', 'Domingo'];
            const checkboxes = document.querySelectorAll('input[name="dias[]"]');
            
            checkboxes.forEach(checkbox => {
                checkbox.checked = diasFinSemana.includes(checkbox.value);
                actualizarEstiloCheckbox(checkbox);
            });
        }

        function limpiarSeleccion() {
            const checkboxes = document.querySelectorAll('input[name="dias[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.checked = false;
                actualizarEstiloCheckbox(checkbox);
            });
        }

        function actualizarEstiloCheckbox(checkbox) {
            const label = checkbox.closest('label');
            if (checkbox.checked) {
                label.classList.add('bg-gray-50', 'border-gray-300');
                label.classList.remove('bg-white');
            } else {
                label.classList.remove('bg-gray-50', 'border-gray-300');
                label.classList.add('bg-white');
            }
        }

        // Actualizar estilos cuando se hace clic en los checkboxes
        document.addEventListener('DOMContentLoaded', function() {
            const checkboxes = document.querySelectorAll('input[name="dias[]"]');
            checkboxes.forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    actualizarEstiloCheckbox(this);
                });
                
                // Aplicar estilo inicial
                actualizarEstiloCheckbox(checkbox);
            });
        });
    </script>
</body>
</html>
