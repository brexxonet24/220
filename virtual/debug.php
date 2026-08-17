<?php
// ARCHIVO TEMPORAL DE DIAGNÓSTICO - ELIMINAR DESPUÉS DE RESOLVER EL ERROR
// Acceder a: virtual.isft220.edu.ar/virtual/debug.php

echo "<h2>PHP Version</h2>";
echo phpversion();

echo "<h2>Extensions</h2>";
echo "calendar: " . (extension_loaded('calendar') ? '✅' : '❌') . "<br>";
echo "json: "     . (extension_loaded('json')     ? '✅' : '❌') . "<br>";
echo "session: "  . (extension_loaded('session')  ? '✅' : '❌') . "<br>";

echo "<h2>Directorios</h2>";
$root = __DIR__;
$dirs = [
    $root . '/data',
    $root . '/data/tracking',
    $root . '/data/calificaciones',
    $root . '/data/calendario',
];
foreach ($dirs as $d) {
    $exists   = is_dir($d)     ? '✅ existe'   : '❌ NO existe';
    $writable = is_writable($d)? '✅ escribible': '❌ NO escribible';
    echo basename($d) . ": $exists · $writable<br>";
}

echo "<h2>Archivos JSON</h2>";
$files = [
    $root . '/data/usuarios.json',
    $root . '/data/materias.json',
    $root . '/data/actividades.json',
];
foreach ($files as $f) {
    $exists   = file_exists($f) ? '✅ existe'   : '❌ NO existe';
    $readable = is_readable($f) ? '✅ legible'  : '❌ NO legible';
    echo basename($f) . ": $exists · $readable<br>";
}

echo "<h2>Test arrow function (PHP 7.4+)</h2>";
try {
    $arr = [1,2,3];
    $r = array_filter($arr, fn($x) => $x > 1);
    echo "✅ Arrow functions OK<br>";
} catch (Throwable $e) {
    echo "❌ " . $e->getMessage() . "<br>";
}

echo "<h2>Test match (PHP 8.0+)</h2>";
try {
    $v = 'test';
    $r = match($v) { 'test' => 'ok', default => 'no' };
    echo "✅ match() OK<br>";
} catch (Throwable $e) {
    echo "❌ match() no disponible: " . $e->getMessage() . "<br>";
}

echo "<h2>Test config.php</h2>";
try {
    define('ROOT', __DIR__);
    require_once __DIR__ . '/config.php';
    echo "✅ config.php cargado OK<br>";
    echo "BASE_URL: " . BASE_URL . "<br>";
    echo "DATA_DIR: " . DATA_DIR . "<br>";
} catch (Throwable $e) {
    echo "❌ Error en config.php: " . $e->getMessage() . "<br>";
    echo "Línea: " . $e->getLine() . "<br>";
}
