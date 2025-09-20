<!-- Favicon Redondeado -->
<?php include 'favicon.php'; ?>

<!-- Widget Flotante CSS -->
<link rel="stylesheet" href="assets/css/floating-widget.css">

<nav class="glass-card border-b border-white border-opacity-20 sticky top-0 z-50 backdrop-blur-lg bg-white bg-opacity-90">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16 sm:h-20">
            <div class="flex items-center space-x-3 sm:space-x-4">
                <img alt="Radio Morrazo Logo" loading="lazy" width="40" height="40" decoding="async" data-nimg="1" class="rounded-full shadow-lg" style="color:transparent" src="assets/images/radiomorrazo-logo.png">
                <span class="text-xl sm:text-2xl font-black text-slate-800">Radio Rías</span>
            </div>
            
            <!-- Desktop Navigation -->
            <div class="desktop-nav hidden md:flex items-center space-x-6 lg:space-x-8">
                <a href="<?php echo url('home'); ?>" class="nav-link <?php echo $page === 'home' ? 'active' : ''; ?> text-slate-700 hover:text-radio-teal font-bold text-sm lg:text-base transition-all duration-300 hover:scale-105"><?php echo $t['nav']['home']; ?></a>
                <!-- <a href="<?php echo url('programs'); ?>" class="nav-link <?php echo $page === 'programs' ? 'active' : ''; ?> text-slate-700 hover:text-radio-teal font-bold text-sm lg:text-base transition-all duration-300 hover:scale-105"><?php echo $t['nav']['programs']; ?></a> -->
                <a href="<?php echo url('about'); ?>" class="nav-link <?php echo $page === 'about' ? 'active' : ''; ?> text-slate-700 hover:text-radio-teal font-bold text-sm lg:text-base transition-all duration-300 hover:scale-105"><?php echo $t['nav']['about']; ?></a>
                <!-- <a href="<?php echo url('podcasts'); ?>" class="nav-link <?php echo $page === 'podcasts' ? 'active' : ''; ?> text-slate-700 hover:text-radio-teal font-bold text-sm lg:text-base transition-all duration-300 hover:scale-105"><?php echo $t['nav']['podcasts']; ?></a> -->
                <!-- <a href="<?php echo url('audiobooks'); ?>" class="nav-link <?php echo $page === 'audiobooks' ? 'active' : ''; ?> text-slate-700 hover:text-radio-teal font-bold text-sm lg:text-base transition-all duration-300 hover:scale-105"><?php echo $t['nav']['audiobooks']; ?></a> -->
                <a href="<?php echo url('contact'); ?>" class="nav-link <?php echo $page === 'contact' ? 'active' : ''; ?> text-slate-700 hover:text-radio-teal font-bold text-sm lg:text-base transition-all duration-300 hover:scale-105"><?php echo $t['nav']['contact']; ?></a>
                <!-- <a href="login.php" class="text-slate-700 hover:text-radio-teal font-bold text-sm lg:text-base transition-all duration-300 hover:scale-105 flex items-center">
                    <i class="fas fa-user-shield mr-2"></i><?php echo $t['nav']['admin']; ?>
                </a> -->
            </div>

            <div class="flex items-center space-x-3 sm:space-x-4">
                <!-- Language Toggle -->
                <div class="language-toggle flex bg-slate-100 rounded-xl p-1 shadow-inner">
                    <a href="<?php echo url($page, 'es'); ?>" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition-all duration-300 <?php echo $lang === 'es' ? 'bg-white text-radio-teal shadow-lg' : 'text-slate-600 hover:text-slate-800 hover:bg-slate-200'; ?>">ES</a>
                    <a href="<?php echo url($page, 'gl'); ?>" class="px-3 sm:px-4 py-2 rounded-lg text-xs sm:text-sm font-bold transition-all duration-300 <?php echo $lang === 'gl' ? 'bg-white text-radio-teal shadow-lg' : 'text-slate-600 hover:text-slate-800 hover:bg-slate-200'; ?>">GL</a>
                </div>

                <!-- Mobile menu button -->
                <button id="mobile-menu-button" onclick="toggleMobileMenu()" class="md:hidden p-3 rounded-xl text-slate-600 hover:text-slate-800 hover:bg-slate-100 transition-all duration-300 relative z-50">
                    <i class="fas fa-bars text-lg hamburger-icon"></i>
                </button>
            </div>
        </div>

        <!-- Mobile Navigation -->
        <div id="mobile-menu" class="mobile-menu fixed inset-x-0 top-16 bg-white shadow-xl border-t border-slate-200 z-40 hidden">
            <div class="max-w-7xl mx-auto px-4 py-6">
                <div class="flex flex-col space-y-1">
                    <a href="<?php echo url('home'); ?>" class="mobile-nav-link text-slate-700 hover:text-radio-teal px-4 py-4 rounded-xl hover:bg-slate-50 font-bold transition-all duration-300 flex items-center <?php echo $page === 'home' ? 'bg-radio-teal bg-opacity-10 text-radio-teal' : ''; ?>">
                        <i class="fas fa-home w-6 mr-3 text-center"></i>
                        <?php echo $t['nav']['home']; ?>
                    </a>
                    <a href="<?php echo url('about'); ?>" class="mobile-nav-link text-slate-700 hover:text-radio-teal px-4 py-4 rounded-xl hover:bg-slate-50 font-bold transition-all duration-300 flex items-center <?php echo $page === 'about' ? 'bg-radio-teal bg-opacity-10 text-radio-teal' : ''; ?>">
                        <i class="fas fa-users w-6 mr-3 text-center"></i>
                        <?php echo $t['nav']['about']; ?>
                    </a>
                    <a href="<?php echo url('contact'); ?>" class="mobile-nav-link text-slate-700 hover:text-radio-teal px-4 py-4 rounded-xl hover:bg-slate-50 font-bold transition-all duration-300 flex items-center <?php echo $page === 'contact' ? 'bg-radio-teal bg-opacity-10 text-radio-teal' : ''; ?>">
                        <i class="fas fa-envelope w-6 mr-3 text-center"></i>
                        <?php echo $t['nav']['contact']; ?>
                    </a>
                </div>
                
                <!-- Language Toggle Mobile -->
                <div class="mt-6 pt-6 border-t border-slate-200">
                    <p class="text-sm font-semibold text-slate-500 mb-3">Idioma / Language</p>
                    <div class="flex space-x-2">
                        <a href="<?php echo url($page, 'es'); ?>" class="flex-1 px-4 py-3 rounded-lg text-center font-bold transition-all duration-300 <?php echo $lang === 'es' ? 'bg-radio-teal text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">Español</a>
                        <a href="<?php echo url($page, 'gl'); ?>" class="flex-1 px-4 py-3 rounded-lg text-center font-bold transition-all duration-300 <?php echo $lang === 'gl' ? 'bg-radio-teal text-white shadow-lg' : 'bg-slate-100 text-slate-600 hover:bg-slate-200'; ?>">Galego</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</nav>