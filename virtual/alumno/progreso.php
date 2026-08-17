<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='alumno') redirect('/index.php');
$page_title='Mi Progreso';
$actividades=getActividades();
$materias=getMaterias();
$anio=$user['anio_cursada']??0;
$misMaterias=$anio?array_values(array_filter($materias,fn($m)=>$m['anio']==$anio)):[];
$matIds=array_column($misMaterias,'id');

// Recopilar todas las sesiones
$sesiones=[];
foreach($actividades as $a){
    if(!in_array($a['materia_id'],$matIds)) continue;
    $t=getTracking($user['id'],$a['id']);
    if(!empty($t)){
        $sesiones[]=array_merge($t,['actividad'=>$a,'mat'=>findMateria($a['materia_id']??0)]);
    }
}
$totalSA=array_sum(array_map(fn($s)=>$s['sa']??0,$sesiones));
$totalSI=array_sum(array_map(fn($s)=>$s['si']??0,$sesiones));
$totalTC=array_sum(array_map(fn($s)=>$s['tc']??0,$sesiones));
$totalPA=array_sum(array_map(fn($s)=>$s['pa']??0,$sesiones));
$comp=count(array_filter($sesiones,fn($s)=>$s['completada']??false));

// Actividades disponibles (para ver % completado)
$dispMat=array_values(array_filter($actividades,fn($a)=>($a['visible']??true)&&in_array($a['materia_id'],$matIds)));
require_once ROOT.'/includes/header.php';
?>
<div class="grid g4" style="margin-bottom:20px">
  <div class="stat"><div class="stat-label">Tiempo activo total</div><div class="stat-val"><?=intdiv($totalSA,3600)?> h <?=intdiv($totalSA%3600,60)?> min</div></div>
  <div class="stat"><div class="stat-label">Actividades completadas</div><div class="stat-val" style="color:var(--suc)"><?=$comp?>/<?=count($dispMat)?></div></div>
  <div class="stat"><div class="stat-label">Cambios de pestaña</div><div class="stat-val" style="color:<?=$totalTC?'var(--dan)':'var(--suc)'?>"><?=$totalTC?></div></div>
  <div class="stat"><div class="stat-label">Intentos de pegado</div><div class="stat-val" style="color:<?=$totalPA?'var(--dan)':'var(--suc)'?>"><?=$totalPA?></div></div>
</div>

<div class="grid g2">
<div>
  <div class="card">
    <div class="card-header"><h3 class="card-title">Progreso por materia</h3></div>
    <?php foreach($misMaterias as $m):
      $actsMat=array_filter($actividades,fn($a)=>$a['materia_id']==$m['id']&&($a['visible']??true));
      $total=count($actsMat);
      $compMat=count(array_filter($actsMat,fn($a)=>($sesiones[array_search($a['id'],array_column($sesiones,'actividad.id')??[])]['completada']??false)));
      // Recalculate properly
      $compMat=0;
      foreach($actsMat as $a){
        $t=getTracking($user['id'],$a['id']);
        if($t['completada']??false) $compMat++;
      }
      $pct=$total>0?round($compMat/$total*100):0;
      $saMatSeg=0;
      foreach($actsMat as $a){ $t=getTracking($user['id'],$a['id']); $saMatSeg+=$t['sa']??0; }
    ?>
    <div style="margin-bottom:14px">
      <div style="display:flex;justify-content:space-between;font-size:13px;margin-bottom:4px">
        <span class="campo-<?=$m['campo']?>" style="padding-left:8px;font-weight:500"><?=h($m['nombre'])?></span>
        <span style="color:var(--text3)"><?=$compMat?>/<?=$total?></span>
      </div>
      <div class="prog"><div class="prog-fill <?=$pct>=100?'suc':($pct>50?'':'war')?>" style="width:<?=$pct?>%"></div></div>
      <div style="font-size:11px;color:var(--text3);margin-top:2px"><?=intdiv($saMatSeg,60)?> min activo en esta materia</div>
    </div>
    <?php endforeach; ?>
  </div>
</div>

<div>
  <div class="card">
    <div class="card-header"><h3 class="card-title">Historial de interacciones</h3></div>
    <?php if(empty($sesiones)): ?>
    <p style="color:var(--text3);font-size:13px">Sin sesiones registradas.</p>
    <?php else: ?>
    <?php foreach(array_reverse($sesiones) as $s):
      [$bl,$bc]=tipoBadge($s['actividad']['tipo']??'');
      $sa=$s['sa']??0; $si=$s['si']??0;
      $alertas=($s['tc']??0)+($s['fe']??0)+($s['pa']??0);
    ?>
    <div style="padding:9px 0;border-bottom:1px solid var(--border)">
      <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
        <span class="badge <?=$bc?>" style="font-size:10px"><?=$bl?></span>
        <span style="font-size:13px;font-weight:500;flex:1"><?=h($s['actividad']['titulo'])?></span>
        <?=($s['completada']??false)?'<span class="badge badge-green" style="font-size:10px">✓</span>':'<span class="badge badge-amber" style="font-size:10px">En curso</span>'?>
      </div>
      <div style="display:flex;gap:14px;font-size:12px;color:var(--text3)">
        <span>🟢 <?=fmtMinutos($sa)?> activo</span>
        <span>🟡 <?=fmtMinutos($si)?> idle</span>
        <?php if($alertas): ?><span style="color:var(--dan)">⚠ <?=$alertas?> alert.</span><?php endif; ?>
        <?php if($s['mat']): ?><span style="margin-left:auto"><?=h($s['mat']['nombre'])?></span><?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php endif; ?>
  </div>

  <div class="card">
    <div class="card-header"><h3 class="card-title">Distribución del tiempo</h3></div>
    <?php if($totalSA+$totalSI>0):
      $pctA=round($totalSA/($totalSA+$totalSI)*100);
      $pctI=100-$pctA;
    ?>
    <div style="margin-bottom:10px">
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
        <span>🟢 Tiempo activo real</span><span><?=$pctA?>%</span>
      </div>
      <div class="prog"><div class="prog-fill suc" style="width:<?=$pctA?>%"></div></div>
    </div>
    <div>
      <div style="display:flex;justify-content:space-between;font-size:12px;margin-bottom:3px">
        <span>🟡 Tiempo idle (sin interacción)</span><span><?=$pctI?>%</span>
      </div>
      <div class="prog"><div class="prog-fill war" style="width:<?=$pctI?>%"></div></div>
    </div>
    <div style="margin-top:12px;font-size:12px;color:var(--text3)">
      Total acumulado: <?=intdiv($totalSA+$totalSI,3600)?> h <?=intdiv(($totalSA+$totalSI)%3600,60)?> min
    </div>
    <?php else: ?>
    <p style="color:var(--text3);font-size:13px">Sin datos de tiempo aún.</p>
    <?php endif; ?>
  </div>
</div>
</div>
<?php require_once ROOT.'/includes/footer.php'; ?>
