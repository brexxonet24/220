<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='alumno') redirect('/index.php');
$page_title='Calendario';
$materias=getMaterias();
$anio=$user['anio_cursada']??0;
$misMaterias=$anio?array_values(array_filter($materias,fn($m)=>$m['anio']==$anio)):[];

// Recolectar todos los eventos de mis materias
$todosEventos=[];
foreach($misMaterias as $m){
    foreach(getCal($m['id']) as $ev){
        $todosEventos[]=array_merge($ev,['mat_nombre'=>$m['nombre'],'mat_color'=>$m['color']]);
    }
}

$now=new DateTime();
$mes=(int)($_GET['mes']??$now->format('n'));
$anioC=(int)($_GET['anio']??$now->format('Y'));
if($mes<1){$mes=12;$anioC--;} if($mes>12){$mes=1;$anioC++;}
$prev=($mes==1)?['mes'=>12,'anio'=>$anioC-1]:['mes'=>$mes-1,'anio'=>$anioC];
$next=($mes==12)?['mes'=>1,'anio'=>$anioC+1]:['mes'=>$mes+1,'anio'=>$anioC];
$diasMes=(int)date('t', mktime(0,0,0,$mes,1,$anioC));
$primerDia=(int)(new DateTime("{$anioC}-{$mes}-01"))->format('N');
$colores=['blue'=>'#B5D4F4','green'=>'#C0DD97','amber'=>'#FAC775','coral'=>'#F5C4B3','purple'=>'#CECBF6','red'=>'#F7C1C1'];
$txColors=['blue'=>'#0C447C','green'=>'#27500A','amber'=>'#633806','coral'=>'#712B13','purple'=>'#3C3489','red'=>'#791F1F'];
require_once ROOT.'/includes/header.php';
?>
<div class="grid" style="grid-template-columns:1fr 280px;gap:16px">
<div class="card">
  <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
    <a href="?mes=<?=$prev['mes']?>&anio=<?=$prev['anio']?>" class="btn btn-sm">‹</a>
    <h3 style="font-size:16px;font-weight:600"><?=(['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][$mes]).' '.$anioC?></h3>
    <a href="?mes=<?=$next['mes']?>&anio=<?=$next['anio']?>" class="btn btn-sm">›</a>
  </div>
  <div class="cal-grid">
    <?php foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d): ?>
    <div class="cal-day-name"><?=$d?></div>
    <?php endforeach; ?>
    <?php for($i=1;$i<$primerDia;$i++): ?><div class="cal-day other-month"></div><?php endfor; ?>
    <?php for($d=1;$d<=$diasMes;$d++):
      $fecha=sprintf('%04d-%02d-%02d',$anioC,$mes,$d);
      $hoy=($fecha===date('Y-m-d'));
      $eventos=array_filter($todosEventos,fn($e)=>$e['fecha']===$fecha);
    ?>
    <div class="cal-day <?=$hoy?'today':''?>">
      <div class="cal-day-num"><?=$d?></div>
      <?php foreach($eventos as $ev):
        $bg=$colores[$ev['mat_color']??'blue']??'#B5D4F4';
        $tx=$txColors[$ev['mat_color']??'blue']??'#0C447C';
      ?>
      <div class="cal-event" style="background:<?=$bg?>;color:<?=$tx?>" title="<?=h($ev['titulo'])?> · <?=h($ev['mat_nombre'])?>">
        <?=h(substr($ev['titulo'],0,14))?>
      </div>
      <?php endforeach; ?>
    </div>
    <?php endfor; ?>
  </div>
</div>

<div>
  <div class="card">
    <h3 class="card-title" style="margin-bottom:12px">Próximos eventos</h3>
    <?php $proximos=array_filter($todosEventos,fn($e)=>$e['fecha']>=date('Y-m-d'));
    usort($proximos,fn($a,$b)=>strcmp($a['fecha'],$b['fecha']));
    foreach(array_slice($proximos,0,10) as $ev):
      $bg=$colores[$ev['mat_color']??'blue']??'#B5D4F4';
      $tx=$txColors[$ev['mat_color']??'blue']??'#0C447C';
    ?>
    <div style="display:flex;align-items:flex-start;gap:8px;padding:7px 0;border-bottom:1px solid var(--border)">
      <div style="width:8px;height:8px;border-radius:50%;background:<?=$bg?>;flex-shrink:0;margin-top:4px"></div>
      <div>
        <div style="font-size:13px;font-weight:500"><?=h($ev['titulo'])?></div>
        <div style="font-size:11px;color:var(--text3)"><?=date('d/m/Y',strtotime($ev['fecha']))?><?=$ev['hora']?' — '.h($ev['hora']):''?></div>
        <div style="font-size:11px;color:var(--text3)"><?=h($ev['mat_nombre'])?></div>
        <?php if($ev['desc']): ?><div style="font-size:11px;color:var(--text2);margin-top:2px"><?=h($ev['desc'])?></div><?php endif; ?>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if(!$proximos): ?><p style="color:var(--text3);font-size:13px">Sin eventos próximos.</p><?php endif; ?>
  </div>

  <div class="card">
    <h3 class="card-title" style="margin-bottom:8px">Referencias</h3>
    <?php foreach($misMaterias as $m):
      $bg=$colores[$m['color']]??'#B5D4F4';
      $tx=$txColors[$m['color']]??'#0C447C';
    ?>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:6px">
      <div style="width:12px;height:12px;border-radius:50%;background:<?=$bg?>;flex-shrink:0"></div>
      <span style="font-size:12px"><?=h($m['nombre'])?></span>
    </div>
    <?php endforeach; ?>
  </div>
</div>
</div>
<?php require_once ROOT.'/includes/footer.php'; ?>
