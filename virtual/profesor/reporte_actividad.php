<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='profesor') redirect('/index.php');
$page_title='Reporte de actividad';
$id=(int)($_GET['id']??0);
$act=null;
foreach(getActividades() as $a){ if($a['id']===$id&&$a['profesor_id']===$user['id']){$act=$a;break;} }
if(!$act){ flash('Actividad no encontrada.','danger'); redirect('/profesor/reportes.php'); }
$usuarios=getUsuarios();
$alumnos=array_values(array_filter($usuarios,fn($u)=>$u['rol']==='alumno'&&($u['activo']??true)));
$mat=findMateria($act['materia_id']??0);
[$bl,$bc]=tipoBadge($act['tipo']);

$sesiones=[];
foreach($alumnos as $alu){
    $t=getTracking($alu['id'],$act['id']);
    if(!empty($t)) $sesiones[]=array_merge($t,['alumno'=>$alu]);
}
usort($sesiones,fn($a,$b)=>($b['sa']??0)<=>($a['sa']??0));
$req=($act['minimo_minutos']??0)*60;
require_once ROOT.'/includes/header.php';
?>
<div class="card" style="margin-bottom:12px">
  <div style="display:flex;align-items:flex-start;gap:12px">
    <div style="flex:1">
      <span class="badge <?=$bc?>"><?=$bl?></span>
      <h2 style="font-size:17px;margin:6px 0 4px"><?=h($act['titulo'])?></h2>
      <div style="font-size:13px;color:var(--text2)"><?=$mat?h($mat['nombre']):''?> · <?=h($act['hora_inicio']??'')?>–<?=h($act['hora_fin']??'')?> · Mínimo: <?=$act['minimo_minutos']?> min</div>
    </div>
    <a href="/profesor/reportes.php" class="btn btn-sm">← Volver</a>
  </div>
</div>

<?php if(empty($sesiones)): ?>
<div class="alert alert-info">Ningún alumno ha iniciado esta actividad todavía.</div>
<?php else: ?>
<div class="grid g4" style="margin-bottom:16px">
  <?php $comp=count(array_filter($sesiones,fn($s)=>$s['completada']??false)); ?>
  <div class="stat"><div class="stat-label">Participaron</div><div class="stat-val"><?=count($sesiones)?></div></div>
  <div class="stat"><div class="stat-label">Completaron</div><div class="stat-val" style="color:var(--suc)"><?=$comp?></div><div class="stat-sub"><?=count($sesiones)?round($comp/count($sesiones)*100):0?>%</div></div>
  <div class="stat"><div class="stat-label">Prom. activo</div><div class="stat-val"><?=count($sesiones)?fmtMinutos((int)round(array_sum(array_column($sesiones,'sa'))/count($sesiones))):'-'?></div></div>
  <div class="stat"><div class="stat-label">Sin iniciar</div><div class="stat-val" style="color:var(--war)"><?=count($alumnos)-count($sesiones)?></div></div>
</div>

<div class="card">
  <div class="card-header"><h3 class="card-title">Detalle por alumno</h3></div>
  <div class="table-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th>Alumno</th>
        <th>T. activo</th>
        <th>T. idle</th>
        <th>Progreso</th>
        <th>Tab changes</th>
        <th>Salidas FS</th>
        <th>Pegados bloq.</th>
        <th>Estado</th>
        <th>Última sesión</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($sesiones as $s):
      $sa=$s['sa']??0; $si=$s['si']??0;
      $pct=$req>0?min(100,round($sa/$req*100)):100;
      $col=$pct>=100?'var(--suc)':($pct>=50?'var(--war)':'var(--dan)');
      $alertas=($s['tc']??0)+($s['fe']??0);
    ?>
    <tr>
      <td><strong><?=h($s['alumno']['apellido'].', '.$s['alumno']['nombre'])?></strong></td>
      <td><?=fmtMinutos($sa)?></td>
      <td><?=fmtMinutos($si)?></td>
      <td>
        <div style="display:flex;align-items:center;gap:6px">
          <div class="prog" style="width:80px">
            <div class="prog-fill" style="width:<?=$pct?>%;background:<?=$col?>"></div>
          </div>
          <span style="font-size:12px;color:<?=$col?>"><?=$pct?>%</span>
        </div>
      </td>
      <td><?php if($s['tc']??0): ?><span class="badge badge-red"><?=$s['tc']?></span><?php else: ?>—<?php endif; ?></td>
      <td><?php if($s['fe']??0): ?><span class="badge badge-amber"><?=$s['fe']?></span><?php else: ?>—<?php endif; ?></td>
      <td><?php if($s['pa']??0): ?><span class="badge badge-red"><?=$s['pa']?></span><?php else: ?>—<?php endif; ?></td>
      <td><?=($s['completada']??false)?'<span class="badge badge-green">Completada</span>':'<span class="badge badge-amber">En curso</span>'?></td>
      <td style="font-size:11px;color:var(--text3)"><?=isset($s['ts'])?substr($s['ts'],0,16):'-'?></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>

<?php if($act['tipo']==='codigo'): ?>
<div class="card">
  <div class="card-header"><h3 class="card-title">💻 Entregas de código</h3></div>
  <?php foreach($sesiones as $s):
    if(empty($s['codigo_entregado'])) continue;
  ?>
  <div style="border:1px solid var(--border);border-radius:var(--r);padding:12px;margin-bottom:10px">
    <div style="font-weight:500;margin-bottom:6px"><?=h($s['alumno']['apellido'].', '.$s['alumno']['nombre'])?></div>
    <pre style="background:var(--surface2);padding:10px;border-radius:var(--r);font-size:12px;overflow-x:auto;white-space:pre-wrap"><?=h($s['codigo_entregado'])?></pre>
  </div>
  <?php endforeach; ?>
  <?php if(!array_filter($sesiones,fn($s)=>!empty($s['codigo_entregado']))): ?>
  <p style="color:var(--text3);font-size:13px">Sin entregas de código aún.</p>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php endif; ?>
<?php require_once ROOT.'/includes/footer.php'; ?>
