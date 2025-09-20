<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Audiolibros - Radio Morrazo</title>
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

<!-- Audiobooks Header -->
<section class="py-20 bg-gradient-to-br from-radio-teal via-radio-cyan to-teal-600 overflow-hidden">
    <!-- Animated background elements -->
    <div class="absolute inset-0">
        <div class="absolute top-10 sm:top-20 left-5 sm:left-10 w-16 sm:w-32 h-16 sm:h-32 bg-white bg-opacity-10 rounded-full animate-pulse"></div>
        <div class="absolute top-20 sm:top-40 right-10 sm:right-20 w-12 sm:w-24 h-12 sm:h-24 bg-white bg-opacity-5 rounded-full animate-bounce"></div>
        <div class="absolute bottom-16 sm:bottom-32 left-1/4 w-8 sm:w-16 h-8 sm:h-16 bg-white bg-opacity-15 rounded-full animate-ping"></div>
    </div>
    
    <!-- Content -->
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl sm:text-6xl md:text-7xl lg:text-8xl xl:text-9xl font-black mb-4 sm:mb-6 leading-none">
            <span class="text-white drop-shadow-2xl"><?php echo $t['audiobooks']['page_title']; ?></span>
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            <?php echo $t['audiobooks']['page_subtitle']; ?>
        </p>
        <div class="flex justify-center mb-12 sm:mb-16 px-4">
            <div class="bg-white bg-opacity-20 backdrop-blur-lg p-2 rounded-2xl shadow-2xl">
                <input type="text" id="search-input" placeholder="Buscar audiolibros..." 
                       class="px-4 sm:px-6 py-2 sm:py-3 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-300 w-80 sm:w-96 text-slate-800 placeholder-slate-500">
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="py-8 sm:py-12 bg-gradient-to-br from-slate-50 via-gray-50 to-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6"><?php echo $t['audiobooks']['categories']; ?></h3>
            <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
                <button class="category-btn active bg-radio-teal text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('all')"><?php echo $t['podcasts']['all_categories']; ?></button>
                <button class="category-btn bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('fiction')"><?php echo $t['audiobooks']['fiction']; ?></button>
                <button class="category-btn bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('history')"><?php echo $t['audiobooks']['history']; ?></button>
                <button class="category-btn bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('poetry')"><?php echo $t['audiobooks']['poetry']; ?></button>
                <button class="category-btn bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('legends')"><?php echo $t['audiobooks']['legends']; ?></button>
            </div>
        </div>
    </div>
</section>

<!-- Featured Books -->
<section id="libros-destacados" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-white via-slate-50 to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo $t['audiobooks']['featured_books']; ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Audiobook 1 -->
            <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" data-category="legends">
                <div class="h-64 sm:h-80 bg-gradient-to-br from-radio-teal to-radio-cyan flex items-center justify-center relative overflow-hidden">
                    <div class="text-center">
                        <div class="w-24 sm:w-28 h-24 sm:h-28 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-2xl group-hover:scale-110 transition-transform duration-300">
                            <i class="fas fa-book text-4xl sm:text-5xl text-radio-teal"></i>
                        </div>
                        <div class="text-sm sm:text-lg text-radio-teal font-bold bg-white px-4 sm:px-6 py-2 rounded-full shadow-lg">Audiolibro Gratuito</div>
                    </div>
                    <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                        GRATIS
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="text-xs sm:text-sm text-radio-teal font-bold bg-radio-teal bg-opacity-10 px-3 py-1 rounded-full">LEYENDAS</span>
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-bold text-slate-800 mb-3 mt-3">Leyendas de Galicia</h3>
                    <p class="text-slate-600 mb-3 font-bold text-sm sm:text-base">Por Varios Autores</p>
                    <p class="text-slate-600 mb-6 leading-relaxed text-sm sm:text-base">
                        Recopilación de las leyendas más fascinantes de nuestra tierra, narradas con la magia que las caracteriza.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-radio-teal px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-radio-teal">3h 45min</span>
                        <span class="text-xs sm:text-sm text-slate-500">12 capítulos</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-radio-teal to-radio-cyan hover:from-radio-cyan hover:to-radio-teal text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl play-btn">
                            <i class="fas fa-play mr-2"></i>Escuchar
                        </button>
                        <button class="border-2 border-radio-teal text-radio-teal hover:bg-radio-teal hover:text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all">
                            <i class="fas fa-download mr-2"></i>Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Audiobook 2 -->
            <div class="media-item searchable-item card-hover glass-card rounded-2xl overflow-hidden shadow-lg" data-category="history">
                <div class="h-64 bg-gradient-to-br from-cyan-100 to-emerald-100 flex items-center justify-center relative">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-book text-4xl text-cyan-600"></i>
                        </div>
                        <div class="text-lg text-cyan-700 font-bold bg-white px-6 py-2 rounded-full shadow-md">Audiolibro Gratuito</div>
                    </div>
                    <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        GRATIS
                    </div>
                </div>
                <div class="p-6">
                    <span class="text-sm text-cyan-600 font-semibold">HISTORIA</span>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 mt-2">Historia Naval de Morrazo</h3>
                    <p class="text-slate-600 mb-3 font-medium">Por María Fernández</p>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        El legado marítimo de la península a través de los siglos, desde los primeros navegantes hasta la actualidad.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-slate-300 px-3 py-1 rounded-full text-sm font-semibold">2h 30min</span>
                        <span class="text-sm text-slate-500">8 capítulos</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-cyan-500 to-cyan-600 hover:from-cyan-600 hover:to-cyan-700 text-white py-3 px-4 rounded-xl font-semibold transition-all shadow-lg play-btn">
                            <i class="fas fa-play mr-2"></i>Escuchar
                        </button>
                        <button class="border-2 border-cyan-500 text-cyan-600 hover:bg-cyan-50 py-3 px-4 rounded-xl font-semibold transition-all">
                            <i class="fas fa-download mr-2"></i>Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Audiobook 3 -->
            <div class="media-item searchable-item card-hover glass-card rounded-2xl overflow-hidden shadow-lg" data-category="poetry">
                <div class="h-64 bg-gradient-to-br from-emerald-100 to-teal-100 flex items-center justify-center relative">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-book text-4xl text-emerald-600"></i>
                        </div>
                        <div class="text-lg text-emerald-700 font-bold bg-white px-6 py-2 rounded-full shadow-md">Audiolibro Gratuito</div>
                    </div>
                    <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        GRATIS
                    </div>
                </div>
                <div class="p-6">
                    <span class="text-sm text-emerald-600 font-semibold">POESÍA</span>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 mt-2">Versos del Atlántico</h3>
                    <p class="text-slate-600 mb-3 font-medium">Por Eduardo Pondal</p>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Una colección de poemas que capturan la esencia del mar y la tierra gallega con una sensibilidad única.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-slate-300 px-3 py-1 rounded-full text-sm font-semibold">1h 45min</span>
                        <span class="text-sm text-slate-500">25 poemas</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white py-3 px-4 rounded-xl font-semibold transition-all shadow-lg play-btn">
                            <i class="fas fa-play mr-2"></i>Escuchar
                        </button>
                        <button class="border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-50 py-3 px-4 rounded-xl font-semibold transition-all">
                            <i class="fas fa-download mr-2"></i>Descargar
                        </button>
                    </div>
                </div>
            </div>

            <!-- Audiobook 4 -->
            <div class="media-item searchable-item card-hover glass-card rounded-2xl overflow-hidden shadow-lg" data-category="fiction">
                <div class="h-64 bg-gradient-to-br from-purple-100 to-pink-100 flex items-center justify-center relative">
                    <div class="text-center">
                        <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
                            <i class="fas fa-book text-4xl text-purple-600"></i>
                        </div>
                        <div class="text-lg text-purple-700 font-bold bg-white px-6 py-2 rounded-full shadow-md">Audiolibro Gratuito</div>
                    </div>
                    <div class="absolute top-4 right-4 bg-green-500 text-white px-3 py-1 rounded-full text-sm font-semibold">
                        GRATIS
                    </div>
                </div>
                <div class="p-6">
                    <span class="text-sm text-purple-600 font-semibold">FICCIÓN</span>
                    <h3 class="text-2xl font-bold text-slate-800 mb-3 mt-2">El Faro de Morrazo</h3>
                    <p class="text-slate-600 mb-3 font-medium">Por Carmen Vidal</p>
                    <p class="text-slate-600 mb-6 leading-relaxed">
                        Una novela que entrelaza realidad y ficción en torno al mítico faro de la península y sus guardianes.
                    </p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-slate-300 px-3 py-1 rounded-full text-sm font-semibold">4h 20min</span>
                        <span class="text-sm text-slate-500">15 capítulos</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white py-3 px-4 rounded-xl font-semibold transition-all shadow-lg play-btn">
                            <i class="fas fa-play mr-2"></i>Escuchar
                        </button>
                        <button class="border-2 border-purple-500 text-purple-600 hover:bg-purple-50 py-3 px-4 rounded-xl font-semibold transition-all">
                            <i class="fas fa-download mr-2"></i>Descargar
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- New Releases -->
<section id="nuevos-lanzamientos" class="py-20 bg-gradient-to-br from-gray-50 via-slate-50 to-teal-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
            <h2 class="text-4xl font-bold text-slate-800 mb-6">
                <?php echo $t['audiobooks']['new_releases']; ?>
            </h2>
            <p class="text-xl text-slate-600">Los últimos audiolibros añadidos a nuestra colección</p>
        </div>
        
        <div class="grid md:grid-cols-2 gap-8">
            <!-- New Release 1 -->
            <div class="glass-card p-6 rounded-2xl shadow-lg">
                <div class="flex items-start space-x-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-teal-500 to-cyan-500 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <i class="fas fa-book-open text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-sm text-teal-600 font-semibold">NUEVO</span>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Cuentos de la Ría</h3>
                        <p class="text-slate-600 mb-3">Por Ana Martínez</p>
                        <p class="text-slate-600 text-sm mb-4">Relatos cortos ambientados en los paisajes únicos de nuestra ría.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">2h 15min</span>
                            <button class="btn-primary text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                <i class="fas fa-play mr-1"></i>Escuchar
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Release 2 -->
            <div class="glass-card p-6 rounded-2xl shadow-lg">
                <div class="flex items-start space-x-4">
                    <div class="w-20 h-20 bg-gradient-to-br from-emerald-500 to-teal-500 rounded-xl flex items-center justify-center shadow-lg flex-shrink-0">
                        <i class="fas fa-book-open text-white text-2xl"></i>
                    </div>
                    <div class="flex-1">
                        <span class="text-sm text-emerald-600 font-semibold">NUEVO</span>
                        <h3 class="text-xl font-bold text-slate-800 mb-2">Memorias de un Pescador</h3>
                        <p class="text-slate-600 mb-3">Por José Rodríguez</p>
                        <p class="text-slate-600 text-sm mb-4">Las vivencias de toda una vida dedicada al mar y la pesca tradicional.</p>
                        <div class="flex items-center justify-between">
                            <span class="text-sm text-slate-500">3h 30min</span>
                            <button class="bg-gradient-to-r from-emerald-500 to-emerald-600 text-white px-4 py-2 rounded-lg text-sm font-semibold">
                                <i class="fas fa-play mr-1"></i>Escuchar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>
