<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='profesor') redirect('/index.php');
$page_title='Calificaciones';
$materias=getMaterias();
$misMaterias=array_values(array_filter($materias,fn($m)=>in_array($m['id'],$user['materia_ids']??[])));
$usuarios=getUsuarios();
$alumnos=array_values(array_filter($usuarios,fn($u)=>$u['rol']==='alumno'&&($u['activo']??true)));

$matId=(int)($_GET['materia']??($misMaterias[0]['id']??0));
$matSel=null;
foreach($misMaterias as $m){ if($m['id']===$matId){ $matSel=$m; break; } }
$calif=$matId?getCalif($matId):[];

if($_SERVER['REQUEST_METHOD']==='POST'){
    $mid=(int)$_POST['materia_id'];
    $uid=(int)$_POST['alumno_id'];
    $peri=$_POST['periodo']??'1C';
    $nota=floatval(str_replace(',','.',$_POST['nota']??'0'));
    $obs=trim($_POST['obs']??'');

    $calActual=getCalif($mid);
    $key="{$uid}_{$peri}";
    $calActual[$key]=['alumno_id'=>$uid,'materia_id'=>$mid,'periodo'=>$peri,'nota'=>round($nota,1),'obs'=>$obs,'fecha'=>date('Y-m-d H:i:s'),'prof_id'=>$user['id']];
    saveCalif($mid,$calActual);
    flash('Calificación guardada.');
    redirect("/profesor/calificaciones.php?materia={$mid}");
}
require_once ROOT.'/includes/header.php';
?>
<?php if($f=getFlash()): ?><div class="alert alert-<?=$f['type']?>"><?=h($f['msg'])?></div><?php endif; ?>

<div style="display:flex;gap:12px;margin-bottom:16px;flex-wrap:wrap">
  <?php foreach($misMaterias as $m): ?>
  <a href="?materia=<?=$m['id']?>" class="btn <?=$m['id']==$matId?'btn-primary':''"?>"><?=h($m['nombre'])?></a>
  <?php endforeach; ?>
</div>

<?php if(!$matSel): ?>
<div class="alert alert-info">Seleccioná una materia para cargar calificaciones.</div>
<?php else: ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">📊 <?=h($matSel['nombre'])?> — Calificaciones</h3>
    <button class="btn btn-sm btn-primary" onclick="toggleForm()">+ Cargar nota</button>
  </div>

  <div id="form-nota" style="display:none;background:var(--surface2);border-radius:var(--r);padding:16px;margin-bottom:16px">
    <form method="POST">
      <input type="hidden" name="materia_id" value="<?=$matId?>">
      <div class="form-row c3">
        <div class="form-group">
          <label>Alumno</label>
          <select name="alumno_id" required>
            <option value="">— Seleccionar —</option>
            <?php foreach($alumnos as $a): ?>
            <option value="<?=$a['id']?>"><?=h($a['apellido'].', '.$a['nombre'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Período</label>
          <select name="periodo">
            <option value="1C">1° Cuatrimestre</option>
            <option value="2C">2° Cuatrimestre</option>
            <option value="TP1">TP 1</option>
            <option value="TP2">TP 2</option>
            <option value="TP3">TP 3</option>
            <option value="FINAL">Examen Final</option>
            <option value="RECUP">Recuperatorio</option>
          </select>
        </div>
        <div class="form-group">
          <label>Nota (1–10)</label>
          <input type="number" name="nota" min="1" max="10" step="0.5" required placeholder="7">
        </div>
      </div>
      <div class="form-group">
        <label>Observaciones (opcional)</label>
        <input type="text" name="obs" placeholder="Buen desempeño, entregó tarde, etc.">
      </div>
      <button type="submit" class="btn btn-primary">Guardar</button>
      <button type="button" class="btn" onclick="toggleForm()">Cancelar</button>
    </form>
  </div>

  <?php
  $periodos=['1C','2C','TP1','TP2','TP3','FINAL','RECUP'];
  $periodoLabels=['1C'=>'1°C','2C'=>'2°C','TP1'=>'TP1','TP2'=>'TP2','TP3'=>'TP3','FINAL'=>'Final','RECUP'=>'Recup.'];
  ?>
  <div class="table-wrap">
  <table class="tbl">
    <thead>
      <tr>
        <th>Alumno</th>
        <?php foreach($periodos as $p): ?><th><?=$periodoLabels[$p]?></th><?php endforeach; ?>
        <th>Prom.</th>
      </tr>
    </thead>
    <tbody>
    <?php foreach($alumnos as $a):
      $notas=[];
      foreach($periodos as $p){
        $key="{$a['id']}_{$p}";
        $notas[$p]=$calif[$key]??null;
      }
      $vals=array_filter(array_map(fn($n)=>$n?$n['nota']:null,$notas),fn($v)=>$v!==null);
      $prom=count($vals)?round(array_sum($vals)/count($vals),1):null;
      $color=$prom===null?'':($prom>=6?'color:var(--suc)':($prom>=4?'color:var(--war)':'color:var(--dan)'));
    ?>
    <tr>
      <td><strong><?=h($a['apellido'].', '.$a['nombre'])?></strong></td>
      <?php foreach($periodos as $p):
        $n=$notas[$p];
        $nc=$n?($n['nota']>=6?'var(--suc)':($n['nota']>=4?'var(--war)':'var(--dan)')):'var(--text3)';
      ?>
      <td>
        <?php if($n): ?>
        <span style="font-weight:600;color:<?=$nc?>"><?=$n['nota']?></span>
        <?php if($n['obs']): ?><span title="<?=h($n['obs'])?>" style="cursor:help;font-size:10px">ⓘ</span><?php endif; ?>
        <?php else: ?><span style="color:var(--border2)">—</span><?php endif; ?>
      </td>
      <?php endforeach; ?>
      <td><strong style="<?=$color?>"><?=$prom!==null?$prom:'—'?></strong></td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endif; ?>
<script>
function toggleForm(){ const f=document.getElementById('form-nota'); f.style.display=f.style.display==='none'?'block':'none'; }
</script>
<?php require_once ROOT.'/includes/footer.php'; ?>
