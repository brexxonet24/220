<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='profesor') redirect('/index.php');
if(!($user['activo']??false)){
    session_destroy();
    header('Location: /index.php?msg=pendiente');
    exit;
}
$page_title='Dashboard Docente';
$materias=getMaterias();
$misMaterias=array_values(array_filter($materias,fn($m)=>in_array($m['id'],$user['materia_ids']??[])));
$actividades=getActividades();
$misActividades=array_values(array_filter($actividades,fn($a)=>$a['profesor_id']==$user['id']));
$usuarios=getUsuarios();
$alumnos=array_filter($usuarios,fn($u)=>$u['rol']==='alumno'&&($u['activo']??true));

// Count sessions for this professor's activities
$totalSesiones=0;
$actIds=array_column($misActividades,'id');
foreach($actIds as $aid){
    foreach($alumnos as $alu){
        $t=getTracking($alu['id'],$aid);
        if(!empty($t)) $totalSesiones++;
    }
}
require_once ROOT.'/includes/header.php';
?>
<div class="grid g4" style="margin-bottom:20px">
  <div class="stat"><div class="stat-label">Mis materias</div><div class="stat-val"><?=count($misMaterias)?></div></div>
  <div class="stat"><div class="stat-label">Actividades creadas</div><div class="stat-val"><?=count($misActividades)?></div></div>
  <div class="stat"><div class="stat-label">Alumnos</div><div class="stat-val"><?=count($alumnos)?></div></div>
  <div class="stat"><div class="stat-label">Sesiones registradas</div><div class="stat-val"><?=$totalSesiones?></div></div>
</div>

<div class="grid g2">
<div>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Mis materias</h3>
      <a href="/profesor/actividades.php" class="btn btn-sm btn-primary">+ Nueva actividad</a>
    </div>
    <?php if(empty($misMaterias)): ?>
    <p style="color:var(--text3);font-size:13px">No tenés materias asignadas aún. Contactá al administrador.</p>
    <?php else: ?>
    <?php foreach($misMaterias as $m): ?>
    <div class="mat-card campo-<?=$m['campo']?>" style="margin-bottom:8px">
      <div class="mat-anio"><?=$m['anio']?>° año</div>
      <div class="mat-nombre"><?=h($m['nombre'])?></div>
      <div class="mat-meta">
        <span><?=$m['horas']?> hs</span>
        <span>·</span>
        <?php $actCount=count(array_filter($misActividades,fn($a)=>$a['materia_id']==$m['id'])); ?>
        <span><?=$actCount?> actividad(es)</span>
        <a href="/profesor/actividades.php?materia=<?=$m['id']?>" style="margin-left:auto;font-size:11px">Ver →</a>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>
</div>

<div>
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Actividades recientes</h3>
    </div>
    <?php if(empty($misActividades)): ?>
    <p style="color:var(--text3);font-size:13px">Sin actividades creadas.</p>
    <?php else: ?>
    <?php foreach(array_slice(array_reverse($misActividades),0,5) as $a): ?>
    <?php [$bl,$bc]=tipoBadge($a['tipo']); ?>
    <div style="display:flex;align-items:center;gap:10px;padding:8px 0;border-bottom:1px solid var(--border)">
      <span class="badge <?=$bc?>"><?=$bl?></span>
      <div style="flex:1">
        <div style="font-size:13px;font-weight:500"><?=h($a['titulo'])?></div>
        <div style="font-size:11px;color:var(--text3)"><?=h($a['hora_inicio']??'')?>–<?=h($a['hora_fin']??'')?> · <?=$a['minimo_minutos']?> min mín.</div>
      </div>
      <a href="/profesor/reporte_actividad.php?id=<?=$a['id']?>" class="btn btn-xs">Reporte</a>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title">Accesos rápidos</h3></div>
    <div style="display:flex;flex-direction:column;gap:8px">
      <a href="/profesor/actividades.php" class="btn">📋 Gestionar actividades</a>
      <a href="/profesor/calificaciones.php" class="btn">📊 Cargar calificaciones</a>
      <a href="/profesor/reportes.php" class="btn">📈 Ver reportes de tracking</a>
      <a href="/profesor/calendario.php" class="btn">📅 Calendario de la materia</a>
    </div>
  </div>
</div>
</div>
<?php require_once ROOT.'/includes/footer.php'; ?>
