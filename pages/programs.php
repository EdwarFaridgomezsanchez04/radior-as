<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Programas - Radio Morrazo</title>
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

<!-- Programs Header -->
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
            <span class="text-white drop-shadow-2xl"><?php echo $t['programs']['page_title']; ?></span>
        </h1>
        <p class="text-lg sm:text-xl md:text-2xl lg:text-3xl xl:text-4xl text-white font-light mb-6 sm:mb-8 drop-shadow-lg px-4">
            <?php echo $t['programs']['page_subtitle']; ?>
        </p>
        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 justify-center mb-12 sm:mb-16 px-4">
            <a href="#programacion" class="group bg-white text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg shadow-2xl hover:shadow-3xl transform hover:scale-105 transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                <i class="fas fa-calendar mr-2 sm:mr-3 group-hover:scale-110 transition-transform"></i>
                Ver Programación
            </a>
            <a href="<?php echo url('about'); ?>#equipo" class="group border-2 sm:border-3 border-white text-white hover:bg-white hover:text-radio-teal px-6 sm:px-12 py-3 sm:py-5 rounded-2xl font-bold text-sm sm:text-lg transition-all duration-300 flex items-center justify-center w-full sm:w-auto">
                <i class="fas fa-users mr-2 sm:mr-3 group-hover:rotate-180 transition-transform duration-500"></i>
                Nuestro Equipo
            </a>
        </div>
    </div>
</section>

<!-- Weekly Schedule -->
<section id="programacion" class="py-12 sm:py-16 lg:py-24 bg-gradient-to-br from-slate-50 via-gray-50 to-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12 sm:mb-16 lg:mb-20">
            <h2 class="text-3xl sm:text-4xl lg:text-5xl xl:text-6xl font-black text-gray-900 mb-4 sm:mb-6">
                <?php echo $t['programs']['schedule']; ?>
            </h2>
            <div class="w-16 sm:w-24 h-1 bg-radio-teal mx-auto mb-4 sm:mb-6"></div>
            <p class="text-base sm:text-lg lg:text-xl text-gray-600 max-w-2xl mx-auto px-4">
                Lunes a Domingo - Programación completa
            </p>
        </div>

        <!-- Time Blocks -->
        <div class="space-y-8 sm:space-y-12">
            <!-- Morning Block -->
            <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-radio-teal to-radio-cyan p-6 sm:p-8">
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-black text-white mb-2 flex items-center">
                        <i class="fas fa-sun mr-3 text-yellow-300"></i>
                        <?php echo $t['programs']['morning_block']; ?> (06:00 - 12:00)
                    </h3>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="grid md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="border-l-4 border-l-radio-teal pl-6">
                            <h4 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-2"><?php echo $t['programs']['buenos_dias']['title']; ?></h4>
                            <p class="text-radio-teal font-bold mb-2 text-sm sm:text-base"><?php echo $t['programs']['buenos_dias']['time']; ?></p>
                            <p class="text-slate-600 mb-3 text-sm sm:text-base"><?php echo $t['programs']['buenos_dias']['description']; ?></p>
                            <p class="text-sm text-slate-500">Presenta: <?php echo $t['programs']['buenos_dias']['host']; ?></p>
                        </div>
                        <div class="bg-gradient-to-br from-radio-teal bg-opacity-10 p-6 rounded-xl">
                            <h5 class="font-bold text-slate-800 mb-3 text-sm sm:text-base">Secciones del programa:</h5>
                            <ul class="space-y-2 text-slate-600 text-sm sm:text-base">
                                <li class="flex items-center"><i class="fas fa-check text-radio-teal mr-2"></i>Noticias locales</li>
                                <li class="flex items-center"><i class="fas fa-check text-radio-teal mr-2"></i>Estado del tiempo</li>
                                <li class="flex items-center"><i class="fas fa-check text-radio-teal mr-2"></i>Música variada</li>
                                <li class="flex items-center"><i class="fas fa-check text-radio-teal mr-2"></i>Entrevistas</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Afternoon Block -->
            <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-purple-600 to-pink-600 p-6 sm:p-8">
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-black text-white mb-2 flex items-center">
                        <i class="fas fa-sun mr-3 text-yellow-300"></i>
                        <?php echo $t['programs']['afternoon_block']; ?> (12:00 - 18:00)
                    </h3>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="grid md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="border-l-4 border-l-purple-500 pl-6">
                            <h4 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-2"><?php echo $t['programs']['cultura_galega']['title']; ?></h4>
                            <p class="text-purple-600 font-bold mb-2 text-sm sm:text-base"><?php echo $t['programs']['cultura_galega']['time']; ?></p>
                            <p class="text-slate-600 mb-3 text-sm sm:text-base"><?php echo $t['programs']['cultura_galega']['description']; ?></p>
                            <p class="text-sm text-slate-500">Presenta: <?php echo $t['programs']['cultura_galega']['host']; ?></p>
                        </div>
                        <div class="bg-gradient-to-br from-purple-500 bg-opacity-10 p-6 rounded-xl">
                            <h5 class="font-bold text-slate-800 mb-3 text-sm sm:text-base">Contenido especial:</h5>
                            <ul class="space-y-2 text-slate-600 text-sm sm:text-base">
                                <li class="flex items-center"><i class="fas fa-check text-purple-500 mr-2"></i>Historia de Galicia</li>
                                <li class="flex items-center"><i class="fas fa-check text-purple-500 mr-2"></i>Música tradicional</li>
                                <li class="flex items-center"><i class="fas fa-check text-purple-500 mr-2"></i>Artistas locales</li>
                                <li class="flex items-center"><i class="fas fa-check text-purple-500 mr-2"></i>Tradiciones</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Night Block -->
            <div class="group bg-white rounded-2xl sm:rounded-3xl overflow-hidden shadow-xl hover:shadow-2xl transition-all duration-500 transform hover:-translate-y-2">
                <div class="bg-gradient-to-br from-orange-500 to-red-500 p-6 sm:p-8">
                    <h3 class="text-xl sm:text-2xl lg:text-3xl font-black text-white mb-2 flex items-center">
                        <i class="fas fa-moon mr-3 text-yellow-300"></i>
                        <?php echo $t['programs']['night_block']; ?> (18:00 - 00:00)
                    </h3>
                </div>
                <div class="p-6 sm:p-8">
                    <div class="grid md:grid-cols-2 gap-6 sm:gap-8">
                        <div class="border-l-4 border-l-orange-500 pl-6">
                            <h4 class="text-lg sm:text-xl lg:text-2xl font-bold text-slate-800 mb-2"><?php echo $t['programs']['noches_radio']['title']; ?></h4>
                            <p class="text-orange-600 font-bold mb-2 text-sm sm:text-base"><?php echo $t['programs']['noches_radio']['time']; ?></p>
                            <p class="text-slate-600 mb-3 text-sm sm:text-base"><?php echo $t['programs']['noches_radio']['description']; ?></p>
                            <p class="text-sm text-slate-500">Presenta: <?php echo $t['programs']['noches_radio']['host']; ?></p>
                        </div>
                        <div class="bg-gradient-to-br from-orange-500 bg-opacity-10 p-6 rounded-xl">
                            <h5 class="font-bold text-slate-800 mb-3 text-sm sm:text-base">Ambiente nocturno:</h5>
                            <ul class="space-y-2 text-slate-600 text-sm sm:text-base">
                                <li class="flex items-center"><i class="fas fa-check text-orange-500 mr-2"></i>Música relajante</li>
                                <li class="flex items-center"><i class="fas fa-check text-orange-500 mr-2"></i>Llamadas de oyentes</li>
                                <li class="flex items-center"><i class="fas fa-check text-orange-500 mr-2"></i>Dedicatorias</li>
                                <li class="flex items-center"><i class="fas fa-check text-orange-500 mr-2"></i>Conversación</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</body>
</html>
