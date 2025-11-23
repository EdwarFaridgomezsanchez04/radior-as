<?php
require_once('../includes/validarsesion.php');
require_once('../config/conexion.php');

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
                // Actualizar usuario existente
                if (!empty($password)) {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    $sql = "UPDATE usuarios SET username = ?, nombre = ?, email = ?, password = ?, rol = ?, activo = ? WHERE id = ?";
                    $params = [$username, $nombre, $email, $hashed_password, $rol, $activo, $id];
                } else {
                    $sql = "UPDATE usuarios SET username = ?, nombre = ?, email = ?, rol = ?, activo = ? WHERE id = ?";
                    $params = [$username, $nombre, $email, $rol, $activo, $id];
                }
            } else {
                // Crear nuevo usuario
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "INSERT INTO usuarios (username, nombre, email, password, rol, activo, fecha_creacion) VALUES (?, ?, ?, ?, ?, ?, NOW())";
                $params = [$username, $nombre, $email, $hashed_password, $rol, $activo];
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
    <title><?php echo $editing ? 'Editar' : 'Crear'; ?> Usuario - RadioRías Admin</title>
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
                    <a href="usuarios.php" class="text-white hover:text-gray-200 transition-colors">
                        <i class="fas fa-arrow-left text-xl"></i>
                    </a>
                    <img src="../assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-10 h-10 rounded-full">
                    <div>
                        <h1 class="text-2xl font-black text-white"><?php echo $editing ? 'Editar Usuario' : 'Crear Usuario'; ?></h1>
                        <p class="text-gray-100">Panel de Administración</p>
                    </div>
                </div>
                
                <div class="flex items-center space-x-4">
                    <a href="../includes/salir.php" class="bg-white bg-opacity-20 hover:bg-opacity-30 text-white px-4 py-2 rounded-lg font-bold transition-all duration-300 flex items-center space-x-2">
                        <i class="fas fa-sign-out-alt"></i>
                        <span>Salir</span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Mensajes -->
        <?php if (!empty($errors)): ?>
        <div class="mb-6 bg-red-50 border-l-4 border-red-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-exclamation-triangle text-red-400"></i>
                </div>
                <div class="ml-3">
                    <h3 class="text-sm font-medium text-red-800">Se encontraron errores:</h3>
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

        <?php if ($success): ?>
        <div class="mb-6 bg-green-50 border-l-4 border-green-400 p-4 rounded-lg">
            <div class="flex">
                <div class="flex-shrink-0">
                    <i class="fas fa-check-circle text-green-400"></i>
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-green-800"><?php echo htmlspecialchars($success); ?></p>
                </div>
            </div>
        </div>
        <?php endif; ?>

        <!-- Formulario -->
        <div class="bg-white rounded-xl shadow-md overflow-hidden">
            <div class="px-6 py-4 bg-gray-50 border-b border-gray-200">
                <h2 class="text-lg font-semibold text-gray-900">
                    <i class="fas fa-user-<?php echo $editing ? 'edit' : 'plus'; ?> mr-2 text-gray-600"></i>
                    <?php echo $editing ? 'Editar Usuario' : 'Crear Nuevo Usuario'; ?>
                </h2>
                <p class="text-sm text-gray-600 mt-1">
                    <?php echo $editing ? 'Modifica los datos del usuario seleccionado' : 'Completa todos los campos para crear un nuevo usuario'; ?>
                </p>
            </div>
            
            <form method="POST" class="p-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    
                    <!-- Nombre de Usuario -->
                    <div>
                        <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user mr-1 text-gray-500"></i>
                            Nombre de Usuario *
                        </label>
                        <input type="text" id="username" name="username" 
                               value="<?php echo $editing ? htmlspecialchars($usuario['username']) : ''; ?>" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Debe ser único en el sistema</p>
                    </div>

                    <!-- Nombre Completo -->
                    <div>
                        <label for="nombre" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-id-card mr-1 text-gray-500"></i>
                            Nombre Completo *
                        </label>
                        <input type="text" id="nombre" name="nombre" 
                               value="<?php echo $editing ? htmlspecialchars($usuario['nombre']) : ''; ?>" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors">
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-envelope mr-1 text-gray-500"></i>
                            Email *
                        </label>
                        <input type="email" id="email" name="email" 
                               value="<?php echo $editing ? htmlspecialchars($usuario['email']) : ''; ?>" 
                               required
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors">
                        <p class="text-xs text-gray-500 mt-1">Debe ser único en el sistema</p>
                    </div>

                    <!-- Rol -->
                    <div>
                        <label for="rol" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-user-tag mr-1 text-gray-500"></i>
                            Rol *
                        </label>
                        <select id="rol" name="rol" required
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors">
                            <option value="">Seleccionar rol</option>
                            <option value="admin" <?php echo ($editing && $usuario['rol'] === 'admin') ? 'selected' : ''; ?>>
                                Administrador
                            </option>
                            <option value="editor" <?php echo ($editing && $usuario['rol'] === 'editor') ? 'selected' : ''; ?>>
                                Editor
                            </option>
                        </select>
                    </div>

                    <!-- Contraseña -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-gray-500"></i>
                            Contraseña <?php echo $editing ? '' : '*'; ?>
                        </label>
                        <input type="password" id="password" name="password" 
                               <?php echo $editing ? '' : 'required'; ?>
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors">
                        <p class="text-xs text-gray-500 mt-1">
                            <?php echo $editing ? 'Dejar en blanco para mantener la actual' : 'Mínimo 6 caracteres'; ?>
                        </p>
                    </div>

                    <!-- Confirmar Contraseña -->
                    <div>
                        <label for="confirm_password" class="block text-sm font-medium text-gray-700 mb-2">
                            <i class="fas fa-lock mr-1 text-gray-500"></i>
                            Confirmar Contraseña <?php echo $editing ? '' : '*'; ?>
                        </label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               <?php echo $editing ? '' : 'required'; ?>
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-gray-500 focus:border-transparent transition-colors">
                    </div>
                </div>

                <!-- Estado Activo -->
                <div class="mt-6">
                    <div class="flex items-center">
                        <input type="checkbox" id="activo" name="activo" 
                               <?php echo (!$editing || $usuario['activo']) ? 'checked' : ''; ?>
                               class="h-4 w-4 text-gray-600 focus:ring-gray-500 border-gray-300 rounded">
                        <label for="activo" class="ml-2 block text-sm text-gray-900">
                            <i class="fas fa-user-check mr-1 text-green-500"></i>
                            Usuario activo
                        </label>
                    </div>
                    <p class="text-xs text-gray-500 mt-1">Los usuarios inactivos no pueden iniciar sesión</p>
                </div>

                <!-- Botones -->
                <div class="mt-8 flex items-center justify-end space-x-4">
                    <a href="usuarios.php" 
                       class="px-6 py-3 border border-gray-300 rounded-lg text-gray-700 bg-white hover:bg-gray-50 font-medium transition-colors flex items-center">
                        <i class="fas fa-times mr-2"></i>
                        Cancelar
                    </a>
                    <button type="submit" 
                            class="px-6 py-3 bg-gray-700 hover:bg-gray-800 text-white rounded-lg font-medium transition-colors flex items-center">
                        <i class="fas fa-<?php echo $editing ? 'save' : 'plus'; ?> mr-2"></i>
                        <?php echo $editing ? 'Actualizar Usuario' : 'Crear Usuario'; ?>
                    </button>
                </div>
            </form>
        </div>
    </div>

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
    </script>
</body>
</html>
