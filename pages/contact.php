<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contacto - Radio Morrazo</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Added Tailwind config with custom colors -->
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

<!-- Contact Header -->
<section class="relative py-20 bg-gradient-to-br from-radio-teal via-radio-cyan to-teal-600 overflow-hidden" style="background-image: url('assets/images/morrazo-beach.png'); background-size: cover; background-position: center; background-blend-mode: multiply;">
    <!-- Dark overlay for better text contrast -->
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    
    <!-- Animated background elements -->
    <div class="absolute inset-0">
        <div class="absolute top-10 sm:top-20 left-5 sm:left-10 w-16 sm:w-32 h-16 sm:h-32 bg-white bg-opacity-10 rounded-full animate-pulse"></div>
        <div class="absolute top-20 sm:top-40 right-10 sm:right-20 w-12 sm:w-24 h-12 sm:h-24 bg-white bg-opacity-5 rounded-full animate-bounce"></div>
        <div class="absolute bottom-16 sm:bottom-32 left-1/4 w-8 sm:w-16 h-8 sm:h-16 bg-white bg-opacity-15 rounded-full animate-ping"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl font-black mb-4 sm:mb-6 leading-none">
            <span class="text-white drop-shadow-2xl"><?php echo $t['contact']['page_title']; ?></span>
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            <?php echo $t['contact']['page_subtitle']; ?>
        </p>
    </div>
</section>

<!-- Contact Information -->
<section id="informacion" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-slate-50 via-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-12 sm:gap-16">
            <!-- Contact Form -->
            <div class="bg-white p-8 rounded-3xl shadow-xl">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-800 mb-6 sm:mb-8">
                    <?php echo $t['contact']['send_message']; ?>
                </h2>
                
                <form id="contact-form" action="#" method="POST" class="space-y-6 sm:space-y-8">
                    <div class="grid md:grid-cols-2 gap-6 sm:gap-8">
                        <div>
                            <label for="name" class="block text-sm sm:text-base font-bold text-slate-700 mb-2 sm:mb-3">
                                <?php echo $t['contact']['your_name']; ?>
                            </label>
                            <input type="text" id="name" name="name" required 
                                   class="w-full px-4 sm:px-6 py-3 sm:py-4 border-2 border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-radio-teal focus:border-radio-teal transition-all duration-300 text-sm sm:text-base"
                                   placeholder="Tu nombre completo">
                        </div>
                        <div>
                            <label for="email" class="block text-sm sm:text-base font-bold text-slate-700 mb-2 sm:mb-3">
                                <?php echo $t['contact']['your_email']; ?>
                            </label>
                            <input type="email" id="email" name="email" required 
                                   class="w-full px-4 sm:px-6 py-3 sm:py-4 border-2 border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-radio-teal focus:border-radio-teal transition-all duration-300 text-sm sm:text-base"
                                   placeholder="tu@email.com">
                        </div>
                    </div>
                    
                    <div>
                        <label for="subject" class="block text-sm sm:text-base font-bold text-slate-700 mb-2 sm:mb-3">
                            <?php echo $t['contact']['subject']; ?>
                        </label>
                        <input type="text" id="subject" name="subject" required 
                               class="w-full px-4 sm:px-6 py-3 sm:py-4 border-2 border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-radio-teal focus:border-radio-teal transition-all duration-300 text-sm sm:text-base"
                               placeholder="Asunto de tu mensaje">
                    </div>
                    
                    <div>
                        <label for="message" class="block text-sm sm:text-base font-bold text-slate-700 mb-2 sm:mb-3">
                            <?php echo $t['contact']['message']; ?>
                        </label>
                        <textarea id="message" name="message" rows="6" required 
                                  class="w-full px-4 sm:px-6 py-3 sm:py-4 border-2 border-slate-300 rounded-xl focus:outline-none focus:ring-2 focus:ring-radio-teal focus:border-radio-teal transition-all duration-300 resize-none text-sm sm:text-base"
                                  placeholder="Escribe tu mensaje aquí..."></textarea>
                    </div>
                    
                    <button type="submit" class="w-full bg-gradient-to-r from-radio-teal to-radio-cyan hover:from-radio-cyan hover:to-radio-teal text-white px-8 py-3 sm:py-4 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105">
                        <i class="fas fa-paper-plane mr-2"></i>
                        <?php echo $t['contact']['send']; ?>
                    </button>
                </form>
            </div>
            
            <!-- Contact Information -->
            <div class="bg-white p-8 rounded-3xl shadow-xl">
                <h2 class="text-2xl sm:text-3xl lg:text-4xl font-black text-slate-800 mb-6 sm:mb-8">
                    <?php echo $t['contact']['get_in_touch']; ?>
                </h2>
                
                <div class="space-y-6 sm:space-y-8">
                    <!-- Address -->
                    <div class="group bg-gray-50 p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-lg hover:shadow-xl transition-all duration-500 transform hover:-translate-y-1 hover:bg-white">
                        <div class="flex items-start space-x-4 sm:space-x-6">
                            <div class="w-12 sm:w-14 h-12 sm:h-14 bg-gradient-to-br from-radio-teal to-radio-cyan rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-map-marker-alt text-white text-lg sm:text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-2 sm:mb-3"><?php echo $t['contact']['address']; ?></h3>
                                <p class="text-slate-600 text-sm sm:text-base"><?php echo SITE_ADDRESS; ?></p>
                                <p class="text-slate-500 text-xs sm:text-sm mt-2">Galicia, España</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Phone -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4 sm:space-x-6">
                            <div class="w-12 sm:w-14 h-12 sm:h-14 bg-gradient-to-br from-purple-600 to-pink-600 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-phone text-white text-lg sm:text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-2 sm:mb-3"><?php echo $t['contact']['phone']; ?></h3>
                                <p class="text-slate-600 text-sm sm:text-base"><?php echo SITE_PHONE; ?></p>
                                <p class="text-slate-500 text-xs sm:text-sm mt-2">Lunes a Viernes: 9:00 - 18:00</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Email -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                        <div class="flex items-start space-x-4 sm:space-x-6">
                            <div class="w-12 sm:w-14 h-12 sm:h-14 bg-gradient-to-br from-orange-500 to-red-500 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0 group-hover:scale-110 transition-transform duration-300">
                                <i class="fas fa-envelope text-white text-lg sm:text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-2 sm:mb-3"><?php echo $t['contact']['email']; ?></h3>
                                <p class="text-slate-600 text-sm sm:text-base"><?php echo SITE_EMAIL; ?></p>
                                <p class="text-slate-500 text-xs sm:text-sm mt-2">Respuesta en 24 horas</p>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Social Media -->
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                        <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-4 sm:mb-6"><?php echo $t['contact']['social_media']; ?></h3>
                        <div class="flex space-x-3 sm:space-x-4">
                            <a href="https://www.facebook.com/rmorrazo" target="_blank" class="w-12 sm:w-14 h-12 sm:h-14 bg-gradient-to-br from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-110">
                                <i class="fab fa-facebook text-white text-lg sm:text-xl"></i>
                            </a>
                            <a href="https://x.com/RadioArredemo" target="_blank" class="w-12 sm:w-14 h-12 sm:h-14 bg-gradient-to-br from-sky-500 to-sky-600 hover:from-sky-600 hover:to-sky-700 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-110">
                                <i class="fab fa-twitter text-white text-lg sm:text-xl"></i>
                            </a>
                            <a href="https://www.instagram.com/radiomorrazo/" target="_blank" class="w-12 sm:w-14 h-12 sm:h-14 bg-gradient-to-br from-pink-600 to-pink-700 hover:from-pink-700 hover:to-pink-800 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-110">
                                <i class="fab fa-instagram text-white text-lg sm:text-xl"></i>
                            </a>
                            <a href="https://www.youtube.com/@radiomorrazo7682" target="_blank" class="w-12 sm:w-14 h-12 sm:h-14 bg-gradient-to-br from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 rounded-xl flex items-center justify-center transition-all duration-300 shadow-lg hover:shadow-xl transform hover:scale-110">
                                <i class="fab fa-youtube text-white text-lg sm:text-xl"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Map Section -->
<section id="mapa" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-slate-900 via-gray-900 to-black text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black mb-4 sm:mb-6">
                Encuéntranos
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-300 max-w-2xl mx-auto px-4">
                Estamos ubicados en el corazón de la península de Morrazo
            </p>
        </div>
        
        <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-2">
            <!-- Mapa placeholder (visible por defecto) -->
            <div id="map-placeholder" class="h-96 sm:h-[500px] bg-gradient-to-br from-radio-teal to-radio-cyan flex items-center justify-center relative overflow-hidden">
                <div class="text-center text-white">
                    <div class="w-24 sm:w-32 h-24 sm:h-32 bg-white bg-opacity-20 backdrop-blur-lg rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 shadow-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-map-marked-alt text-4xl sm:text-5xl text-white"></i>
                    </div>
                    <h3 class="text-2xl sm:text-3xl lg:text-4xl font-bold mb-4 sm:mb-6">Mapa Interactivo</h3>
                    <p class="text-white text-opacity-90 mb-6 sm:mb-8 text-sm sm:text-base lg:text-lg max-w-md mx-auto">
                        Aquí se mostraría un mapa interactivo de nuestra ubicación
                    </p>
                    <button id="show-map-btn" class="bg-white text-radio-teal px-6 sm:px-8 py-3 sm:py-4 rounded-xl font-bold text-sm sm:text-base shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300">
                        <i class="fas fa-directions mr-2"></i>
                        Cómo Llegar
                    </button>
                </div>
            </div>
            
            <!-- Mapa interactivo (oculto por defecto) -->
            <div id="map-container" class="h-96 sm:h-[500px] relative hidden">
                <!-- Botón de cerrar -->
                <button id="close-map-btn" class="absolute top-4 right-4 z-10 bg-white bg-opacity-90 hover:bg-opacity-100 text-gray-800 hover:text-red-600 w-10 h-10 rounded-full flex items-center justify-center shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-110">
                    <i class="fas fa-times text-lg"></i>
                </button>
                
                <!-- Mapa de Google Maps embebido -->
                <iframe 
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3037.1234567890!2d-8.7890123456789!3d42.1234567890!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zNDLCsDA3JzI0LjQiTiA4wrA0NycyMC40Ilc!5e0!3m2!1ses!2ses!4v1234567890123!5m2!1ses!2ses&q=Rúa+Uruguai,+18,+36201+Vigo,+Pontevedra,+Galicia"
                    width="100%" 
                    height="100%" 
                    style="border:0;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade"
                    class="rounded-2xl sm:rounded-3xl">
                </iframe>
            </div>
        </div>
    </div>
</section>

<style>
/* Asegurar que los campos del formulario sean completamente funcionales */
#contact-form input,
#contact-form textarea {
    -webkit-user-select: text !important;
    -moz-user-select: text !important;
    -ms-user-select: text !important;
    user-select: text !important;
    pointer-events: auto !important;
    cursor: text !important;
}

/* Asegurar que no haya elementos que bloqueen la interacción */
#contact-form * {
    pointer-events: auto !important;
}

#contact-form input,
#contact-form textarea {
    position: relative !important;
    z-index: 10 !important;
}
</style>
a
<script>
// Funcionalidad del mapa interactivo
document.addEventListener('DOMContentLoaded', function() {
    const showMapBtn = document.getElementById('show-map-btn');
    const closeMapBtn = document.getElementById('close-map-btn');
    const mapPlaceholder = document.getElementById('map-placeholder');
    const mapContainer = document.getElementById('map-container');

    // Mostrar mapa
    showMapBtn.addEventListener('click', function() {
        mapPlaceholder.classList.add('hidden');
        mapContainer.classList.remove('hidden');
        mapContainer.classList.add('block');
    });

    // Ocultar mapa
    closeMapBtn.addEventListener('click', function() {
        mapContainer.classList.add('hidden');
        mapContainer.classList.remove('block');
        mapPlaceholder.classList.remove('hidden');
    });

    // Asegurar que los campos del formulario funcionen correctamente
    const formInputs = document.querySelectorAll('#contact-form input, #contact-form textarea');
    
    console.log('Campos encontrados:', formInputs.length);
    
    formInputs.forEach((input, index) => {
        console.log(`Campo ${index + 1}:`, input.type, input.name, input.id);
        
        // Asegurar que el campo sea completamente funcional
        input.removeAttribute('readonly');
        input.removeAttribute('disabled');
        input.setAttribute('tabindex', '0');
        
        // Event listeners para focus y hover
        input.addEventListener('focus', function() {
            this.style.borderColor = '#14b8a6';
            this.style.boxShadow = '0 0 0 3px rgba(20, 184, 166, 0.1)';
            console.log('Campo enfocado:', this.name);
        });
        
        input.addEventListener('blur', function() {
            if (!this.value) {
                this.style.borderColor = '#cbd5e1';
                this.style.boxShadow = 'none';
            }
        });
        
        input.addEventListener('mouseenter', function() {
            this.style.borderColor = '#94a3b8';
        });
        
        input.addEventListener('mouseleave', function() {
            if (document.activeElement !== this) {
                this.style.borderColor = '#cbd5e1';
            }
        });
        
        // Asegurar que el campo responda a clics
        input.addEventListener('click', function(e) {
            e.stopPropagation();
            this.focus();
        });
        
        // Test de funcionalidad
        input.addEventListener('input', function() {
            console.log('Texto ingresado en', this.name, ':', this.value);
        });
    });
});
</script>

</body>
</html>
