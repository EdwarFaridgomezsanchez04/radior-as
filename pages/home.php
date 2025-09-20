
<!-- Modern Hero Section -->
<section class="relative min-h-screen bg-gradient-to-br from-radio-teal via-radio-cyan to-teal-600 overflow-hidden" style="background-image: url('assets/images/morrazo-beach.png'); background-size: cover; background-position: center; background-blend-mode: multiply;">
    <!-- Dark overlay for better text contrast -->
    <div class="absolute inset-0 bg-black bg-opacity-40"></div>
    <!-- Animated background elements -->
    <div class="absolute inset-0">
        <div class="absolute top-10 sm:top-20 left-5 sm:left-10 w-16 sm:w-32 h-16 sm:h-32 bg-white bg-opacity-10 rounded-full animate-pulse"></div>
        <div class="absolute top-20 sm:top-40 right-10 sm:right-20 w-12 sm:w-24 h-12 sm:h-24 bg-white bg-opacity-5 rounded-full animate-bounce"></div>
        <div class="absolute bottom-16 sm:bottom-32 left-1/4 w-8 sm:w-16 h-8 sm:h-16 bg-white bg-opacity-15 rounded-full animate-ping"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 flex items-center justify-center min-h-screen px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-6xl mx-auto">
            <!-- Live Badge -->
            <div class="inline-flex items-center bg-red-500 text-white px-4 sm:px-8 py-2 sm:py-3 rounded-full font-bold text-xs sm:text-sm mb-6 sm:mb-8 shadow-2xl animate-pulse">
                <div class="w-2 sm:w-3 h-2 sm:h-3 bg-white rounded-full mr-2 sm:mr-3 animate-ping"></div>
                EN VIVO
            </div>
            
            <!-- Main Title -->
            <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl font-black mb-4 sm:mb-6 leading-none">
                <span class="text-white drop-shadow-2xl">Radio</span>
                <br>
                <span class="text-yellow-300 drop-shadow-2xl">Rías</span>
            </h1>
            
            <!-- Subtitle -->
            <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
                <?php echo $t['home']['hero_subtitle']; ?>
            </p>
            
            <!-- Description -->
            <p class="text-sm sm:text-base md:text-lg lg:text-xl text-white text-opacity-90 mb-8 sm:mb-12 max-w-4xl mx-auto leading-relaxed drop-shadow-md px-4">
                <?php echo $t['home']['hero_description']; ?>
            </p>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center mb-12 sm:mb-16 px-4">
                <button onclick="showRadioWidget()" class="group bg-white text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                    <i class="fas fa-play mr-2 sm:mr-3 group-hover:scale-110 transition-transform"></i>
                    <?php echo $t['home']['listen_live']; ?>
                </button>
                <button class="group border-2 sm:border-3 border-white text-white hover:bg-white hover:text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                    <i class="fas fa-compass mr-2 sm:mr-3 group-hover:rotate-180 transition-transform duration-500"></i>
                    <?php echo $t['home']['discover_more']; ?>
                </button>
            </div>
            
            <!-- Modern Radio Wave Animation -->
            <div class="flex justify-center">
                <div class="relative">
                    <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-broadcast-tower text-lg sm:text-2xl text-white"></i>
                    </div>
                    <!-- Animated waves -->
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-24 sm:w-32 h-24 sm:h-32 border-2 border-white border-opacity-30 rounded-full animate-ping"></div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-36 sm:w-48 h-36 sm:h-48 border-2 border-white border-opacity-20 rounded-full animate-ping" style="animation-delay: 1s;"></div>
                    </div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <div class="w-48 sm:w-64 h-48 sm:h-64 border-2 border-white border-opacity-10 rounded-full animate-ping" style="animation-delay: 2s;"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-4 sm:bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
        <i class="fas fa-chevron-down text-xl sm:text-2xl"></i>
    </div>
</section>

<!-- Modern News Section -->
<!-- <section class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo $t['home']['latest_news']; ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                Mantente al día con todo lo que acontece en nuestra comunidad
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8"> -->
            <!-- News Card 1 -->
            <!-- <article class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 sm:hover:-translate-y-4">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-radio-teal to-radio-cyan relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-music text-4xl sm:text-6xl text-white group-hover:scale-125 transition-transform duration-500"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="inline-block bg-radio-teal bg-opacity-10 text-radio-teal px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold mb-3 sm:mb-4">
                        EVENTOS
                    </span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-3 sm:mb-4 group-hover:text-radio-teal transition-colors">
                        Festival de Música Tradicional
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 leading-relaxed">
                        Únete a nosotros en una celebración única de la música tradicional gallega con artistas locales.
                    </p>
                    <a href="#" class="inline-flex items-center text-radio-teal hover:text-radio-cyan font-bold text-sm sm:text-base group-hover:translate-x-2 transition-all">
                        Leer más <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </article> -->

            <!-- News Card 2 -->
            <!-- <article class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 sm:hover:-translate-y-4">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-purple-500 to-pink-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-podcast text-4xl sm:text-6xl text-white group-hover:scale-125 transition-transform duration-500"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="inline-block bg-purple-100 text-purple-700 px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold mb-3 sm:mb-4">
                        PODCASTS
                    </span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-3 sm:mb-4 group-hover:text-purple-600 transition-colors">
                        Nuevo Podcast Semanal
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 leading-relaxed">
                        Descubre nuestro nuevo formato de podcast con entrevistas exclusivas y contenido especial.
                    </p>
                    <a href="#" class="inline-flex items-center text-purple-600 hover:text-purple-700 font-bold text-sm sm:text-base group-hover:translate-x-2 transition-all">
                        Leer más <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </article> -->

            <!-- News Card 3 -->
            <!-- <article class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 sm:hover:-translate-y-4 sm:col-span-2 lg:col-span-1">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-orange-400 to-red-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <i class="fas fa-microphone text-4xl sm:text-6xl text-white group-hover:scale-125 transition-transform duration-500"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="inline-block bg-orange-100 text-orange-700 px-3 sm:px-4 py-1 sm:py-2 rounded-full text-xs sm:text-sm font-bold mb-3 sm:mb-4">
                        ENTREVISTAS
                    </span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-gray-900 mb-3 sm:mb-4 group-hover:text-orange-600 transition-colors">
                        Entrevista Especial
                    </h3>
                    <p class="text-sm sm:text-base text-gray-600 mb-4 sm:mb-6 leading-relaxed">
                        Una conversación íntima con personalidades destacadas de nuestra región.
                    </p>
                    <a href="#" class="inline-flex items-center text-orange-600 hover:text-orange-700 font-bold text-sm sm:text-base group-hover:translate-x-2 transition-all">
                        Leer más <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div>
            </article>
        </div>
    </div>
</section> -->

<!-- Modern Programs Section -->
<!-- <section class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-radio-teal to-radio-cyan">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-white mb-4 sm:mb-6">
                Programas <span class="text-yellow-300">Destacados</span>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-yellow-300 mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-white text-opacity-90 max-w-2xl mx-auto px-4">
                Los programas más populares de nuestra parrilla radiofónica
            </p>
        </div> -->
        
        <!-- <div class="grid lg:grid-cols-2 gap-8 lg:gap-16 items-center">
            <div class="space-y-6 sm:space-y-8 order-2 lg:order-1">
                <!-- Program 1 -->
                <!-- <div class="group bg-white bg-opacity-10 backdrop-blur-lg p-6 sm:p-8 rounded-2xl sm:rounded-3xl border border-white border-opacity-20 hover:bg-opacity-20 transition-all duration-300">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-3 sm:gap-0">
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">Buenos Días Morrazo</h3>
                        <span class="bg-yellow-300 text-radio-dark px-4 sm:px-6 py-2 sm:py-3 rounded-full font-bold text-sm flex items-center w-fit">
                            <i class="fas fa-sun mr-2"></i>07:00 - 10:00
                        </span>
                    </div>
                    <p class="text-white text-opacity-90 mb-4 sm:mb-6 text-sm sm:text-base lg:text-lg leading-relaxed">
                        Tu compañía perfecta para comenzar el día con energía, noticias locales y la mejor música.
                    </p>
                    <a href="#" class="inline-flex items-center text-yellow-300 hover:text-yellow-200 font-bold text-sm sm:text-base lg:text-lg group-hover:translate-x-2 transition-all">
                        Ver programa <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div> --> 

                <!-- Program 2 -->
                <!-- <div class="group bg-white bg-opacity-10 backdrop-blur-lg p-6 sm:p-8 rounded-2xl sm:rounded-3xl border border-white border-opacity-20 hover:bg-opacity-20 transition-all duration-300">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-3 sm:gap-0">
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">Cultura Galega</h3>
                        <span class="bg-yellow-300 text-radio-dark px-4 sm:px-6 py-2 sm:py-3 rounded-full font-bold text-sm flex items-center w-fit">
                            <i class="fas fa-heart mr-2"></i>15:00 - 16:00
                        </span>
                    </div>
                    <p class="text-white text-opacity-90 mb-4 sm:mb-6 text-sm sm:text-base lg:text-lg leading-relaxed">
                        Exploramos nuestras raíces y tradiciones culturales con invitados especiales y música tradicional.
                    </p>
                    <a href="#" class="inline-flex items-center text-yellow-300 hover:text-yellow-200 font-bold text-sm sm:text-base lg:text-lg group-hover:translate-x-2 transition-all">
                        Ver programa <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div> -->

                <!-- Program 3 -->
                <!-- <!-- <div class="group bg-white bg-opacity-10 backdrop-blur-lg p-6 sm:p-8 rounded-2xl sm:rounded-3xl border border-white border-opacity-20 hover:bg-opacity-20 transition-all duration-300">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between mb-4 sm:mb-6 gap-3 sm:gap-0">
                        <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-white">Noches de Radio</h3>
                        <span class="bg-yellow-300 text-radio-dark px-4 sm:px-6 py-2 sm:py-3 rounded-full font-bold text-sm flex items-center w-fit">
                            <i class="fas fa-moon mr-2"></i>21:00 - 23:00
                        </span>
                    </div>
                    <p class="text-white text-opacity-90 mb-4 sm:mb-6 text-sm sm:text-base lg:text-lg leading-relaxed">
                        El programa nocturno perfecto con música relajante y conversaciones íntimas.
                    </p>
                    <a href="#" class="inline-flex items-center text-yellow-300 hover:text-yellow-200 font-bold text-sm sm:text-base lg:text-lg group-hover:translate-x-2 transition-all">
                        Ver programa <i class="fas fa-arrow-right ml-2"></i>
                    </a>
                </div> 
            </div>
             -->
            <!-- Visual Element -->
            <!-- <div class="text-center order-1 lg:order-2">
                <div class="relative">
                    <div class="w-64 sm:w-80 lg:w-96 h-64 sm:h-80 lg:h-96 bg-white bg-opacity-20 rounded-full flex items-center justify-center mx-auto backdrop-blur-lg border border-white border-opacity-30 shadow-2xl transform hover:scale-105 transition-transform duration-500">
                        <div class="w-48 sm:w-60 lg:w-72 h-48 sm:h-60 lg:h-72 bg-yellow-300 rounded-full flex items-center justify-center shadow-2xl">
                            <i class="fas fa-radio text-5xl sm:text-6xl lg:text-8xl text-radio-dark"></i>
                        </div>
                    </div>
                    <!--Floating elements -->
                    <!-- <div class="absolute -top-4 sm:-top-8 -right-4 sm:-right-8 w-12 sm:w-16 h-12 sm:h-16 bg-yellow-300 rounded-full animate-bounce flex items-center justify-center">
                        <i class="fas fa-music text-radio-dark text-base sm:text-xl"></i>
                    </div>
                    <div class="absolute -bottom-4 sm:-bottom-8 -left-4 sm:-left-8 w-10 sm:w-12 h-10 sm:h-12 bg-white rounded-full animate-bounce flex items-center justify-center" style="animation-delay: 0.5s;">
                        <i class="fas fa-heart text-radio-teal text-sm sm:text-base"></i>
                    </div>
                </div>
                <p class="text-white text-lg sm:text-xl lg:text-2xl font-bold mt-6 sm:mt-8">Escúchanos 24/7</p>
                <p class="text-white text-opacity-80 mt-2 text-sm sm:text-base lg:text-lg">Desde el corazón de Galicia</p>
            </div>
        </div>
    </div>
</section>  -->

<!-- Modern Contact Section -->
<section class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-gray-900 to-black text-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black mb-4 sm:mb-6">
                Únete a Nuestra <span class="text-radio-teal">Comunidad</span>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-300 max-w-2xl mx-auto px-4">
                Forma parte de la familia Radio Morrazo y mantente conectado
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Contact Card 1 -->
            <div class="group text-center p-6 sm:p-8 lg:p-10 bg-gradient-to-br from-radio-teal to-radio-cyan rounded-2xl sm:rounded-3xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-phone text-2xl sm:text-3xl text-radio-teal"></i>
                </div>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-3 sm:mb-4">Llámanos</h3>
                <p class="text-white text-opacity-90 mb-4 sm:mb-6 text-sm sm:text-base lg:text-lg">
                    ¿Tienes una petición musical o quieres participar en directo?
                </p>
                <a href="tel:+34662403918" class="text-yellow-300 hover:text-yellow-200 font-bold text-lg sm:text-xl">
                    +34 662 40 39 18
                </a>
            </div>

            <!-- Contact Card 2 -->
            <div class="group text-center p-6 sm:p-8 lg:p-10 bg-gradient-to-br from-purple-600 to-pink-600 rounded-2xl sm:rounded-3xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-share-alt text-2xl sm:text-3xl text-purple-600"></i>
                </div>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-3 sm:mb-4">Síguenos</h3>
                <p class="text-white text-opacity-90 mb-4 sm:mb-6 text-sm sm:text-base lg:text-lg">
                    Mantente al día con todas nuestras novedades
                </p>
                <div class="flex justify-center space-x-4 sm:space-x-6">
                    <a href="https://www.facebook.com/rmorrazo" target="_blank" class="text-yellow-300 hover:text-yellow-200 text-2xl sm:text-3xl transform hover:scale-125 transition-all">
                        <i class="fab fa-facebook"></i>
                    </a>
                    <a href="https://www.instagram.com/radiomorrazo/" target="_blank" class="text-yellow-300 hover:text-yellow-200 text-2xl sm:text-3xl transform hover:scale-125 transition-all">
                        <i class="fab fa-instagram"></i>
                    </a>
                    <a href="https://x.com/RadioArredemo" target="_blank" class="text-yellow-300 hover:text-yellow-200 text-2xl sm:text-3xl transform hover:scale-125 transition-all">
                        <i class="fab fa-twitter"></i>
                    </a>
                    <a href="https://www.youtube.com/@radiomorrazo7682" target="_blank" class="text-yellow-300 hover:text-yellow-200 text-2xl sm:text-3xl transform hover:scale-125 transition-all">
                        <i class="fab fa-youtube"></i>
                    </a>
                </div>
            </div>

            <!-- Contact Card 3 -->
            <div class="group text-center p-6 sm:p-8 lg:p-10 bg-gradient-to-br from-orange-500 to-red-500 rounded-2xl sm:rounded-3xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 sm:col-span-2 lg:col-span-1">
                <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-envelope text-2xl sm:text-3xl text-orange-500"></i>
                </div>
                <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold mb-3 sm:mb-4">Escríbenos</h3>
                <p class="text-white text-opacity-90 mb-4 sm:mb-6 text-sm sm:text-base lg:text-lg">
                    Comparte tus ideas, sugerencias o colaboraciones
                </p>
                <a href="mailto:contacto@radiorias.com" class="text-yellow-300 hover:text-yellow-200 font-bold text-sm sm:text-base lg:text-lg">
                    contacto@radiorias.com
                </a>
            </div>
        </div>
    </div>
</section>

<style>
/* Custom animations */
@keyframes float {
    0%, 100% { transform: translateY(0px); }
    50% { transform: translateY(-20px); }
}

.animate-float {
    animation: float 3s ease-in-out infinite;
}

/* Enhanced responsive design */
@media (max-width: 640px) {
    .text-4xl { font-size: 2.5rem; line-height: 1.1; }
    .text-6xl { font-size: 3.5rem; line-height: 1.1; }
    .text-7xl { font-size: 4rem; line-height: 1.1; }
    .text-8xl { font-size: 4.5rem; line-height: 1.1; }
    .text-9xl { font-size: 5rem; line-height: 1.1; }
}

@media (min-width: 641px) and (max-width: 768px) {
    .text-6xl { font-size: 4rem; }
    .text-7xl { font-size: 5rem; }
    .text-8xl { font-size: 6rem; }
    .text-9xl { font-size: 7rem; }
}

/* Improved touch targets for mobile */
@media (max-width: 640px) {
    button, a {
        min-height: 44px;
        min-width: 44px;
    }
}

/* Better spacing for mobile */
@media (max-width: 640px) {
    .space-y-8 > * + * {
        margin-top: 1.5rem;
    }
}
</style>


