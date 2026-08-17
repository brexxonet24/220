<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config.php';
if (!isset($_SESSION['user_id'])) redirect('/index.php');
$user = findUser($_SESSION['user_id']);
if (!$user || $user['rol'] !== 'alumno') redirect('/index.php');

$page_title  = 'Mis Actividades';
$materias    = getMaterias();
$actividades = getActividades();
$ahora = date('H:i');
$hoy   = date('Y-m-d');

$anio = (int)($user['anio_cursada'] ?? 0);
$misMaterias = $anio
    ? array_values(array_filter($materias, fn($m) => (int)$m['anio'] === $anio))
    : [];

// IDs de materias del alumno (por año asignado)
$matIds = array_map('intval', array_column($misMaterias, 'id'));

$filtroMat = (int)($_GET['materia'] ?? 0);

// Todas las actividades visibles para este alumno
$visibles = array_values(array_filter($actividades, fn($a) =>
    ($a['visible'] ?? true) && in_array((int)($a['materia_id'] ?? 0), $matIds)
));

if ($filtroMat) {
    $visibles = array_values(array_filter($visibles, fn($a) => (int)$a['materia_id'] === $filtroMat));
}

require_once ROOT . '/includes/header.php';
?>

<?php if (empty($matIds)): ?>
<div class="alert alert-warning">
  Tu cuenta no tiene un año asignado todavía. Contactá al administrador para que te asigne el año de cursada.
</div>
<?php else: ?>

<div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
  <a href="?" class="btn <?= !$filtroMat ? 'btn-primary' : '' ?>">Todas</a>
  <?php foreach ($misMaterias as $m): ?>
  <a href="?materia=<?= (int)$m['id'] ?>" class="btn <?= $filtroMat === (int)$m['id'] ? 'btn-primary' : '' ?>">
    <?= h($m['nombre']) ?>
  </a>
  <?php endforeach; ?>
</div>

<?php if (empty($visibles)): ?>
<div style="text-align:center;padding:60px 20px;color:var(--text3)">
  <div style="font-size:40px;margin-bottom:12px">📋</div>
  <p>Sin actividades disponibles por ahora.</p>
</div>
<?php else: ?>

<?php foreach ($visibles as $a):
    [$bl, $bc] = tipoBadge($a['tipo'] ?? '');
    $mat = findMateria((int)($a['materia_id'] ?? 0));

    // Leer sesión con protección total
    $sesion = [];
    try { $sesion = getTracking((int)$user['id'], (int)$a['id']); } catch (Throwable $e) {}

    $comp      = (bool)($sesion['completada'] ?? false);
    $saMin     = intdiv((int)($sesion['sa'] ?? 0), 60);
    $enHorario = $ahora >= ($a['hora_inicio'] ?? '00:00') && $ahora <= ($a['hora_fin'] ?? '23:59');
    $enFecha   = $hoy >= ($a['fecha_desde'] ?? '0000-00-00') && $hoy <= ($a['fecha_hasta'] ?? '9999-99-99');
    $disponible = $enHorario && $enFecha;
    $req       = (int)($a['minimo_minutos'] ?? 0);
    $pct       = $req > 0 ? min(100, round($saMin / $req * 100)) : ($comp ? 100 : 0);
?>
<div class="card" style="opacity:<?= $disponible || $comp ? 1 : .7 ?>;margin-bottom:10px">
  <div style="display:flex;align-items:flex-start;gap:12px">
    <div style="flex:1">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px;flex-wrap:wrap">
        <span class="badge <?= $bc ?>"><?= $bl ?></span>
        <?php if ($mat): ?>
        <span style="font-size:11px;color:var(--text3)"><?= h($mat['nombre']) ?></span>
        <?php endif; ?>
        <?php if ($comp): ?>
          <span class="badge badge-green">✓ Completada</span>
        <?php elseif (!$enFecha): ?>
          <span class="badge badge-gray">Fuera de fecha</span>
        <?php elseif (!$enHorario): ?>
          <span class="badge badge-amber">Fuera de horario</span>
        <?php else: ?>
          <span class="badge badge-green" style="background:#C0DD97;color:#27500A">Disponible ahora</span>
        <?php endif; ?>
      </div>
      <div style="font-size:15px;font-weight:600;margin-bottom:4px"><?= h($a['titulo']) ?></div>
      <div style="font-size:12px;color:var(--text3);margin-bottom:8px">
        Horario: <?= h($a['hora_inicio'] ?? '') ?>–<?= h($a['hora_fin'] ?? '') ?>
        · Mínimo: <?= $req ?> min
        <?php if (($a['fecha_desde'] ?? '') && ($a['fecha_desde'] ?? '') !== $hoy): ?>
        · <?= date('d/m', strtotime($a['fecha_desde'])) ?> al <?= date('d/m', strtotime($a['fecha_hasta'] ?? $hoy)) ?>
        <?php endif; ?>
      </div>
      <?php if (!empty($sesion)): ?>
      <div style="display:flex;align-items:center;gap:8px">
        <div class="prog" style="flex:1;max-width:200px">
          <div class="prog-fill <?= $pct >= 100 ? 'suc' : '' ?>" style="width:<?= $pct ?>%"></div>
        </div>
        <span style="font-size:12px;color:var(--text2)"><?= $saMin ?> / <?= $req ?> min</span>
      </div>
      <?php endif; ?>
    </div>
    <?php if ($disponible || $comp): ?>
    <a href="/alumno/actividad.php?id=<?= (int)$a['id'] ?>"
       class="btn <?= $disponible && !$comp ? 'btn-primary' : '' ?>"
       style="flex-shrink:0">
      <?= $comp ? 'Revisar →' : ($disponible ? 'Iniciar →' : 'Ver') ?>
    </a>
    <?php endif; ?>
  </div>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php endif; ?>

<?php require_once ROOT . '/includes/footer.php'; ?>
