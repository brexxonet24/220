<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='profesor') redirect('/index.php');
$page_title='Actividades';
$materias=getMaterias();
$misMaterias=array_values(array_filter($materias,fn($m)=>in_array($m['id'],$user['materia_ids']??[])));
$actividades=getActividades();
$misActividades=array_values(array_filter($actividades,fn($a)=>$a['profesor_id']==$user['id']));

// Filtro por materia
$filtroMat=(int)($_GET['materia']??0);
if($filtroMat) $misActividades=array_values(array_filter($misActividades,fn($a)=>$a['materia_id']==$filtroMat));

// Eliminar
if(isset($_GET['del'])){
    $del=(int)$_GET['del'];
    $todas=getActividades();
    $todas=array_values(array_filter($todas,fn($a)=>!($a['id']===$del&&$a['profesor_id']===$user['id'])));
    saveActividades($todas);
    flash('Actividad eliminada.','warning');
    redirect('/profesor/actividades.php');
}

$modo=isset($_GET['nueva'])||isset($_GET['editar'])?'form':'list';
$editId=(int)($_GET['editar']??0);
$editData=['titulo'=>'','tipo'=>'lectura','materia_id'=>0,'descripcion'=>'','hora_inicio'=>'08:00','hora_fin'=>'22:00','minimo_minutos'=>45,'fecha_desde'=>date('Y-m-d'),'fecha_hasta'=>date('Y-m-d',strtotime('+30 days')),'codigo_template'=>'','codigo_lenguaje'=>'htmlmixed','visible'=>true];
if($editId){
    foreach(getActividades() as $a){ if($a['id']===$editId&&$a['profesor_id']===$user['id']){ $editData=array_merge($editData,$a); break; } }
}

// Guardar
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['guardar'])){
    $todas=getActividades();
    $nuevo=[
        'id'=>$editId?:nextId($todas),
        'profesor_id'=>$user['id'],
        'materia_id'=>(int)$_POST['materia_id'],
        'titulo'=>trim($_POST['titulo']??''),
        'tipo'=>$_POST['tipo']??'lectura',
        'descripcion'=>trim($_POST['descripcion']??''),
        'hora_inicio'=>$_POST['hora_inicio']??'08:00',
        'hora_fin'=>$_POST['hora_fin']??'22:00',
        'minimo_minutos'=>(int)($_POST['minimo_minutos']??45),
        'fecha_desde'=>$_POST['fecha_desde']??date('Y-m-d'),
        'fecha_hasta'=>$_POST['fecha_hasta']??date('Y-m-d',strtotime('+30 days')),
        'codigo_template'=>$_POST['codigo_template']??'',
        'codigo_lenguaje'=>$_POST['codigo_lenguaje']??'htmlmixed',
        'visible'=>isset($_POST['visible']),
        'created_at'=>date('Y-m-d H:i:s'),
    ];
    if($nuevo['titulo']&&$nuevo['materia_id']){
        if($editId) foreach($todas as &$a){ if($a['id']===$editId){ $a=$nuevo; break; } }
        else $todas[]=$nuevo;
        saveActividades($todas);
        flash($editId?'Actividad actualizada.':'Actividad creada.');
        redirect('/profesor/actividades.php');
    }
}
require_once ROOT.'/includes/header.php';
?>
<?php if($f=getFlash()): ?><div class="alert alert-<?=$f['type']?>"><?=h($f['msg'])?></div><?php endif; ?>

<?php if($modo==='form'): ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title"><?=$editId?'Editar actividad':'Nueva actividad'?></h3>
    <a href="/profesor/actividades.php" class="btn btn-sm">Cancelar</a>
  </div>
  <form method="POST">
    <div class="form-row c2">
      <div class="form-group">
        <label>Materia *</label>
        <select name="materia_id" required>
          <option value="">— Seleccionar —</option>
          <?php foreach($misMaterias as $m): ?>
          <option value="<?=$m['id']?>" <?=$editData['materia_id']==$m['id']?'selected':''?>><?=$m['anio']?>° — <?=h($m['nombre'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label>Tipo de actividad *</label>
        <select name="tipo" id="tipo-select" onchange="toggleCodigo(this.value)">
          <option value="lectura" <?=$editData['tipo']==='lectura'?'selected':''?>>📄 Lectura de material (PDF/texto)</option>
          <option value="cuestionario" <?=$editData['tipo']==='cuestionario'?'selected':''?>>❓ Cuestionario / preguntas</option>
          <option value="video" <?=$editData['tipo']==='video'?'selected':''?>>🎥 Video con seguimiento</option>
          <option value="consigna" <?=$editData['tipo']==='consigna'?'selected':''?>>📝 Actividad libre (consigna + entrega)</option>
          <option value="codigo" <?=$editData['tipo']==='codigo'?'selected':''?>>💻 Editor de código (HTML/CSS/JS)</option>
        </select>
      </div>
    </div>
    <div class="form-group">
      <label>Título *</label>
      <input type="text" name="titulo" value="<?=h($editData['titulo'])?>" placeholder="Ej: Práctica 3 — Formularios HTML" required>
    </div>
    <div class="form-group">
      <label>Descripción / Consigna</label>
      <textarea name="descripcion" rows="4" placeholder="Instrucciones detalladas para el alumno..."><?=h($editData['descripcion'])?></textarea>
    </div>

    <div id="sec-codigo" style="display:<?=$editData['tipo']==='codigo'?'block':'none'?>">
      <div class="form-row c2">
        <div class="form-group">
          <label>Lenguaje del editor</label>
          <select name="codigo_lenguaje">
            <option value="htmlmixed" <?=$editData['codigo_lenguaje']==='htmlmixed'?'selected':''?>>HTML + CSS + JS</option>
            <option value="javascript" <?=$editData['codigo_lenguaje']==='javascript'?'selected':''?>>JavaScript</option>
            <option value="css" <?=$editData['codigo_lenguaje']==='css'?'selected':''?>>CSS</option>
            <option value="php" <?=$editData['codigo_lenguaje']==='php'?'selected':''?>>PHP</option>
          </select>
        </div>
        <div class="form-group">
          <label>Código template inicial (opcional)</label>
          <textarea name="codigo_template" rows="3" placeholder="Código que verá el alumno al inicio, ej: &lt;!DOCTYPE html&gt;..."><?=h($editData['codigo_template']??'')?></textarea>
        </div>
      </div>
      <div class="alert alert-info">Los alumnos <strong>no pueden pegar código</strong> externo. Todo debe ser escrito manualmente. Los intentos de pegado quedan registrados.</div>
    </div>

    <div class="form-row c2">
      <div class="form-group"><label>Fecha desde</label><input type="date" name="fecha_desde" value="<?=h($editData['fecha_desde'])?>"></div>
      <div class="form-group"><label>Fecha hasta</label><input type="date" name="fecha_hasta" value="<?=h($editData['fecha_hasta'])?>"></div>
    </div>
    <div class="form-row c3">
      <div class="form-group"><label>Hora inicio</label><input type="time" name="hora_inicio" value="<?=h($editData['hora_inicio'])?>"></div>
      <div class="form-group"><label>Hora fin</label><input type="time" name="hora_fin" value="<?=h($editData['hora_fin'])?>"></div>
      <div class="form-group"><label>Tiempo mínimo (minutos)</label><input type="number" name="minimo_minutos" value="<?=(int)$editData['minimo_minutos']?>" min="5" max="240"></div>
    </div>
    <div class="form-group">
      <label style="display:flex;align-items:center;gap:8px;cursor:pointer">
        <input type="checkbox" name="visible" <?=($editData['visible']??true)?'checked':''?>>
        Actividad visible para los alumnos
      </label>
    </div>
    <button type="submit" name="guardar" class="btn btn-primary">💾 Guardar actividad</button>
  </form>
</div>
<script>
function toggleCodigo(val){ document.getElementById('sec-codigo').style.display=val==='codigo'?'block':'none'; }
</script>

<?php else: ?>
<div class="card">
  <div class="card-header">
    <div style="display:flex;align-items:center;gap:12px;flex:1">
      <h3 class="card-title">Actividades</h3>
      <select onchange="location='?materia='+this.value" style="padding:5px 9px;border:1px solid var(--border2);border-radius:var(--r);font-size:13px">
        <option value="0">Todas las materias</option>
        <?php foreach($misMaterias as $m): ?>
        <option value="<?=$m['id']?>" <?=$filtroMat==$m['id']?'selected':''?>><?=$m['anio']?>° — <?=h($m['nombre'])?></option>
        <?php endforeach; ?>
      </select>
    </div>
    <a href="?nueva=1" class="btn btn-primary btn-sm">+ Nueva actividad</a>
  </div>

  <?php if(empty($misActividades)): ?>
  <div style="text-align:center;padding:40px 20px;color:var(--text3)">
    <div style="font-size:32px;margin-bottom:10px">📋</div>
    <p>Sin actividades. <a href="?nueva=1">Creá la primera</a>.</p>
  </div>
  <?php else: ?>
  <div class="table-wrap">
  <table class="tbl">
    <thead><tr><th>Tipo</th><th>Título</th><th>Materia</th><th>Horario</th><th>Mín.</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach(array_reverse($misActividades) as $a): ?>
    <?php [$bl,$bc]=tipoBadge($a['tipo']); $mat=findMateria($a['materia_id']??0); ?>
    <tr>
      <td><span class="badge <?=$bc?>"><?=$bl?></span></td>
      <td><strong><?=h($a['titulo'])?></strong></td>
      <td style="font-size:12px"><?=$mat?h($mat['nombre']):'-'?></td>
      <td style="font-size:12px;white-space:nowrap"><?=h($a['hora_inicio']??'')?>–<?=h($a['hora_fin']??'')?></td>
      <td><?=$a['minimo_minutos']?> min</td>
      <td><?=($a['visible']??true)?'<span class="badge badge-green">Visible</span>':'<span class="badge badge-gray">Oculta</span>'?></td>
      <td style="white-space:nowrap">
        <a href="/profesor/reporte_actividad.php?id=<?=$a['id']?>" class="btn btn-xs">📈 Reporte</a>
        <a href="?editar=<?=$a['id']?>" class="btn btn-xs">Editar</a>
        <a href="?del=<?=$a['id']?>" class="btn btn-xs btn-danger" onclick="return confirm('¿Eliminar?')">Eliminar</a>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>
<?php endif; ?>
<?php require_once ROOT.'/includes/footer.php'; ?>
