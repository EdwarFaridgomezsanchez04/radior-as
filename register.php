<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro - RadioRías</title>
    <?php include('includes/favicon.php'); ?>
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
    <style>
        .glass-effect {
            backdrop-filter: blur(10px);
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }
        
        .register-bg {
            background: linear-gradient(135deg, #20B2AA 0%, #00CED1 100%);
            min-height: 100vh;
            position: relative;
        }
        
        .register-bg::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-image: url('assets/images/morrazo-beach.png');
            background-size: cover;
            background-position: center;
            opacity: 0.3;
            z-index: 1;
        }
        
        .register-container {
            position: relative;
            z-index: 2;
        }
        
        .floating-elements {
            position: absolute;
            width: 100%;
            height: 100%;
            overflow: hidden;
            z-index: 1;
        }
        
        .floating-elements::before,
        .floating-elements::after {
            content: '';
            position: absolute;
            border-radius: 50%;
            background: rgba(255, 255, 255, 0.1);
            animation: float 6s ease-in-out infinite;
        }
        
        .floating-elements::before {
            width: 100px;
            height: 100px;
            top: 20%;
            left: 10%;
            animation-delay: 0s;
        }
        
        .floating-elements::after {
            width: 150px;
            height: 150px;
            top: 60%;
            right: 10%;
            animation-delay: 3s;
        }
        
        @keyframes float {
            0%, 100% { transform: translateY(0px) rotate(0deg); }
            50% { transform: translateY(-20px) rotate(180deg); }
        }
        
        .input-focus:focus {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(32, 178, 170, 0.3);
        }
        
        .btn-hover:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
        }
    </style>
</head>
<body class="register-bg">
    <div class="floating-elements"></div>
    
    <div class="register-container flex items-center justify-center min-h-screen px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Logo y título -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-2xl mb-6">
                    <img src="assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-12 h-12 rounded-full">
                </div>
                <h1 class="text-4xl font-black text-white mb-2">RadioRías</h1>
                <p class="text-white text-opacity-80 text-lg">Crear Nueva Cuenta</p>
            </div>
            
            <!-- Formulario de registro -->
            <div class="glass-effect rounded-3xl p-8 shadow-2xl">
                <h2 class="text-2xl font-bold text-white text-center mb-6">Registro de Usuario</h2>
                
                <form method="POST" action="includes/inicio.php" class="space-y-6">
                    <!-- Campo de usuario -->
                    <div>
                        <label for="username" class="block text-white text-sm font-bold mb-2">
                            <i class="fas fa-user mr-2"></i>Nombre de Usuario
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="input-focus w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-300"
                            placeholder="Ingresa tu nombre de usuario"
                        >
                    </div>
                    
                    <!-- Campo de nombre completo -->
                    <div>
                        <label for="nombre" class="block text-white text-sm font-bold mb-2">
                            <i class="fas fa-id-card mr-2"></i>Nombre Completo
                        </label>
                        <input 
                            type="text" 
                            id="nombre" 
                            name="nombre" 
                            required
                            class="input-focus w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-300"
                            placeholder="Ingresa tu nombre completo"
                        >
                    </div>
                    
                    <!-- Campo de email -->
                    <div>
                        <label for="email" class="block text-white text-sm font-bold mb-2">
                            <i class="fas fa-envelope mr-2"></i>Correo Electrónico
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="input-focus w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-300"
                            placeholder="Ingresa tu correo electrónico"
                        >
                    </div>
                    
                    <!-- Campo de contraseña -->
                    <div>
                        <label for="password" class="block text-white text-sm font-bold mb-2">
                            <i class="fas fa-lock mr-2"></i>Contraseña
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="password" 
                                name="password" 
                                required
                                minlength="6"
                                class="input-focus w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-300 pr-12"
                                placeholder="Ingresa tu contraseña"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('password')" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white text-opacity-70 hover:text-opacity-100 transition-all duration-300"
                            >
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Campo de confirmar contraseña -->
                    <div>
                        <label for="confirm_password" class="block text-white text-sm font-bold mb-2">
                            <i class="fas fa-lock mr-2"></i>Confirmar Contraseña
                        </label>
                        <div class="relative">
                            <input 
                                type="password" 
                                id="confirm_password" 
                                name="confirm_password" 
                                required
                                minlength="6"
                                class="input-focus w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-300 pr-12"
                                placeholder="Confirma tu contraseña"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword('confirm_password')" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white text-opacity-70 hover:text-opacity-100 transition-all duration-300"
                            >
                                <i id="confirm_password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Términos y condiciones -->
                    <div class="flex items-center">
                        <input 
                            type="checkbox" 
                            id="terms" 
                            name="terms" 
                            required
                            class="mr-3 rounded border-white border-opacity-30 bg-white bg-opacity-20 text-radio-teal focus:ring-white focus:ring-opacity-50"
                        >
                        <label for="terms" class="text-white text-sm">
                            Acepto los términos y condiciones de uso
                        </label>
                    </div>
                    
                    <!-- Botón de registro -->
                    <button 
                        type="submit" 
                        name="register"
                        class="btn-hover w-full bg-white text-radio-teal font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 hover:bg-opacity-90"
                    >
                        <i class="fas fa-user-plus"></i>
                        <span>Crear Cuenta</span>
                    </button>
                </form>
                
                <!-- Información adicional -->
                <div class="mt-8 pt-6 border-t border-white border-opacity-20 text-center">
                    <p class="text-white text-opacity-70 text-sm mb-4">
                        Información de registro
                    </p>
                    <div class="bg-white bg-opacity-10 rounded-xl p-4 text-left">
                        <p class="text-white text-sm"><strong>Rol:</strong> Editor (por defecto)</p>
                        <p class="text-white text-sm"><strong>Estado:</strong> Activo</p>
                        <p class="text-white text-sm"><strong>Contraseña:</strong> Mínimo 6 caracteres</p>
                    </div>
                    <div class="mt-4">
                        <a href="login.php" class="text-yellow-300 hover:text-yellow-200 text-sm font-bold transition-all duration-300">
                            ¿Ya tienes cuenta? Inicia sesión aquí
                        </a>
                    </div>
                </div>
            </div>
            
            <!-- Volver al sitio -->
            <div class="text-center mt-6">
                <a href="index.php" class="text-white text-opacity-80 hover:text-opacity-100 transition-all duration-300 flex items-center justify-center space-x-2">
                    <i class="fas fa-arrow-left"></i>
                    <span>Volver al sitio web</span>
                </a>
            </div>
        </div>
    </div>
    
    <script>
        function togglePassword(fieldId) {
            const passwordInput = document.getElementById(fieldId);
            const passwordIcon = document.getElementById(fieldId + '-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }
        
        // Validar que las contraseñas coincidan
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const confirmPassword = this.value;
            
            if (password !== confirmPassword) {
                this.setCustomValidity('Las contraseñas no coinciden');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password').value;
            const confirmPasswordField = document.getElementById('confirm_password');
            
            if (confirmPassword && this.value !== confirmPassword) {
                confirmPasswordField.setCustomValidity('Las contraseñas no coinciden');
            } else {
                confirmPasswordField.setCustomValidity('');
            }
        });
    </script>
</body>
</html>
