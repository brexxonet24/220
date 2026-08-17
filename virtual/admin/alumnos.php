<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='admin') redirect('/index.php');
$page_title='Gestión de Alumnos';
$usuarios=getUsuarios();
$materias=getMaterias();

if($_SERVER['REQUEST_METHOD']==='POST'){
    $accion=$_POST['accion']??'';
    $uid=(int)($_POST['uid']??0);
    if($accion==='asignar_anio'){
        $anio=(int)$_POST['anio'];
        // Asignar materias del año seleccionado
        $mids=array_column(array_filter($materias,fn($m)=>$m['anio']===$anio),'id');
        foreach($usuarios as &$u){
            if($u['id']===$uid){ $u['materia_ids']=$mids; $u['anio_cursada']=$anio; break; }
        }
        saveUsuarios($usuarios);
        flash("Alumno asignado al {$anio}° año.");
        redirect('/admin/alumnos.php');
    }
    if($accion==='eliminar'){
        $usuarios=array_values(array_filter($usuarios,fn($u)=>!($u['id']===$uid&&$u['rol']==='alumno')));
        saveUsuarios($usuarios);
        flash('Alumno eliminado.','warning');
        redirect('/admin/alumnos.php');
    }
    if($accion==='toggle'){
        foreach($usuarios as &$u){
            if($u['id']===$uid) { $u['activo']=!($u['activo']??true); break; }
        }
        saveUsuarios($usuarios);
        flash('Estado actualizado.');
        redirect('/admin/alumnos.php');
    }
}

$alumnos=array_values(array_filter($usuarios,fn($u)=>$u['rol']==='alumno'));
require_once ROOT.'/includes/header.php';
?>
<?php if($f=getFlash()): ?><div class="alert alert-<?=$f['type']?>"><?=h($f['msg'])?></div><?php endif; ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Alumnos registrados (<?=count($alumnos)?>)</h3>
  </div>
  <?php if(empty($alumnos)): ?>
  <p style="color:var(--text3)">Ningún alumno registrado. Se auto-registran en <a href="/registro.php">/registro.php</a>.</p>
  <?php else: ?>
  <div class="table-wrap">
  <table class="tbl">
    <thead><tr><th>Apellido y Nombre</th><th>Usuario</th><th>DNI</th><th>Año</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($alumnos as $a): ?>
    <tr>
      <td><strong><?=h($a['apellido'].', '.$a['nombre'])?></strong></td>
      <td><code><?=h($a['usuario'])?></code></td>
      <td><?=h($a['dni']??'-')?></td>
      <td>
        <form method="POST" style="display:flex;align-items:center;gap:6px">
          <input type="hidden" name="uid" value="<?=$a['id']?>">
          <input type="hidden" name="accion" value="asignar_anio">
          <select name="anio" style="padding:4px 8px;font-size:12px;border:1px solid var(--border2);border-radius:var(--r)">
            <option value="">Sin año</option>
            <?php foreach([1,2,3] as $y): ?>
            <option value="<?=$y?>" <?=($a['anio_cursada']??'')==$y?'selected':''?>><?=$y?>° año</option>
            <?php endforeach; ?>
          </select>
          <button class="btn btn-xs btn-primary" type="submit">OK</button>
        </form>
      </td>
      <td><?=($a['activo']??true)?'<span class="badge badge-green">Activo</span>':'<span class="badge badge-red">Inactivo</span>'?></td>
      <td style="white-space:nowrap">
        <form method="POST" style="display:inline">
          <input type="hidden" name="uid" value="<?=$a['id']?>">
          <input type="hidden" name="accion" value="toggle">
          <button class="btn btn-sm" type="submit"><?=($a['activo']??true)?'Desactivar':'Activar'?></button>
        </form>
        <form method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar alumno?')">
          <input type="hidden" name="uid" value="<?=$a['id']?>">
          <input type="hidden" name="accion" value="eliminar">
          <button class="btn btn-danger btn-sm" type="submit">Eliminar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
  <?php endif; ?>
</div>
<?php require_once ROOT.'/includes/footer.php'; ?>
