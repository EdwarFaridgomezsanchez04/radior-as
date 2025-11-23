<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - RadioRías</title>
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
        
        .login-bg {
            background: linear-gradient(135deg, #20B2AA 0%, #00CED1 100%);
            min-height: 100vh;
            position: relative;
        }
        
        .login-bg::before {
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
        
        .login-container {
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
<body class="login-bg">
    <div class="floating-elements"></div>
    
    <div class="login-container flex items-center justify-center min-h-screen px-4 py-8">
        <div class="w-full max-w-md">
            <!-- Logo y título -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-20 h-20 bg-white rounded-full shadow-2xl mb-6">
                    <img src="assets/images/radiomorrazo-logo.png" alt="RadioRías" class="w-12 h-12 rounded-full">
                </div>
                <h1 class="text-4xl font-black text-white mb-2">RadioRías</h1>
                <p class="text-white text-opacity-80 text-lg">Panel de Administración</p>
            </div>
            
            <!-- Formulario de login -->
            <div class="glass-effect rounded-3xl p-8 shadow-2xl">
                <h2 class="text-2xl font-bold text-white text-center mb-6">Iniciar Sesión</h2>
                
                
                <form method="POST" action="includes/inicio.php" class="space-y-6">
                    <!-- Campo de usuario -->
                    <div>
                        <label for="username" class="block text-white text-sm font-bold mb-2">
                            <i class="fas fa-user mr-2"></i>Usuario o Email
                        </label>
                        <input 
                            type="text" 
                            id="username" 
                            name="username" 
                            required
                            class="input-focus w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-300"
                            placeholder="Ingresa tu usuario o email"
                            value="admin"
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
                                class="input-focus w-full px-4 py-3 bg-white bg-opacity-20 border border-white border-opacity-30 rounded-xl text-white placeholder-white placeholder-opacity-70 focus:outline-none focus:ring-2 focus:ring-white focus:ring-opacity-50 transition-all duration-300 pr-12"
                                placeholder="Ingresa tu contraseña"
                            >
                            <button 
                                type="button" 
                                onclick="togglePassword()" 
                                class="absolute right-3 top-1/2 transform -translate-y-1/2 text-white text-opacity-70 hover:text-opacity-100 transition-all duration-300"
                            >
                                <i id="password-icon" class="fas fa-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <!-- Mensaje de error -->
                    <div id="error-message" class="hidden bg-red-500 bg-opacity-20 border border-red-400 text-red-100 px-4 py-3 rounded-xl flex items-center">
                        <i class="fas fa-exclamation-circle mr-3"></i>
                        <span id="error-text"></span>
                    </div>
                    
                    <!-- Mensaje de éxito -->
                    <div id="success-message" class="hidden bg-green-500 bg-opacity-20 border border-green-400 text-green-100 px-4 py-3 rounded-xl flex items-center">
                        <i class="fas fa-check-circle mr-3"></i>
                        <span id="success-text">Validación correcta, enviando...</span>
                    </div>
                    
                    <!-- Botón de login -->
                    <button 
                        type="submit" 
                        name="login"
                        id="submit-btn"
                        class="btn-hover w-full bg-white text-radio-teal font-bold py-3 px-6 rounded-xl transition-all duration-300 flex items-center justify-center space-x-2 hover:bg-opacity-90 mt-4"
                    >
                        <i class="fas fa-sign-in-alt"></i>
                        <span>Iniciar Sesión</span>
                    </button>
                </form>
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
        // Función para mostrar/ocultar contraseña
        function togglePassword() {
            const passwordInput = document.getElementById('password');
            const passwordIcon = document.getElementById('password-icon');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                passwordIcon.className = 'fas fa-eye-slash';
            } else {
                passwordInput.type = 'password';
                passwordIcon.className = 'fas fa-eye';
            }
        }
        
        // Función para mostrar mensaje de error
        function showError(message) {
            const errorDiv = document.getElementById('error-message');
            const errorText = document.getElementById('error-text');
            const successDiv = document.getElementById('success-message');
            
            errorText.textContent = message;
            errorDiv.classList.remove('hidden');
            successDiv.classList.add('hidden');
        }
        
        // Función para mostrar mensaje de éxito
        function showSuccess(message) {
            const successDiv = document.getElementById('success-message');
            const successText = document.getElementById('success-text');
            const errorDiv = document.getElementById('error-message');
            
            successText.textContent = message;
            successDiv.classList.remove('hidden');
            errorDiv.classList.add('hidden');
        }
        
        // Función para ocultar todos los mensajes
        function hideMessages() {
            document.getElementById('error-message').classList.add('hidden');
            document.getElementById('success-message').classList.add('hidden');
        }
        
        // Función para validar con AJAX
        function validateAndLogin(event) {
            event.preventDefault();
            
            const form = document.querySelector('form');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            const submitBtn = document.getElementById('submit-btn');
            const username = usernameInput.value.trim();
            const password = passwordInput.value.trim();
            
            // Ocultar mensajes anteriores
            hideMessages();
            
            // Validación básica del lado del cliente
            if (username === '') {
                showError('Por favor, ingresa tu usuario o email');
                usernameInput.classList.add('border-red-400');
                usernameInput.classList.remove('border-white');
                return false;
            }
            
            if (password === '') {
                showError('Por favor, ingresa tu contraseña');
                passwordInput.classList.add('border-red-400');
                passwordInput.classList.remove('border-white');
                return false;
            }
            
            // Restaurar bordes
            usernameInput.classList.remove('border-red-400');
            usernameInput.classList.add('border-white');
            passwordInput.classList.remove('border-red-400');
            passwordInput.classList.add('border-white');
            
            // Mostrar loading
            submitBtn.disabled = true;
            submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i><span>Validando...</span>';
            
            // Crear FormData
            const formData = new FormData();
            formData.append('username', username);
            formData.append('password', password);
            formData.append('login', '1');
            
            // Petición AJAX
            fetch('includes/inicio.php', {
                method: 'POST',
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Iniciar Sesión</span>';
                
                if (data.success) {
                    // Login exitoso
                    showSuccess('¡Bienvenido! Redirigiendo...');
                    
                    // Redirigir después de 1 segundo
                    setTimeout(() => {
                        if (data.redirect) {
                            window.location.href = data.redirect;
                        } else {
                            window.location.href = 'admin/dashboard.php';
                        }
                    }, 1000);
                } else {
                    // Error en el login
                    showError(data.message || 'Usuario o contraseña incorrectos');
                    
                    // Efecto de shake en el formulario
                    form.style.animation = 'shake 0.5s';
                    setTimeout(() => {
                        form.style.animation = '';
                    }, 500);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                submitBtn.disabled = false;
                submitBtn.innerHTML = '<i class="fas fa-sign-in-alt"></i><span>Iniciar Sesión</span>';
                showError('Error de conexión. Por favor, intenta de nuevo.');
            });
            
            return false;
        }
        
        // Validación en tiempo real
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            const usernameInput = document.getElementById('username');
            const passwordInput = document.getElementById('password');
            
            // Limpiar errores cuando el usuario escribe
            usernameInput.addEventListener('input', function() {
                hideMessages();
                if (this.value.trim().length > 0) {
                    this.classList.remove('border-red-400');
                    this.classList.add('border-white');
                }
            });
            
            passwordInput.addEventListener('input', function() {
                hideMessages();
                if (this.value.length > 0) {
                    this.classList.remove('border-red-400');
                    this.classList.add('border-white');
                }
            });
            
            // Agregar evento al formulario
            form.addEventListener('submit', validateAndLogin);
        });
        
        // Agregar animación de shake
        const style = document.createElement('style');
        style.textContent = `
            @keyframes shake {
                0%, 100% { transform: translateX(0); }
                25% { transform: translateX(-10px); }
                75% { transform: translateX(10px); }
            }
        `;
        document.head.appendChild(style);
    </script>
</body>
</html>