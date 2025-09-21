<?php
session_start();

// Sistema de idiomas
$lang = isset($_GET['lang']) ? $_GET['lang'] : (isset($_SESSION['lang']) ? $_SESSION['lang'] : 'es');
$_SESSION['lang'] = $lang;

// Página actual
$page = isset($_GET['page']) ? $_GET['page'] : 'home';

// Incluir configuración y traducciones
include 'config/config.php';
include 'config/translations.php';

$t = $translations[$lang];

// Función para generar URLs
function url($page, $lang = null) {
    $current_lang = $GLOBALS['lang'] ?? 'es';
    $lang = $lang ?? $current_lang;
    return "?page={$page}&lang={$lang}";
}
?>

<!DOCTYPE html>
<html lang="<?php echo $lang; ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Radio Rías<?php echo $t['nav'][$page] ?? 'Inicio'; ?></title>
    <link rel="icon" type="image/png" href="assets/images/radiomorrazo-logo.png">
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/floating-widget.css">
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
<body>
    <!-- Navigation -->
    <?php include 'includes/header.php'; ?>

    <!-- Main Content -->
    <main class="min-h-screen">
        <?php
        $page_file = "pages/{$page}.php";
        if (file_exists($page_file)) {
            include $page_file;
        } else {
            include 'pages/home.php';
        }
        ?>
    </main>

    <!-- Footer -->
    <?php include 'includes/footer.php'; ?>

    <script src="assets/js/main.js"></script>
    
    <!-- Configuración de RadioRías -->
    <script src="config/radio-config.js?v=<?php echo time(); ?>"></script>
</body>
</html>