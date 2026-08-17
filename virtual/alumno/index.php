<?php
define('ROOT', dirname(__DIR__));
require_once ROOT . '/config.php';
if (!isset($_SESSION['user_id'])) redirect('/index.php');
$user = findUser($_SESSION['user_id']);
if (!$user || $user['rol'] !== 'alumno') redirect('/index.php');

$page_title  = 'Inicio';
$materias    = getMaterias();
$actividades = getActividades();
$ahora = date('H:i');
$hoy   = date('Y-m-d');

$anio        = (int)($user['anio_cursada'] ?? 0);
$misMaterias = $anio
    ? array_values(array_filter($materias, fn($m) => (int)$m['anio'] === $anio))
    : [];
$matIds = array_column($misMaterias, 'id');

// Actividades visibles para este alumno
$userMatIds = $user['materia_ids'] ?? $matIds;
$disps = array_values(array_filter($actividades, fn($a) =>
    ($a['visible'] ?? true) &&
    in_array($a['materia_id'], $userMatIds) &&
    ($a['fecha_desde'] ?? '0000-00-00') <= $hoy &&
    ($a['fecha_hasta'] ?? '9999-99-99') >= $hoy
));
$actDisp = array_values(array_filter($disps, fn($a) =>
    $ahora >= ($a['hora_inicio'] ?? '00:00') && $ahora <= ($a['hora_fin'] ?? '23:59')
));

// Sesiones del alumno
$misSesiones = [];
foreach ($actividades as $a) {
    try {
        $t = getTracking((int)$user['id'], (int)$a['id']);
        if (!empty($t)) $misSesiones[(int)$a['id']] = $t;
    } catch (Throwable $e) { /* continuar */ }
}
$completadas = count(array_filter($misSesiones, fn($s) => $s['completada'] ?? false));
$totalMin    = array_sum(array_map(fn($s) => intdiv((int)($s['sa'] ?? 0), 60), $misSesiones));

// Calificaciones
$todasCalif = [];
foreach ($misMaterias as $m) {
    try {
        $c = getCalif((int)$m['id']);
        foreach ($c as $v) {
            if ((int)($v['alumno_id'] ?? 0) === (int)$user['id']) $todasCalif[] = $v;
        }
    } catch (Throwable $e) { /* continuar */ }
}
$notas = array_column($todasCalif, 'nota');
$promGeneral = count($notas) ? round(array_sum($notas) / count($notas), 1) : null;

require_once ROOT . '/includes/header.php';
?>
<div style="margin-bottom:20px">
  <h2 style="font-size:18px;font-weight:600">Hola, <?=h($user['nombre'])?>! 👋</h2>
  <?php if ($anio): ?>
  <p style="color:var(--text2);margin-top:4px">Cursás <?=$anio?>° año · <?=count($misMaterias)?> materias</p>
  <?php else: ?>
  <div class="alert alert-warning" style="margin-top:8px">Tu cuenta aún no tiene año asignado. Contactá al administrador.</div>
  <?php endif; ?>
</div>

<div class="grid g4" style="margin-bottom:20px">
  <div class="stat"><div class="stat-label">Actividades completadas</div><div class="stat-val"><?=$completadas?></div></div>
  <div class="stat"><div class="stat-label">Minutos activos totales</div><div class="stat-val"><?=$totalMin?></div></div>
  <div class="stat">
    <div class="stat-label">Promedio general</div>
    <div class="stat-val" style="color:<?=$promGeneral===null?'var(--text3)':($promGeneral>=6?'var(--suc)':($promGeneral>=4?'var(--war)':'var(--dan)'))?>">
      <?=$promGeneral ?? '—'?>
    </div>
  </div>
  <div class="stat">
    <div class="stat-label">Disp. ahora</div>
    <div class="stat-val" style="color:<?=count($actDisp)?'var(--suc)':'var(--text3)'?>"><?=count($actDisp)?></div>
  </div>
</div>

<?php if (count($actDisp)): ?>
<div class="card" style="border-left:3px solid var(--suc);margin-bottom:16px">
  <div class="card-header"><h3 class="card-title">🟢 Disponibles ahora</h3></div>
  <?php foreach ($actDisp as $a):
    [$bl, $bc] = tipoBadge($a['tipo'] ?? '');
    $comp = ($misSesiones[(int)$a['id']]['completada'] ?? false);
  ?>
  <div style="display:flex;align-items:center;gap:10px;padding:9px 0;border-bottom:1px solid var(--border)">
    <span class="badge <?=$bc?>"><?=$bl?></span>
    <div style="flex:1">
      <div style="font-size:14px;font-weight:500"><?=h($a['titulo'])?></div>
      <div style="font-size:12px;color:var(--text3)"><?=$a['minimo_minutos'] ?? 0?> min mínimo · hasta <?=h($a['hora_fin'] ?? '')?></div>
    </div>
    <?php if ($comp): ?><span class="badge badge-green">✓ Completada</span><?php endif; ?>
    <a href="/alumno/actividad.php?id=<?=(int)$a['id']?>" class="btn btn-primary btn-sm">Entrar →</a>
  </div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<div class="grid g2">
<div>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Mis materias</h3>
    </div>
    <?php if (empty($misMaterias)): ?>
    <p style="color:var(--text3);font-size:13px">Sin materias asignadas. Contactá al administrador para que te asigne un año.</p>
    <?php else: ?>
    <?php foreach ($misMaterias as $m):
      $actsMat  = array_filter($actividades, fn($a) => $a['materia_id'] == $m['id'] && ($a['visible'] ?? true));
      $compMat  = 0;
      foreach ($actsMat as $a) {
          if ($misSesiones[(int)$a['id']]['completada'] ?? false) $compMat++;
      }
    ?>
    <div class="mat-card campo-<?=h($m['campo'] ?? '')?>" style="margin-bottom:6px">
      <div class="mat-nombre"><?=h($m['nombre'])?></div>
      <div class="mat-meta">
        <span><?=(int)$m['horas']?> hs</span>
        <span>·</span>
        <span><?=$compMat?>/<?=count($actsMat)?> actividades</span>
        <a href="/alumno/actividades.php?materia=<?=(int)$m['id']?>" style="margin-left:auto;font-size:11px">Ver →</a>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div>
  <div class="card">
    <div class="card-header"><h3 class="card-title">Últimas calificaciones</h3></div>
    <?php if (empty($todasCalif)): ?>
    <p style="color:var(--text3);font-size:13px">Sin calificaciones registradas aún.</p>
    <?php else: ?>
    <?php foreach (array_slice(array_reverse($todasCalif), 0, 6) as $c):
      $mat = findMateria((int)($c['materia_id'] ?? 0));
      $col = ($c['nota'] ?? 0) >= 6 ? 'var(--suc)' : (($c['nota'] ?? 0) >= 4 ? 'var(--war)' : 'var(--dan)');
    ?>
    <div style="display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid var(--border)">
      <div style="width:36px;height:36px;border-radius:50%;background:var(--surface2);display:flex;align-items:center;justify-content:center;font-size:15px;font-weight:700;color:<?=$col?>"><?=$c['nota']?></div>
      <div>
        <div style="font-size:13px;font-weight:500"><?=$mat ? h($mat['nombre']) : '—'?></div>
        <div style="font-size:11px;color:var(--text3)"><?=h($c['periodo'] ?? '')?> · <?=substr($c['fecha'] ?? '', 0, 10)?></div>
      </div>
    </div>
    <?php endforeach; ?>
    <a href="/alumno/calificaciones.php" style="font-size:12px;display:block;margin-top:8px">Ver todas →</a>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title">Accesos rápidos</h3></div>
    <div style="display:flex;flex-direction:column;gap:8px">
      <a href="/alumno/actividades.php" class="btn">📋 Ver todas las actividades</a>
      <a href="/alumno/calificaciones.php" class="btn">📊 Mis calificaciones</a>
      <a href="/alumno/progreso.php" class="btn">📈 Mi progreso</a>
      <a href="/alumno/calendario.php" class="btn">📅 Calendario</a>
    </div>
  </div>
</div>
</div>
<?php require_once ROOT . '/includes/footer.php'; ?>
