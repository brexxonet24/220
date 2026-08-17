<?php

// echo '<pre>';
// var_dump($_SERVER['REQUEST_URI']); 
// echo '</pre>';


$routes = [
	'/' => 'controllers/home.controller.php',
	'/ficha-inscripcion' => 'controllers/ficha.controller.php',
	'/gracias' => 'views/gracias.view.php',
];

function route($url, $routes)
{
	if (array_key_exists($url, $routes)) { // Se preguntà si lo que ingrese por URI existe (En las rutas qeu declaré)
		
		require $routes[$url]; // Devuelve el valor del Key (controlador)
		
	}else{
		
		require 'views/404.view.php'; // Sino paso la prueba nos duveleve un 404 personalizado.
	};
}

$url = parse_url($_SERVER['REQUEST_URI'])['path']; // Se limpia el URL

// Adaptación para servidor local en subcarpeta
if ($_SERVER['HTTP_HOST'] === 'localhost' || $_SERVER['HTTP_HOST'] === '127.0.0.1') {
    $url = str_replace('/pagina220', '', $url);
    if ($url === '') {
        $url = '/';
    }
}

route($url, $routes);