<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Podcasts - Radio Morrazo</title>
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

<!-- Podcasts Header -->
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
            <span class="text-white drop-shadow-2xl"><?php echo $t['podcasts']['page_title']; ?></span>
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            <?php echo $t['podcasts']['page_subtitle']; ?>
        </p>
        <div class="flex justify-center mb-12 sm:mb-16 px-4">
            <div class="bg-white bg-opacity-20 backdrop-blur-lg p-2 rounded-2xl shadow-2xl">
                <input type="text" id="search-input" placeholder="Buscar podcasts..." 
                       class="px-4 sm:px-6 py-2 sm:py-3 border-0 rounded-xl focus:outline-none focus:ring-2 focus:ring-yellow-300 w-80 sm:w-96 text-slate-800 placeholder-slate-500">
            </div>
        </div>
    </div>
</section>

<!-- Category Filter -->
<section class="py-8 sm:py-12 bg-gradient-to-br from-slate-50 via-gray-50 to-white border-b border-slate-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center">
            <h3 class="text-lg sm:text-xl font-bold text-slate-800 mb-6"><?php echo $t['podcasts']['categories']; ?></h3>
            <div class="flex flex-wrap justify-center gap-3 sm:gap-4">
                <button class="category-btn active bg-radio-teal text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('all')"><?php echo $t['podcasts']['all_categories']; ?></button>
                <button class="category-btn bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('history')"><?php echo $t['podcasts']['history']; ?></button>
                <button class="category-btn bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('culture')"><?php echo $t['podcasts']['culture']; ?></button>
                <button class="category-btn bg-white text-radio-teal border-2 border-radio-teal hover:bg-radio-teal hover:text-white px-4 sm:px-6 py-2 sm:py-3 rounded-xl font-bold text-sm sm:text-base shadow-lg hover:shadow-xl transition-all duration-300 transform hover:scale-105" onclick="filterByCategory('gastronomy')"><?php echo $t['podcasts']['gastronomy']; ?></button>
            </div>
        </div>
    </div>
</section>

<!-- Latest Episodes -->
<section id="episodios" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-white via-slate-50 to-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo $t['podcasts']['latest_episodes']; ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
        </div>
        
        <div class="grid sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            <!-- Podcast 1 -->
            <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" data-category="history">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-radio-teal to-radio-cyan flex items-center justify-center relative overflow-hidden">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headphones text-3xl sm:text-4xl text-radio-teal"></i>
                    </div>
                    <div class="absolute top-4 right-4 bg-red-500 text-white px-3 py-1 rounded-full text-xs sm:text-sm font-bold shadow-lg">
                        NUEVO
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="text-xs sm:text-sm text-radio-teal font-bold bg-radio-teal bg-opacity-10 px-3 py-1 rounded-full">HISTORIA</span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3 mt-3">Historia de Morrazo</h3>
                    <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">Un viaje por los orígenes de nuestra península, desde los primeros asentamientos hasta la actualidad.</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-radio-teal px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-radio-teal">45 min</span>
                        <span class="text-xs sm:text-sm text-slate-500">Episodio 1</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-radio-teal to-radio-cyan hover:from-radio-cyan hover:to-radio-teal text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl play-btn">
                            <i class="fas fa-play mr-2"></i>Reproducir
                        </button>
                        <button class="border-2 border-radio-teal text-radio-teal hover:bg-radio-teal hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Podcast 2 -->
            <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" data-category="culture">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-purple-600 to-pink-600 flex items-center justify-center relative overflow-hidden">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headphones text-3xl sm:text-4xl text-purple-600"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="text-xs sm:text-sm text-purple-600 font-bold bg-purple-100 px-3 py-1 rounded-full">CULTURA</span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3 mt-3">Tradiciones Marineras</h3>
                    <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">Las costumbres que nos definen como pueblo costero, desde la pesca hasta las festividades.</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-purple-500 px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-purple-600">32 min</span>
                        <span class="text-xs sm:text-sm text-slate-500">Episodio 2</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-purple-500 to-pink-500 hover:from-pink-500 hover:to-purple-500 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl play-btn">
                            <i class="fas fa-play mr-2"></i>Reproducir
                        </button>
                        <button class="border-2 border-purple-500 text-purple-600 hover:bg-purple-500 hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Podcast 3 -->
            <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" data-category="gastronomy">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-emerald-500 to-emerald-600 flex items-center justify-center relative overflow-hidden">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headphones text-3xl sm:text-4xl text-emerald-600"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="text-xs sm:text-sm text-emerald-600 font-bold bg-emerald-100 px-3 py-1 rounded-full">GASTRONOMÍA</span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3 mt-3">Gastronomía Local</h3>
                    <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">Los sabores únicos de nuestra tierra, recetas tradicionales y productos del mar.</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-emerald-500 px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-emerald-600">28 min</span>
                        <span class="text-xs sm:text-sm text-slate-500">Episodio 3</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-600 hover:to-emerald-700 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl play-btn">
                            <i class="fas fa-play mr-2"></i>Reproducir
                        </button>
                        <button class="border-2 border-emerald-500 text-emerald-600 hover:bg-emerald-500 hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Podcast 4 -->
            <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" data-category="history">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-purple-500 to-purple-600 flex items-center justify-center relative overflow-hidden">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headphones text-3xl sm:text-4xl text-purple-600"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="text-xs sm:text-sm text-purple-600 font-bold bg-purple-100 px-3 py-1 rounded-full">HISTORIA</span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3 mt-3">Leyendas del Mar</h3>
                    <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">Historias y leyendas que han pasado de generación en generación en nuestras costas.</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-purple-500 px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-purple-600">38 min</span>
                        <span class="text-xs sm:text-sm text-slate-500">Episodio 4</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-purple-500 to-purple-600 hover:from-purple-600 hover:to-purple-700 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl play-btn">
                            <i class="fas fa-play mr-2"></i>Reproducir
                        </button>
                        <button class="border-2 border-purple-500 text-purple-600 hover:bg-purple-500 hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Podcast 5 -->
            <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" data-category="culture">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-pink-500 to-pink-600 flex items-center justify-center relative overflow-hidden">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headphones text-3xl sm:text-4xl text-pink-600"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="text-xs sm:text-sm text-pink-600 font-bold bg-pink-100 px-3 py-1 rounded-full">CULTURA</span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3 mt-3">Festivales y Tradiciones</h3>
                    <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">Las celebraciones que marcan el calendario cultural de Morrazo a lo largo del año.</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-pink-500 px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-pink-600">42 min</span>
                        <span class="text-xs sm:text-sm text-slate-500">Episodio 5</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-pink-500 to-pink-600 hover:from-pink-600 hover:to-pink-700 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl play-btn">
                            <i class="fas fa-play mr-2"></i>Reproducir
                        </button>
                        <button class="border-2 border-pink-500 text-pink-600 hover:bg-pink-500 hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Podcast 6 -->
            <div class="media-item searchable-item group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2" data-category="gastronomy">
                <div class="h-48 sm:h-64 bg-gradient-to-br from-orange-500 to-orange-600 flex items-center justify-center relative overflow-hidden">
                    <div class="w-20 sm:w-24 h-20 sm:h-24 bg-white rounded-full flex items-center justify-center shadow-2xl group-hover:scale-110 transition-transform duration-300">
                        <i class="fas fa-headphones text-3xl sm:text-4xl text-orange-600"></i>
                    </div>
                </div>
                <div class="p-6 sm:p-8">
                    <span class="text-xs sm:text-sm text-orange-600 font-bold bg-orange-100 px-3 py-1 rounded-full">GASTRONOMÍA</span>
                    <h3 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-3 mt-3">Productos del Mar</h3>
                    <p class="text-slate-600 mb-4 text-sm sm:text-base leading-relaxed">Todo sobre los frutos del mar que caracterizan nuestra gastronomía local.</p>
                    <div class="flex items-center justify-between mb-4">
                        <span class="border-2 border-orange-500 px-3 py-1 rounded-full text-xs sm:text-sm font-bold text-orange-600">35 min</span>
                        <span class="text-xs sm:text-sm text-slate-500">Episodio 6</span>
                    </div>
                    <div class="flex gap-2">
                        <button class="flex-1 bg-gradient-to-r from-orange-500 to-orange-600 hover:from-orange-600 hover:to-orange-700 text-white py-2 sm:py-3 px-4 rounded-xl font-bold text-xs sm:text-sm transition-all shadow-lg hover:shadow-xl play-btn">
                            <i class="fas fa-play mr-2"></i>Reproducir
                        </button>
                        <button class="border-2 border-orange-500 text-orange-600 hover:bg-orange-500 hover:text-white py-2 sm:py-3 px-4 rounded-xl transition-all">
                            <i class="fas fa-download"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>
