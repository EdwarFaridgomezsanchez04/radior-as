<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sobre Nosotros - Radio Morrazo</title>
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

<!-- About Header -->
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
            <!-- Main Title -->
            <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl font-black mb-4 sm:mb-6 leading-none">
                <span class="text-white drop-shadow-2xl"><?php echo $t['about']['page_title']; ?></span>
        </h1>
            
            <!-- Subtitle -->
            <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            <?php echo $t['about']['page_subtitle']; ?>
        </p>
            
            <!-- Description -->
            <p class="text-sm sm:text-base md:text-lg lg:text-xl text-white text-opacity-90 mb-8 sm:mb-12 max-w-4xl mx-auto leading-relaxed drop-shadow-md px-4">
                Conoce la historia, misión y visión de RadioRías, la voz auténtica de la península de Morrazo.
            </p>
            
            <!-- Action Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center mb-12 sm:mb-16 px-4">
                <a href="#historia" class="group bg-white text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                    <i class="fas fa-history mr-2 sm:mr-3 group-hover:scale-110 transition-transform"></i>
                    Nuestra Historia
                </a>
                <a href="#equipo" class="group border-2 sm:border-3 border-white text-white hover:bg-white hover:text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                    <i class="fas fa-users mr-2 sm:mr-3 group-hover:rotate-180 transition-transform duration-500"></i>
                    Nuestro Equipo
                </a>
            </div>
            
            <!-- Modern Radio Wave Animation -->
            <div class="flex justify-center">
                <div class="relative">
                    <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center backdrop-blur-sm">
                        <i class="fas fa-heart text-lg sm:text-2xl text-white"></i>
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

<!-- History Section -->
<section id="historia" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                    <?php echo $t['about']['our_history']; ?>
                </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                    <?php echo $t['about']['history_text']; ?>
                </p>
        </div>
        
        <div class="grid lg:grid-cols-2 gap-12 sm:gap-16 items-center">
            <div>
                <div class="space-y-6 sm:space-y-8">
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                            <div class="w-4 h-4 bg-radio-teal rounded-full mr-4"></div>
                            <span class="text-slate-700 font-bold text-sm sm:text-base"><strong>2009:</strong> Primeras emisiones de RadioCangas</span>
                        </div>
                    </div>
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                            <div class="w-4 h-4 bg-radio-cyan rounded-full mr-4"></div>
                            <span class="text-slate-700 font-bold text-sm sm:text-base"><strong>2016:</strong> Formalización de Asociación Morrazo Radio</span>
                        </div>
                    </div>
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                        <div class="w-4 h-4 bg-emerald-500 rounded-full mr-4"></div>
                            <span class="text-slate-700 font-bold text-sm sm:text-base"><strong>2020:</strong> Pausa por pandemia COVID-19</span>
                        </div>
                    </div>
                    <div class="group bg-white p-6 sm:p-8 rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-1">
                        <div class="flex items-center mb-4">
                        <div class="w-4 h-4 bg-teal-600 rounded-full mr-4"></div>
                            <span class="text-slate-700 font-bold text-sm sm:text-base"><strong>2024:</strong> Relanzamiento como RadioRías</span>
                        </div>
                    </div>
                </div>
            </div>
            <div class="text-center">
                <div class="w-80 sm:w-96 h-80 sm:h-96 bg-gradient-to-br from-radio-teal to-radio-cyan rounded-3xl flex items-center justify-center mx-auto shadow-2xl group hover:scale-105 transition-transform duration-300">
                    <div class="w-64 sm:w-72 h-64 sm:h-72 bg-white rounded-2xl flex items-center justify-center shadow-lg">
                        <img src="assets/images/team/image.png" alt="logo2" class="w-full h-full object-cover">
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Mission Section -->
<section id="mision" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-radio-teal to-radio-cyan">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-white mb-4 sm:mb-6">
                <?php echo $t['about']['our_mission']; ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-yellow-300 mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-white text-opacity-90 max-w-2xl mx-auto px-4">
                <?php echo $t['about']['mission_text']; ?>
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Value 1 -->
            <div class="group bg-white bg-opacity-10 backdrop-blur-lg p-6 sm:p-8 rounded-2xl sm:rounded-3xl border border-white border-opacity-20 hover:bg-opacity-20 transition-all duration-300 text-center">
                <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-heart text-2xl sm:text-3xl text-radio-teal"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-white mb-3 sm:mb-4">Pasión</h3>
                <p class="text-white text-opacity-90 text-sm sm:text-base">Amamos lo que hacemos y se refleja en cada programa que transmitimos.</p>
            </div>

            <!-- Value 2 -->
            <div class="group bg-white bg-opacity-10 backdrop-blur-lg p-6 sm:p-8 rounded-2xl sm:rounded-3xl border border-white border-opacity-20 hover:bg-opacity-20 transition-all duration-300 text-center">
                <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-users text-2xl sm:text-3xl text-radio-teal"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-white mb-3 sm:mb-4">Comunidad</h3>
                <p class="text-white text-opacity-90 text-sm sm:text-base">Somos parte integral de la comunidad de Morrazo y trabajamos para ella.</p>
            </div>

            <!-- Value 3 -->
            <div class="group bg-white bg-opacity-10 backdrop-blur-lg p-6 sm:p-8 rounded-2xl sm:rounded-3xl border border-white border-opacity-20 hover:bg-opacity-20 transition-all duration-300 text-center sm:col-span-2 lg:col-span-1">
                <div class="w-16 sm:w-20 h-16 sm:h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 group-hover:scale-110 transition-transform duration-300 shadow-lg">
                    <i class="fas fa-star text-2xl sm:text-3xl text-radio-teal"></i>
                </div>
                <h3 class="text-xl sm:text-2xl font-bold text-white mb-3 sm:mb-4">Calidad</h3>
                <p class="text-white text-opacity-90 text-sm sm:text-base">Nos comprometemos con la excelencia en cada aspecto de nuestro trabajo.</p>
            </div>
        </div>
    </div>
</section>

<!-- Vision Section -->
<section id="vision" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo $t['about']['our_vision']; ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                <?php echo $t['about']['vision_text']; ?>
            </p>
        </div>
        
        <div class="text-center">
            <div class="max-w-4xl mx-auto">
                <p class="text-lg sm:text-xl text-slate-600 leading-relaxed mb-6 sm:mb-8">
                    RadioRías es un tributo a nuestra tierra y a su identidad, un medio que suma voces para una sociedad abierta, moderna y dinámica.
                </p>
                <p class="text-base sm:text-lg text-slate-600 leading-relaxed">
                    Un espacio que tiende puentes entre Galicia, España, América Latina y el resto del mundo.
                </p>
            </div>
        </div>
    </div>
</section>

<!-- Our Essence Section -->
<section id="esencia" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-radio-teal to-radio-cyan">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <div class="group bg-white bg-opacity-10 backdrop-blur-lg p-8 sm:p-12 rounded-2xl sm:rounded-3xl shadow-2xl hover:shadow-3xl transition-all duration-500 transform hover:-translate-y-1 max-w-4xl mx-auto border border-white border-opacity-20">
                <div class="w-24 sm:w-32 h-24 sm:h-32 bg-white rounded-full flex items-center justify-center mx-auto mb-6 sm:mb-8 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-heart text-3xl sm:text-4xl text-radio-teal"></i>
                </div>
                <h3 class="text-2xl sm:text-3xl lg:text-4xl font-black text-white mb-4 sm:mb-6"><?php echo $t['about']['our_essence']; ?></h3>
                <p class="text-base sm:text-lg text-white text-opacity-90 leading-relaxed mb-4 sm:mb-6">
                    <?php echo $t['about']['essence_text']; ?>
                </p>
                <div class="text-xl sm:text-2xl font-black text-yellow-300">
                    <?php echo $t['about']['essence_slogan']; ?>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Statistics Section - COMENTADA
<section id="estadisticas" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-white to-slate-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                RadioRías en <span class="text-radio-teal">Números</span>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                Nuestro impacto en la comunidad
            </p>
        </div>
        
        <!-- <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <!-- <div class="group text-center p-6 sm:p-8 bg-white rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="w-20 sm:w-24 h-20 sm:h-24 bg-gradient-to-br from-radio-teal to-radio-cyan rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-users text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-slate-800 mb-2">15,000+</div>
                <div class="text-slate-600 font-bold text-sm sm:text-base"><?php echo $t['about']['stats_listeners']; ?></div>
            </div> -->

            <!-- <div class="group text-center p-6 sm:p-8 bg-white rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="w-20 sm:w-24 h-20 sm:h-24 bg-gradient-to-br from-purple-500 to-pink-500 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-headphones text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-slate-800 mb-2">25</div>
                <div class="text-slate-600 font-bold text-sm sm:text-base"><?php echo $t['about']['stats_programs']; ?></div>
            </div>

            <div class="group text-center p-6 sm:p-8 bg-white rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="w-20 sm:w-24 h-20 sm:h-24 bg-gradient-to-br from-orange-500 to-red-500 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-calendar text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-slate-800 mb-2">20+</div>
                <div class="text-slate-600 font-bold text-sm sm:text-base"><?php echo $t['about']['stats_years']; ?></div>
            </div>

            <div class="group text-center p-6 sm:p-8 bg-white rounded-2xl sm:rounded-3xl shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="w-20 sm:w-24 h-20 sm:h-24 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-full flex items-center justify-center mx-auto mb-4 sm:mb-6 shadow-lg group-hover:scale-110 transition-transform duration-300">
                    <i class="fas fa-podcast text-white text-2xl sm:text-3xl"></i>
                </div>
                <div class="text-3xl sm:text-4xl font-black text-slate-800 mb-2">500+</div>
                <div class="text-slate-600 font-bold text-sm sm:text-base">Episodios de Podcast</div>
            </div>
        </div>
    </div>
</section>  -->

<!-- Team Section -->
<section id="equipo" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-slate-50 via-teal-50 to-cyan-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo $t['about']['our_team']; ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                <?php echo $t['about']['team_text']; ?>
            </p>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-6 sm:gap-8">
            <!-- Team Member 1 - José Manuel Malvido Gregorio -->
            <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 text-center">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-radio-teal to-radio-cyan relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                         <img src="assets/images/team/josemanuel.jpg" alt="Jose jose-manuel-malvido" class="w-24 sm:w-32 h-24 sm:h-32 rounded-full object-cover shadow-lg group-hover:scale-125 transition-transform duration-500">
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg sm:text-xl text-black mb-2">José Manuel Malvido</h3>
                    <p class="text-black text-sm sm:text-base mb-3 sm:mb-4">Programador y Operador de Radio</p>
                    <p class="text-sm sm:text-base text-black leading-relaxed">Gallego con amplia experiencia, cuya inestimable ayuda fue decisiva en el nacimiento y consolidación de Morrazo Radio.</p>
                </div>
            </div>

            <!-- Team Member 2 - Ollantay González Serga -->
            <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 text-center">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-purple-500 to-pink-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="assets/images/team/ollanty-gonzalez.jpg" alt="Ollantay González Serga" class="w-24 sm:w-32 h-24 sm:h-32 rounded-full object-cover shadow-lg group-hover:scale-125 transition-transform duration-500">
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg sm:text-xl text-black mb-2">Ollantay González Serga</h3>
                    <p class="text-black text-sm sm:text-base mb-3 sm:mb-4">Escritor, Conferencista, Coach y Abogado</p>
                    <p class="text-sm sm:text-base text-black leading-relaxed">Venezolano residente en España, arraigado en Galicia. Autor de diecisiete libros publicados en Amazon.</p>
                </div>
            </div>

            <!-- Team Member 3 - Miguelángel Parcero Palma -->
            <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 text-center">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-orange-400 to-red-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="assets/images/team/miguelangel-parcero.jpg" alt="Miguelángel Parcero Palma" class="w-24 sm:w-32 h-24 sm:h-32 rounded-full object-cover shadow-lg group-hover:scale-125 transition-transform duration-500">
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg sm:text-xl text-black mb-2">Miguelángel Parcero</h3>
                    <p class="text-black text-sm sm:text-base mb-3 sm:mb-4">Presidente y Editor de la Asociación Morrazo Radio</p>
                    <p class="text-sm sm:text-base text-black leading-relaxed">Gallego cuya trayectoria personal y profesional lo ha llevado a crecer entre cables, micrófonos y cámaras.</p>
                </div>
            </div>

            <!-- Team Member 4 - Edwar Farid Gómez Sánchez -->
            <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2 text-center">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-emerald-500 to-teal-500 relative overflow-hidden">
                    <div class="absolute inset-0 bg-black bg-opacity-20 group-hover:bg-opacity-0 transition-all duration-500"></div>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <img src="assets/images/team/edwar-gomez.jpg" alt="Edwar Farid Gómez Sánchez" class="w-24 sm:w-32 h-24 sm:h-32 rounded-full object-cover shadow-lg group-hover:scale-125 transition-transform duration-500">
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <h3 class="text-lg sm:text-xl text-black mb-2">Edwar Farid Gómez</h3>
                    <p class="text-black text-sm sm:text-base mb-3 sm:mb-4">Diseñador y Desarrollador Web</p>
                    <p class="text-sm sm:text-base text-black leading-relaxed">
                        Responsable del diseño y visualización de nuestra página web, aportando su experiencia profesional para crear una plataforma digital moderna y accesible que refleje la identidad de RadioRías.
                    </p>
                </div>
            </div>
        </div>
        
    </div>
</section>

</body>
</html>
