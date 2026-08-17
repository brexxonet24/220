<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='profesor') redirect('/index.php');
$page_title='Reportes de seguimiento';
$actividades=array_values(array_filter(getActividades(),fn($a)=>$a['profesor_id']==$user['id']));
$usuarios=getUsuarios();
$alumnos=array_values(array_filter($usuarios,fn($u)=>$u['rol']==='alumno'&&($u['activo']??true)));
$materias=getMaterias();

// Agrupar por materia
$porMateria=[];
foreach($actividades as $a) $porMateria[$a['materia_id']][]=$a;
require_once ROOT.'/includes/header.php';
?>
<?php if(empty($actividades)): ?>
<div class="alert alert-info">No tenés actividades creadas aún. <a href="/profesor/actividades.php?nueva=1">Crear una</a>.</div>
<?php else: ?>
<?php foreach($porMateria as $mid=>$acts):
  $mat=findMateria($mid);
?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title"><?=$mat?h($mat['nombre']):'Materia'?></h3>
  </div>
  <?php foreach($acts as $act): 
    [$bl,$bc]=tipoBadge($act['tipo']);
    $sesiones=[];
    foreach($alumnos as $alu){
      $t=getTracking($alu['id'],$act['id']);
      if(!empty($t)) $sesiones[$alu['id']]=$t;
    }
    $totalPart=count($sesiones);
    $completaron=count(array_filter($sesiones,fn($s)=>$s['completada']??false));
    $avgAct=$totalPart?round(array_sum(array_map(fn($s)=>$s['sa']??0,$sesiones))/count($sesiones)/60):0;
  ?>
  <div style="border:1px solid var(--border);border-radius:var(--r);padding:14px;margin-bottom:12px">
    <div style="display:flex;align-items:center;gap:10px;margin-bottom:10px">
      <span class="badge <?=$bc?>"><?=$bl?></span>
      <strong style="flex:1"><?=h($act['titulo'])?></strong>
      <span style="font-size:12px;color:var(--text3)"><?=h($act['hora_inicio']??'')?>–<?=h($act['hora_fin']??'')?></span>
      <a href="/profesor/reporte_actividad.php?id=<?=$act['id']?>" class="btn btn-xs btn-primary">Ver detalle →</a>
    </div>
    <div class="grid g4">
      <div class="stat" style="padding:10px 12px">
        <div class="stat-label">Participaron</div>
        <div class="stat-val" style="font-size:20px"><?=$totalPart?></div>
      </div>
      <div class="stat" style="padding:10px 12px">
        <div class="stat-label">Completaron</div>
        <div class="stat-val" style="font-size:20px;color:var(--suc)"><?=$completaron?></div>
      </div>
      <div class="stat" style="padding:10px 12px">
        <div class="stat-label">Prom. activo</div>
        <div class="stat-val" style="font-size:20px"><?=$avgAct?> min</div>
      </div>
      <div class="stat" style="padding:10px 12px">
        <div class="stat-label">Sin actividad</div>
        <div class="stat-val" style="font-size:20px;color:var(--war)"><?=count($alumnos)-$totalPart?></div>
      </div>
    </div>
    <?php if($totalPart>0): ?>
    <div style="margin-top:10px">
      <?php foreach($sesiones as $uid=>$s):
        $alu=null; foreach($alumnos as $al){ if($al['id']==$uid){$alu=$al;break;} }
        if(!$alu) continue;
        $req=($act['minimo_minutos']??0)*60;
        $sa=$s['sa']??0; $si=$s['si']??0;
        $pct=$req>0?min(100,round($sa/$req*100)):100;
        $col=$pct>=100?'suc':($pct>=50?'':'war');
      ?>
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:5px;font-size:12px">
        <span style="width:120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:var(--text2)"><?=h($alu['apellido'].', '.$alu['nombre'])?></span>
        <div class="prog" style="flex:1">
          <div class="prog-fill <?=$col?>" style="width:<?=$pct?>%"></div>
        </div>
        <span style="width:45px;text-align:right;color:var(--text3)"><?=fmtMinutos($sa)?></span>
        <?php if(($s['tc']??0)+($s['fe']??0)>0): ?>
        <span class="badge badge-red" style="font-size:10px">⚠ <?=($s['tc']??0)+($s['fe']??0)?></span>
        <?php else: ?><span style="width:36px"></span><?php endif; ?>
        <?php if($s['pa']??0): ?>
        <span class="badge badge-amber" style="font-size:10px">📋 <?=$s['pa']?></span>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
      <div style="font-size:10px;color:var(--text3);margin-top:4px">⚠ = cambios de tab/pantalla · 📋 = intentos de pegado</div>
    </div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
</div>
<?php endforeach; ?>
<?php endif; ?>
<?php require_once ROOT.'/includes/footer.php'; ?>
