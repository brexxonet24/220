<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='admin') redirect('/index.php');

$page_title='Gestión de Docentes';
$usuarios=getUsuarios();
$materias=getMaterias();
$error=''; 

if($_SERVER['REQUEST_METHOD']==='POST'){
    $accion=$_POST['accion']??'';
    
    if($accion==='asignar'){
        $pid=(int)$_POST['prof_id'];
        $mids=array_map('intval',$_POST['materia_ids']??[]);
        foreach($usuarios as &$u){
            if($u['id']===$pid){ $u['materia_ids']=$mids; break; }
        }
        // Sync materias
        foreach($materias as &$m){
            if(in_array($m['id'],$mids)){
                if(!in_array($pid,$m['profesor_ids'])) $m['profesor_ids'][]=$pid;
            } else {
                $m['profesor_ids']=array_values(array_filter($m['profesor_ids'],fn($x)=>$x!==$pid));
            }
        }
        saveUsuarios($usuarios);
        saveMaterias($materias);
        flash('Materias asignadas correctamente.');
        redirect('/admin/profesores.php');
    }
    if($accion==='activar'||$accion==='desactivar'){
        $uid=(int)$_POST['uid'];
        foreach($usuarios as &$u){
            if($u['id']===$uid){
                $u['activo']=($accion==='activar');
                $u['pendiente_aprobacion']=false;
                break;
            }
        }
        saveUsuarios($usuarios);
        flash($accion==='activar'?'Docente activado.':'Docente desactivado.');
        redirect('/admin/profesores.php');
    }
    if($accion==='eliminar'){
        $uid=(int)$_POST['uid'];
        $usuarios=array_values(array_filter($usuarios,fn($u)=>$u['id']!==$uid||$u['rol']!=='profesor'));
        saveUsuarios($usuarios);
        flash('Docente eliminado.','warning');
        redirect('/admin/profesores.php');
    }
}

$profs=array_values(array_filter($usuarios,fn($u)=>$u['rol']==='profesor'));
$selProf=null;
if(isset($_GET['asignar'])){
    foreach($profs as $p) if($p['id']==(int)$_GET['asignar']){$selProf=$p;break;}
}
require_once ROOT.'/includes/header.php';
?>
<?php if($f=getFlash()): ?><div class="alert alert-<?=$f['type']?>"><?=h($f['msg'])?></div><?php endif; ?>

<?php if($selProf): ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Asignar materias a <?=h($selProf['apellido'].', '.$selProf['nombre'])?></h3>
    <a href="/admin/profesores.php" class="btn btn-sm">Cancelar</a>
  </div>
  <form method="POST">
    <input type="hidden" name="accion" value="asignar">
    <input type="hidden" name="prof_id" value="<?=$selProf['id']?>">
    <?php foreach([1,2,3] as $anio): ?>
    <div style="font-size:12px;font-weight:600;text-transform:uppercase;color:var(--text3);margin:14px 0 6px"><?=$anio?>° Año</div>
    <div class="grid g3">
    <?php foreach(array_filter($materias,fn($m)=>$m['anio']==$anio) as $m): ?>
    <label style="display:flex;align-items:flex-start;gap:8px;cursor:pointer;font-size:13px;padding:8px 10px;border:1px solid var(--border);border-radius:var(--r);background:var(--surface2)">
      <input type="checkbox" name="materia_ids[]" value="<?=$m['id']?>" <?=in_array($m['id'],$selProf['materia_ids']??[])?'checked':''?>>
      <div>
        <div style="font-weight:500"><?=h($m['nombre'])?></div>
        <div style="font-size:11px;color:var(--text3)"><?=$m['horas']?> hs · <?=h($m['campo'])?></div>
      </div>
    </label>
    <?php endforeach; ?>
    </div>
    <?php endforeach; ?>
    <div style="margin-top:20px">
      <button type="submit" class="btn btn-primary">Guardar asignación</button>
    </div>
  </form>
</div>
<?php else: ?>
<div class="card">
  <div class="card-header">
    <h3 class="card-title">Docentes registrados</h3>
  </div>
  <?php if(empty($profs)): ?>
  <p style="color:var(--text3)">Ningún docente registrado aún. Los docentes se auto-registran en <a href="/registro.php">/registro.php</a>.</p>
  <?php else: ?>
  <div class="table-wrap">
  <table class="tbl">
    <thead><tr><th>Apellido y Nombre</th><th>Usuario</th><th>Email</th><th>Materias</th><th>Estado</th><th>Acciones</th></tr></thead>
    <tbody>
    <?php foreach($profs as $p): ?>
    <?php $activo=$p['activo']??false; $pend=$p['pendiente_aprobacion']??false; ?>
    <tr>
      <td><strong><?=h($p['apellido'].', '.$p['nombre'])?></strong></td>
      <td><code><?=h($p['usuario'])?></code></td>
      <td><?=h($p['email'])?></td>
      <td>
        <?php foreach($p['materia_ids']??[] as $mid): ?>
          <?php $mat=findMateria($mid); if($mat): ?>
          <span class="badge badge-<?=$mat['color']?>" style="margin:1px"><?=h(substr($mat['nombre'],0,22))?></span>
          <?php endif; ?>
        <?php endforeach; ?>
        <?php if(empty($p['materia_ids']??[])): ?><span style="color:var(--text3);font-size:12px">Sin asignar</span><?php endif; ?>
      </td>
      <td>
        <?php if($pend): ?><span class="badge badge-amber">Pendiente</span>
        <?php elseif($activo): ?><span class="badge badge-green">Activo</span>
        <?php else: ?><span class="badge badge-red">Inactivo</span><?php endif; ?>
      </td>
      <td style="white-space:nowrap">
        <a href="?asignar=<?=$p['id']?>" class="btn btn-sm">Asignar materias</a>
        <form method="POST" style="display:inline">
          <input type="hidden" name="uid" value="<?=$p['id']?>">
          <?php if(!$activo||$pend): ?>
          <input type="hidden" name="accion" value="activar">
          <button class="btn btn-success btn-sm" type="submit">Activar</button>
          <?php else: ?>
          <input type="hidden" name="accion" value="desactivar">
          <button class="btn btn-sm" type="submit">Desactivar</button>
          <?php endif; ?>
        </form>
        <form method="POST" style="display:inline" onsubmit="return confirm('¿Eliminar este docente?')">
          <input type="hidden" name="uid" value="<?=$p['id']?>">
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
<?php endif; ?>
<?php require_once ROOT.'/includes/footer.php'; ?>
