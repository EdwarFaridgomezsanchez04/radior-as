<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title : 'Radio Rías - La voz del corazón de Galicia'; ?></title>
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
</head>
<body class="bg-white">

<?php include 'header.php'; ?>

<main>
    <?php echo $content; ?>
</main>

<?php include 'footer.php'; ?>

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

</body>
</html>
