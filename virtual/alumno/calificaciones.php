<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='alumno') redirect('/index.php');
$page_title='Mis Calificaciones';
$materias=getMaterias();
$anio=$user['anio_cursada']??0;
$misMaterias=$anio?array_values(array_filter($materias,fn($m)=>$m['anio']==$anio)):[];
$periodos=['1C'=>'1°C','2C'=>'2°C','TP1'=>'TP1','TP2'=>'TP2','TP3'=>'TP3','FINAL'=>'Final','RECUP'=>'Recup.'];
require_once ROOT.'/includes/header.php';
?>
<?php if(empty($misMaterias)): ?>
<div class="alert alert-info">No tenés materias asignadas aún.</div>
<?php else: ?>
<?php
$todasNotas=[];
foreach($misMaterias as $m){
    $c=getCalif($m['id']);
    $misNotas=[];
    foreach($c as $k=>$v){ if($v['alumno_id']==$user['id']) $misNotas[$v['periodo']]=$v; }
    $vals=array_column($misNotas,'nota');
    $prom=count($vals)?round(array_sum($vals)/count($vals),1):null;
    if($prom!==null) $todasNotas[]=$prom;
    ?>
<div class="card" style="margin-bottom:10px">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:12px">
    <div>
      <span class="badge badge-<?=$m['color']?>" style="margin-bottom:4px;display:inline-block"><?=strtoupper($m['campo'])?></span>
      <h3 style="font-size:15px;font-weight:600"><?=h($m['nombre'])?></h3>
    </div>
    <?php if($prom!==null): ?>
    <div style="text-align:center">
      <div style="font-size:28px;font-weight:700;color:<?=$prom>=6?'var(--suc)':($prom>=4?'var(--war)':'var(--dan)')?>"><?=$prom?></div>
      <div style="font-size:10px;color:var(--text3)">PROMEDIO</div>
    </div>
    <?php endif; ?>
  </div>

  <div class="grid" style="grid-template-columns:repeat(auto-fill,minmax(100px,1fr));gap:8px">
  <?php foreach($periodos as $pk=>$pl):
    $n=$misNotas[$pk]??null;
    $col=$n?($n['nota']>=6?'var(--suc)':($n['nota']>=4?'var(--war)':'var(--dan)')):'var(--text3)';
  ?>
  <div class="stat" style="text-align:center;padding:10px">
    <div class="stat-label"><?=$pl?></div>
    <div style="font-size:22px;font-weight:700;color:<?=$col?>"><?=$n?$n['nota']:'—'?></div>
    <?php if($n&&$n['obs']): ?>
    <div style="font-size:10px;color:var(--text3);margin-top:2px"><?=h(substr($n['obs'],0,30))?></div>
    <?php endif; ?>
  </div>
  <?php endforeach; ?>
  </div>

  <?php if(empty($misNotas)): ?>
  <p style="color:var(--text3);font-size:12px;text-align:center;margin-top:6px">Sin calificaciones registradas en esta materia.</p>
  <?php endif; ?>
</div>
<?php } ?>

<?php if(count($todasNotas)):
  $promTotal=round(array_sum($todasNotas)/count($todasNotas),1);
  $col=$promTotal>=6?'var(--suc)':($promTotal>=4?'var(--war)':'var(--dan)');
?>
<div class="card" style="text-align:center;border-top:3px solid <?=$col?>">
  <div style="font-size:13px;color:var(--text3);margin-bottom:4px">Promedio general · <?=$anio?>° año</div>
  <div style="font-size:42px;font-weight:700;color:<?=$col?>"><?=$promTotal?></div>
  <div style="font-size:12px;color:var(--text3);margin-top:4px"><?=count($todasNotas)?> materia(s) con calificaciones</div>
</div>
<?php endif; ?>
<?php endif; ?>
<?php require_once ROOT.'/includes/footer.php'; ?>
