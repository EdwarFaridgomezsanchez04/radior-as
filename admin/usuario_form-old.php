<?php
session_start();
require_once(__DIR__ . '/../includes/validarsesion.php');
require_once(__DIR__ . '/../config/conexion.php');

// Solo administradores pueden gestionar usuarios
if ($_SESSION['rol'] !== 'admin') {
    header('Location: dashboard.php');
    exit;
}

$conex = new database();
$con = $conex->conectar();

$editing = false;
$usuario = null;
$errors = [];
$success = '';

// Verificar si estamos editando
if (isset($_GET['id'])) {
    $id = (int)$_GET['id'];
    $stmt = $con->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$id]);
    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if ($usuario) {
        $editing = true;
    } else {
        header('Location: usuarios.php');
        exit;
    }
}

// Procesar formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username']);
    $nombre = trim($_POST['nombre']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $confirm_password = trim($_POST['confirm_password']);
    $rol = $_POST['rol'];
    $activo = isset($_POST['activo']) ? 1 : 0;
    
    // Validaciones
    if (empty($username)) $errors[] = "El nombre de usuario es obligatorio";
    if (empty($nombre)) $errors[] = "El nombre completo es obligatorio";
    if (empty($email)) $errors[] = "El email es obligatorio";
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "El email no es válido";
    if (!in_array($rol, ['admin', 'editor'])) $errors[] = "El rol seleccionado no es válido";
    
    // Validaciones de contraseña
    if (!$editing) {
        // Para usuarios nuevos, la contraseña es obligatoria
        if (empty($password)) $errors[] = "La contraseña es obligatoria";
        if (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres";
        if ($password !== $confirm_password) $errors[] = "Las contraseñas no coinciden";
    } else {
        // Para usuarios existentes, solo validar si se proporciona contraseña
        if (!empty($password)) {
            if (strlen($password) < 6) $errors[] = "La contraseña debe tener al menos 6 caracteres";
            if ($password !== $confirm_password) $errors[] = "Las contraseñas no coinciden";
        }
    }
    
    // Verificar username único
    if ($editing) {
        $stmt = $con->prepare("SELECT id FROM usuarios WHERE username = ? AND id != ?");
        $stmt->execute([$username, $id]);
    } else {
        $stmt = $con->prepare("SELECT id FROM usuarios WHERE username = ?");
        $stmt->execute([$username]);
    }
    if ($stmt->fetch()) {
        $errors[] = "El nombre de usuario ya está en uso";
    }
    
    // Verificar email único
    if ($editing) {
        $stmt = $con->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
        $stmt->execute([$email, $id]);
    } else {
        $stmt = $con->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
    }
    if ($stmt->fetch()) {
        $errors[] = "El email ya está en uso";
    }
    
    // Validación especial: no permitir desactivar o cambiar rol del propio usuario
    if ($editing && $id == $_SESSION['user_id']) {
        if (!$activo) {
            $errors[] = "No puedes desactivar tu propia cuenta";
        }
        if ($rol !== $_SESSION['rol']) {
            $errors[] = "No puedes cambiar tu propio rol";
        }
    }
    
    // Si no hay errores, guardar en la base de datos
    if (empty($errors)) {
        try {
            if ($editing) {
                if (!empty($password)) {
                    // Actualizar con nueva contraseña
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET username = ?, password = ?, nombre = ?, email = ?, rol = ?, activo = ?, ultima_actualizacion = NOW() WHERE id = ?";
                    $params = [$username, $hashed_password, $nombre, $email, $rol, $activo, $id];
                } else {
                    // Actualizar sin cambiar contraseña
                    $sql = "UPDATE usuarios SET username = ?, nombre = ?, email = ?, rol = ?, activo = ?, ultima_actualizacion = NOW() WHERE id = ?";
                    $params = [$username, $nombre, $email, $rol, $activo, $id];
                }
            } else {
                // Crear nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (username, password, nombre, email, rol, activo) VALUES (?, ?, ?, ?, ?, ?)";
                $params = [$username, $hashed_password, $nombre, $email, $rol, $activo];
            }
            
            $stmt = $con->prepare($sql);
            $stmt->execute($params);
            
            $success = $editing ? "Usuario actualizado correctamente" : "Usuario creado correctamente";
            
            // Si estamos creando, redirigir a la lista
            if (!$editing) {
                header('Location: usuarios.php?success=' . urlencode($success));
                exit;
            }
            
            // Si estamos editando, recargar los datos
            $stmt = $con->prepare("SELECT * FROM usuarios WHERE id = ?");
            $stmt->execute([$id]);
            $usuario = $stmt->fetch(PDO::FETCH_ASSOC);
            
        } catch (PDOException $e) {
            $errors[] = "Error al guardar: " . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $editing ? 'Editar' : 'Crear'; ?> Usuario - RadioRías</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        'radio-red': '#DC2626',
                        'radio-dark': '#1F2937',
                        'radio-teal': '#0D9488',
                        'radio-cyan': '#0891B2'
                    }
                }
            }
        }
    </script>
</head>
<body class="bg-gray-50">
    <main class="min-h-screen py-8">
        <div class="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-8">
                <div class="flex items-center space-x-4">
                    <a href="usuarios.php" class="text-gray-600 hover:text-gray-900 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <div>
                        <h1 class="text-3xl font-bold text-gray-900">
                            <?php echo $editing ? 'Editar Usuario' : 'Crear Usuario'; ?>
                        </h1>
                        <p class="mt-2 text-gray-600">
                            <?php echo $editing ? 'Modifica los datos del usuario' : 'Completa los datos del nuevo usuario'; ?>
                        </p>
                    </div>
                </div>
            </div>

            <!-- Mensajes -->
            <?php if (!empty($errors)): ?>
                <div class="mb-6 bg-red-50 border border-red-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-exclamation-circle text-red-400 mr-3 mt-0.5"></i>
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

            <?php if (!empty($success)): ?>
                <div class="mb-6 bg-green-50 border border-green-200 rounded-lg p-4">
                    <div class="flex">
                        <i class="fas fa-check-circle text-green-400 mr-3 mt-0.5"></i>
                        <div class="text-sm text-green-700">
                            <?php echo htmlspecialchars($success); ?>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Formulario -->
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900">
                        Información del Usuario
                    </h3>
                </div>
                
                <form method="POST" class="p-6 space-y-6">
                    <!-- Información básica -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre de Usuario <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="username" name="username" required
                                   value="<?php echo $editing ? htmlspecialchars($usuario['username']) : ''; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                        
                        <div>
                            <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                                Nombre Completo <span class="text-red-500">*</span>
                            </label>
                            <input type="text" id="nombre" name="nombre" required
                                   value="<?php echo $editing ? htmlspecialchars($usuario['nombre']) : ''; ?>"
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            Email <span class="text-red-500">*</span>
                        </label>
                        <input type="email" id="email" name="email" required
                               value="<?php echo $editing ? htmlspecialchars($usuario['email']) : ''; ?>"
                               class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                    </div>

                    <!-- Contraseña -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                                Contraseña <?php echo $editing ? '(dejar vacío para no cambiar)' : '<span class="text-red-500">*</span>'; ?>
                            </label>
                            <input type="password" id="password" name="password" 
                                   <?php echo !$editing ? 'required' : ''; ?>
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                            <p class="text-sm text-gray-500 mt-1">Mínimo 6 caracteres</p>
                        </div>
                        
                        <div>
                            <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                                Confirmar Contraseña <?php echo $editing ? '' : '<span class="text-red-500">*</span>'; ?>
                            </label>
                            <input type="password" id="confirm_password" name="confirm_password" 
                                   <?php echo !$editing ? 'required' : ''; ?>
                                   class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                        </div>
                    </div>

                    <!-- Rol y Estado -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="rol" class="block text-sm font-medium text-gray-700 mb-2">
                                Rol <span class="text-red-500">*</span>
                            </label>
                            <select id="rol" name="rol" required
                                    <?php echo ($editing && $id == $_SESSION['user_id']) ? 'disabled' : ''; ?>
                                    class="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-red-500 focus:border-transparent">
                                <option value="editor" <?php echo ($editing && $usuario['rol'] === 'editor') ? 'selected' : ''; ?>>Editor</option>
                                <option value="admin" <?php echo ($editing && $usuario['rol'] === 'admin') ? 'selected' : ''; ?>>Administrador</option>
                            </select>
                            <?php if ($editing && $id == $_SESSION['user_id']): ?>
                                <input type="hidden" name="rol" value="<?php echo $usuario['rol']; ?>">
                                <p class="text-sm text-gray-500 mt-1">No puedes cambiar tu propio rol</p>
                            <?php endif; ?>
                        </div>
                        
                        <div class="flex items-center pt-6">
                            <input type="checkbox" id="activo" name="activo" value="1"
                                   <?php echo (!$editing || $usuario['activo']) ? 'checked' : ''; ?>
                                   <?php echo ($editing && $id == $_SESSION['user_id']) ? 'disabled' : ''; ?>
                                   class="h-4 w-4 text-red-600 focus:ring-red-500 border-gray-300 rounded">
                            <label for="activo" class="ml-2 block text-sm text-gray-900">
                                Usuario activo
                            </label>
                            <?php if ($editing && $id == $_SESSION['user_id']): ?>
                                <input type="hidden" name="activo" value="1">
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if ($editing && $id == $_SESSION['user_id']): ?>
                        <div class="bg-blue-50 border border-blue-200 rounded-lg p-4">
                            <div class="flex">
                                <i class="fas fa-info-circle text-blue-400 mr-3 mt-0.5"></i>
                                <div class="text-sm text-blue-700">
                                    <strong>Nota:</strong> Estás editando tu propia cuenta. No puedes cambiar tu rol ni desactivar tu cuenta.
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>

                    <!-- Botones -->
                    <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200">
                        <a href="usuarios.php" 
                           class="px-6 py-2 border border-gray-300 rounded-lg text-gray-700 hover:bg-gray-50 font-medium transition-colors">
                            Cancelar
                        </a>
                        <button type="submit" 
                                class="px-6 py-2 bg-radio-red text-white rounded-lg hover:bg-red-700 font-medium transition-colors">
                            <i class="fas fa-save mr-2"></i>
                            <?php echo $editing ? 'Actualizar Usuario' : 'Crear Usuario'; ?>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Validación de contraseñas en tiempo real
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password && confirmPassword && password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value) {
                confirmPassword.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>
