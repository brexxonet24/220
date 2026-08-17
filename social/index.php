<?php include 'main.php';?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Lato:wght@100;300;400;700&display=swap" rel="stylesheet">   
    <!-- Bootstrap CSS -->
    <link href="assets/css/bootstrap.min.css" rel="stylesheet">
    <!-- Style CSS -->
    <link href="assets/css/style.css" rel="stylesheet">
    <title><?= $title ?></title>
</head>
<body>

    <nav class="navbar bg-light fixed-top" id="bg-nav-fixed">
        <div class="container">
            <a class="navbar-brand text-white" href="/">ISFT 220</a>
        </div>
    </nav>
 
    <div class="container mt-5 pt-5">
        <div class="d-flex align-items-center flex-column ">
            <img class="d-block mx-auto mb-4" src="assets/img/logo-isft-220.png" alt="Logo ISFT 220" width="160">

            <h1 class="display-5"><?= $header_titulo ?></h1>
            <p class="lead mb-4 text-center"><?= $descripcion ?></p>

            <div class="d-grid gap-3 mb-3">

                <!-- Botón WhatsApp (verde) -->
                <a href="https://wa.me/5491126888455" 
                   role="button" 
                   class="btn btn-success btn-lg px-4 me-md-2" 
                   target="_blank">
                   📱 WhatsApp: +54 9 11 2688-8455
                </a>

                <!-- Botones dinámicos según $links -->
                <?php foreach($links as $item => $link): ?>
                    <?php 
                        // Detectar red para asignar color y emoji
                        $nombre = strtolower($link['nombre']);
                        $btnClass = "btn-secondary"; 
                        $emoji = "🔗"; 

                        if (strpos($nombre, 'facebook') !== false) { 
                            $btnClass = "btn-primary"; 
                            $emoji = "📘"; 
                        } elseif (strpos($nombre, 'instagram') !== false) { 
                            $btnClass = "btn-danger"; 
                            $emoji = "📸"; 
                        } elseif (strpos($nombre, 'twitter') !== false || strpos($nombre, 'x') !== false) { 
                            $btnClass = "btn-dark"; 
                            $emoji = "🐦"; 
                        } elseif (strpos($nombre, 'youtube') !== false) { 
                            $btnClass = "btn-danger"; 
                            $emoji = "▶️"; 
                        } elseif (strpos($nombre, 'linkedin') !== false) { 
                            $btnClass = "btn-info"; 
                            $emoji = "💼"; 
                        } elseif (strpos($nombre, 'tiktok') !== false) { 
                            $btnClass = "btn-dark"; 
                            $emoji = "🎵"; 
                        } elseif (strpos($nombre, 'correo') !== false || strpos($nombre, 'mail') !== false) { 
                            $btnClass = "btn-warning"; 
                            $emoji = "✉️"; 
                        }
                    ?>
                    <a href="<?= $link['valor'] ?>" 
                       role="button" 
                       class="btn <?= $btnClass ?> btn-lg px-4 me-md-2" 
                       target="_blank">
                       <?= $emoji ?> <?= $link['nombre'] ?>
                    </a>
                <?php endforeach; ?>

            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
</body>
</html>
