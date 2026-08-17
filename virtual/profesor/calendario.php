<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='profesor') redirect('/index.php');
$page_title='Calendario de actividades';
$misMaterias=array_values(array_filter(getMaterias(),fn($m)=>in_array($m['id'],$user['materia_ids']??[])));
$matId=(int)($_GET['materia']??($misMaterias[0]['id']??0));

// Guardar evento
if($_SERVER['REQUEST_METHOD']==='POST'){
    $mid=(int)$_POST['materia_id'];
    $cal=getCal($mid);
    if(isset($_POST['del_id'])){
        $cal=array_values(array_filter($cal,fn($e)=>$e['id']!==(int)$_POST['del_id']));
    } else {
        $ev=['id'=>nextId($cal),'titulo'=>trim($_POST['titulo']??''),'fecha'=>$_POST['fecha']??date('Y-m-d'),'tipo'=>$_POST['tipo']??'clase','hora'=>$_POST['hora']??'','desc'=>trim($_POST['desc']??''),'color'=>$_POST['color']??'blue'];
        if($ev['titulo']) $cal[]=$ev;
    }
    saveCal($mid,$cal);
    flash('Calendario actualizado.');
    redirect("/profesor/calendario.php?materia={$mid}");
}

$cal=$matId?getCal($matId):[];
$now=new DateTime();
$mes=(int)($_GET['mes']??$now->format('n'));
$anio=(int)($_GET['anio']??$now->format('Y'));
if($mes<1){$mes=12;$anio--;} if($mes>12){$mes=1;$anio++;}
$prev=($mes==1)?['mes'=>12,'anio'=>$anio-1]:['mes'=>$mes-1,'anio'=>$anio];
$next=($mes==12)?['mes'=>1,'anio'=>$anio+1]:['mes'=>$mes+1,'anio'=>$anio];
$diasMes=(int)date('t', mktime(0,0,0,$mes,1,$anio));
$primerDia=(int)(new DateTime("{$anio}-{$mes}-01"))->format('N');
$colores=['blue'=>'#B5D4F4','green'=>'#C0DD97','amber'=>'#FAC775','coral'=>'#F5C4B3','purple'=>'#CECBF6','red'=>'#F7C1C1'];
$tiposLabel=['clase'=>'Clase','tp'=>'Trabajo Práctico','parcial'=>'Parcial','examen'=>'Examen','entrega'=>'Entrega','feriado'=>'Feriado','otro'=>'Otro'];
require_once ROOT.'/includes/header.php';
?>
<?php if($f=getFlash()): ?><div class="alert alert-<?=$f['type']?>"><?=h($f['msg'])?></div><?php endif; ?>

<div style="display:flex;gap:10px;margin-bottom:16px;flex-wrap:wrap">
  <?php foreach($misMaterias as $m): ?>
  <a href="?materia=<?=$m['id']?>&mes=<?=$mes?>&anio=<?=$anio?>" class="btn <?=$m['id']==$matId?'btn-primary':''"?>"><?=h($m['nombre'])?></a>
  <?php endforeach; ?>
</div>

<?php if($matId): ?>
<div class="grid" style="grid-template-columns:1fr 320px;gap:16px">
<div>
  <div class="card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px">
      <a href="?materia=<?=$matId?>&mes=<?=$prev['mes']?>&anio=<?=$prev['anio']?>" class="btn btn-sm">‹</a>
      <h3 style="font-size:16px;font-weight:600"><?=(['','Enero','Febrero','Marzo','Abril','Mayo','Junio','Julio','Agosto','Septiembre','Octubre','Noviembre','Diciembre'][$mes]).' '.$anio?></h3>
      <a href="?materia=<?=$matId?>&mes=<?=$next['mes']?>&anio=<?=$next['anio']?>" class="btn btn-sm">›</a>
    </div>
    <div class="cal-grid">
      <?php foreach(['Lun','Mar','Mié','Jue','Vie','Sáb','Dom'] as $d): ?>
      <div class="cal-day-name"><?=$d?></div>
      <?php endforeach; ?>
      <?php for($i=1;$i<$primerDia;$i++): ?><div class="cal-day other-month"></div><?php endfor; ?>
      <?php for($d=1;$d<=$diasMes;$d++):
        $fecha=sprintf('%04d-%02d-%02d',$anio,$mes,$d);
        $hoy=($fecha===date('Y-m-d'));
        $eventos=array_filter($cal,fn($e)=>$e['fecha']===$fecha);
      ?>
      <div class="cal-day <?=$hoy?'today':''?>">
        <div class="cal-day-num"><?=$d?></div>
        <?php foreach($eventos as $ev):
          $bg=$colores[$ev['color']??'blue']??'#B5D4F4';
          $tx=str_replace(['#B5D4F4','#C0DD97','#FAC775','#F5C4B3','#CECBF6','#F7C1C1'],['#0C447C','#27500A','#633806','#712B13','#3C3489','#791F1F'],$bg);
        ?>
        <div class="cal-event" style="background:<?=$bg?>;color:<?=$tx?>" title="<?=h($ev['titulo'])?>">
          <?=h(substr($ev['titulo'],0,18))?>
        </div>
        <?php endforeach; ?>
      </div>
      <?php endfor; ?>
    </div>
  </div>
</div>

<div>
  <div class="card">
    <h3 class="card-title" style="margin-bottom:14px">+ Agregar al calendario</h3>
    <form method="POST">
      <input type="hidden" name="materia_id" value="<?=$matId?>">
      <div class="form-group"><label>Título *</label><input type="text" name="titulo" placeholder="Ej: Entrega TP2" required></div>
      <div class="form-group"><label>Fecha *</label><input type="date" name="fecha" value="<?=date('Y-m-d')?>" required></div>
      <div class="form-row c2">
        <div class="form-group"><label>Tipo</label>
          <select name="tipo">
            <?php foreach($tiposLabel as $k=>$v): ?>
            <option value="<?=$k?>"><?=$v?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group"><label>Hora (opcional)</label><input type="time" name="hora"></div>
      </div>
      <div class="form-group"><label>Color</label>
        <select name="color">
          <option value="blue">Azul</option><option value="green">Verde</option>
          <option value="amber">Amarillo</option><option value="coral">Naranja</option>
          <option value="purple">Violeta</option><option value="red">Rojo</option>
        </select>
      </div>
      <div class="form-group"><label>Descripción</label><textarea name="desc" rows="2"></textarea></div>
      <button type="submit" class="btn btn-primary" style="width:100%">Agregar</button>
    </form>
  </div>

  <div class="card">
    <h3 class="card-title" style="margin-bottom:10px">Próximos eventos</h3>
    <?php $proximos=array_filter($cal,fn($e)=>$e['fecha']>=date('Y-m-d'));
    usort($proximos,fn($a,$b)=>strcmp($a['fecha'],$b['fecha']));
    foreach(array_slice($proximos,0,8) as $ev):
      $bg=$colores[$ev['color']??'blue']??'#B5D4F4';
    ?>
    <div style="display:flex;align-items:flex-start;gap:8px;padding:6px 0;border-bottom:1px solid var(--border)">
      <div style="width:8px;height:8px;border-radius:50%;background:<?=$bg?>;flex-shrink:0;margin-top:4px"></div>
      <div style="flex:1">
        <div style="font-size:13px;font-weight:500"><?=h($ev['titulo'])?></div>
        <div style="font-size:11px;color:var(--text3)"><?=date('d/m/Y',strtotime($ev['fecha']))?><?=$ev['hora']?' — '.$ev['hora']:''?> · <?=$tiposLabel[$ev['tipo']]??$ev['tipo']?></div>
      </div>
      <form method="POST" style="flex-shrink:0">
        <input type="hidden" name="materia_id" value="<?=$matId?>">
        <input type="hidden" name="del_id" value="<?=$ev['id']?>">
        <button class="btn btn-xs" type="submit" onclick="return confirm('¿Eliminar?')">✕</button>
      </form>
    </div>
    <?php endforeach; ?>
    <?php if(!$proximos): ?><p style="color:var(--text3);font-size:13px">Sin eventos próximos.</p><?php endif; ?>
  </div>
</div>
</div>
<?php else: ?>
<div class="alert alert-info">Seleccioná una materia para ver su calendario.</div>
<?php endif; ?>
<?php require_once ROOT.'/includes/footer.php'; ?>
