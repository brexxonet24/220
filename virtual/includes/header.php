<?php
// $page_title, $user, $extra_css, $extra_js deben estar definidos antes de incluir
$page_title = $page_title ?? 'AMET 220';
$extra_css   = $extra_css  ?? [];
$extra_js    = $extra_js   ?? [];
$base = BASE_URL; // ej: '/amet220'

$nav = [];
if ($user['rol'] === 'admin') {
    $nav = [
        ['url' => '/admin/index.php',     'ico' => '⊞', 'label' => 'Dashboard'],
        ['url' => '/admin/profesores.php','ico' => '👤', 'label' => 'Profesores'],
        ['url' => '/admin/alumnos.php',   'ico' => '🎓', 'label' => 'Alumnos'],
        ['url' => '/admin/materias.php',  'ico' => '📚', 'label' => 'Materias'],
    ];
} elseif ($user['rol'] === 'profesor') {
    $nav = [
        ['url' => '/profesor/index.php',           'ico' => '⊞', 'label' => 'Dashboard'],
        ['url' => '/profesor/actividades.php',      'ico' => '📋', 'label' => 'Actividades'],
        ['url' => '/profesor/calificaciones.php',   'ico' => '📊', 'label' => 'Calificaciones'],
        ['url' => '/profesor/reportes.php',         'ico' => '📈', 'label' => 'Reportes'],
        ['url' => '/profesor/calendario.php',       'ico' => '📅', 'label' => 'Calendario'],
    ];
} elseif ($user['rol'] === 'alumno') {
    $nav = [
        ['url' => '/alumno/index.php',          'ico' => '⊞', 'label' => 'Inicio'],
        ['url' => '/alumno/actividades.php',    'ico' => '📋', 'label' => 'Mis Actividades'],
        ['url' => '/alumno/calificaciones.php', 'ico' => '📊', 'label' => 'Calificaciones'],
        ['url' => '/alumno/progreso.php',       'ico' => '📈', 'label' => 'Mi Progreso'],
        ['url' => '/alumno/calendario.php',     'ico' => '📅', 'label' => 'Calendario'],
    ];
}

// Determinar página activa comparando la parte después de BASE_URL
$scriptFull = str_replace('\\', '/', $_SERVER['SCRIPT_NAME'] ?? '');
$scriptRel  = $base !== '' ? str_replace($base, '', $scriptFull) : $scriptFull;

$flash = getFlash();
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title><?=h($page_title)?> — AMET 220</title>
<link rel="stylesheet" href="<?=$base?>/assets/css/style.css">
<?php foreach ($extra_css as $css): ?>
<link rel="stylesheet" href="<?=$css?>">
<?php endforeach; ?>
</head>
<body>
<div class="layout">

<nav class="sidebar" id="sidebar">
  <div class="sidebar-brand">
    <div class="inst">Instituto Superior</div>
    <div class="name">AMET N° 220</div>
    <div class="year">Técnico Superior en Programación</div>
  </div>
  <div class="sidebar-nav">
    <div class="sidebar-section">Menú</div>
    <?php foreach ($nav as $item):
      $active = (strpos($scriptRel, $item['url']) === 0) ? 'active' : '';
    ?>
    <a href="<?=$base.$item['url']?>" class="<?=$active?>">
      <span class="ico"><?=$item['ico']?></span>
      <?=h($item['label'])?>
    </a>
    <?php endforeach; ?>
  </div>
  <div class="sidebar-foot">
    <div class="uname"><?=h($user['nombre'] . ' ' . $user['apellido'])?></div>
    <div><?=ucfirst(h($user['rol']))?> · <a href="<?=$base?>/logout.php" style="color:rgba(255,255,255,.6)">Salir</a></div>
  </div>
</nav>

<div class="main">
  <div class="topbar">
    <button class="menu-toggle" onclick="document.getElementById('sidebar').classList.toggle('open')">☰</button>
    <span class="topbar-title"><?=h($page_title)?></span>
  </div>
  <div class="content">
<?php if ($flash): ?>
<div class="alert alert-<?=h($flash['type'])?>"><?=h($flash['msg'])?></div>
<?php endif; ?>
