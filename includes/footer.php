<!-- Footer -->
<footer class="bg-black text-white py-8 sm:py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid sm:grid-cols-2 lg:grid-cols-4 gap-8 lg:gap-12">
            <div class="sm:col-span-2 lg:col-span-2">
                <div class="flex items-center space-x-4 mb-6">
                    <img alt="Radio Logo" loading="lazy" width="48" height="48" decoding="async" data-nimg="1" class="rounded-full shadow-2xl" style="color:transparent" src="assets/images/radiomorrazo-logo.png">
                    <span class="text-2xl font-black">Radio Rías</span>
                </div>
                <p class="text-slate-300 mb-6 leading-relaxed text-lg">
                    La voz de la península de Morrazo, conectando comunidades a través de las ondas.
                </p>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-6 text-radio-teal"><?php echo $t['footer']['quick_links']; ?></h3>
                <div class="space-y-3">
                    <a href="<?php echo url('home'); ?>" class="block text-slate-300 hover:text-radio-teal transition-all duration-300 font-medium hover:translate-x-2"><?php echo $t['nav']['home']; ?></a>
                    <!-- <a href="<?php echo url('programs'); ?>" class="block text-slate-300 hover:text-radio-teal transition-all duration-300 font-medium hover:translate-x-2"><?php echo $t['nav']['programs']; ?></a> -->
                    <!-- <a href="<?php echo url('podcasts'); ?>" class="block text-slate-300 hover:text-radio-teal transition-all duration-300 font-medium hover:translate-x-2"><?php echo $t['nav']['podcasts']; ?></a> -->
                    <a href="<?php echo url('contact'); ?>" class="block text-slate-300 hover:text-radio-teal transition-all duration-300 font-medium hover:translate-x-2"><?php echo $t['nav']['contact']; ?></a>
                </div>
            </div>
            
            <div>
                <h3 class="text-xl font-bold mb-6 text-radio-teal"><?php echo $t['footer']['contact_info']; ?></h3>
                <div class="space-y-3 text-slate-300">
                    <p class="flex items-center"><i class="fas fa-map-marker-alt mr-3 text-radio-teal"></i><?php echo SITE_ADDRESS; ?></p>
                    <p class="flex items-center"><i class="fas fa-phone mr-3 text-radio-teal"></i><?php echo SITE_PHONE; ?></p>
                    <p class="flex items-center"><i class="fas fa-envelope mr-3 text-radio-teal"></i><?php echo SITE_EMAIL; ?></p>
                </div>
            </div>
        </div>
        
        <div class="border-t border-slate-700 mt-8 pt-8 text-center">
            <p class="text-slate-400 text-lg"><?php echo $t['footer']['rights']; ?></p>
        </div>
    </div>
</footer>

<!-- Widget Flotante JavaScript - Versión Simple -->
<script src="js/simple-radio-widget.js?v=<?php echo time(); ?>"></script>