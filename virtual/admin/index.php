<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='admin') redirect('/index.php');

$page_title='Dashboard Admin';
$usuarios=getUsuarios();
$materias=getMaterias();
$actividades=getActividades();

$profs=array_filter($usuarios,fn($u)=>$u['rol']==='profesor');
$alumnos=array_filter($usuarios,fn($u)=>$u['rol']==='alumno');
$pending=array_filter($profs,fn($u)=>($u['pendiente_aprobacion']??false));

// Activar/desactivar usuario
if($_SERVER['REQUEST_METHOD']==='POST'){
    $uid=(int)($_POST['uid']??0);
    $accion=$_POST['accion']??'';
    foreach($usuarios as &$u){
        if($u['id']===$uid){
            if($accion==='activar'){ $u['activo']=true; $u['pendiente_aprobacion']=false; }
            if($accion==='desactivar') $u['activo']=false;
            break;
        }
    }
    saveUsuarios($usuarios);
    flash('Usuario actualizado.');
    redirect('/admin/index.php');
}
require_once ROOT.'/includes/header.php';
?>
<?php if($flash=getFlash()): ?>
<div class="alert alert-<?=$flash['type']?>"><?=h($flash['msg'])?></div>
<?php endif; ?>

<div class="grid g4" style="margin-bottom:20px">
  <div class="stat"><div class="stat-label">Profesores</div><div class="stat-val"><?=count($profs)?></div></div>
  <div class="stat"><div class="stat-label">Alumnos</div><div class="stat-val"><?=count($alumnos)?></div></div>
  <div class="stat"><div class="stat-label">Materias</div><div class="stat-val"><?=count($materias)?></div></div>
  <div class="stat"><div class="stat-label">Actividades</div><div class="stat-val"><?=count($actividades)?></div></div>
</div>

<?php if(count($pending)): ?>
<div class="card" style="border-left:3px solid var(--war)">
  <div class="card-header">
    <h3 class="card-title">⏳ Docentes pendientes de aprobación</h3>
  </div>
  <div class="table-wrap">
  <table class="tbl">
    <thead><tr><th>Nombre</th><th>Usuario</th><th>Email</th><th>Fecha</th><th>Acción</th></tr></thead>
    <tbody>
    <?php foreach($pending as $p): ?>
    <tr>
      <td><?=h($p['apellido'].', '.$p['nombre'])?></td>
      <td><code><?=h($p['usuario'])?></code></td>
      <td><?=h($p['email'])?></td>
      <td><?=h($p['created_at']??'')?></td>
      <td>
        <form method="POST" style="display:inline">
          <input type="hidden" name="uid" value="<?=$p['id']?>">
          <input type="hidden" name="accion" value="activar">
          <button class="btn btn-success btn-sm" type="submit">✓ Activar</button>
        </form>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endif; ?>

<div class="grid g2">
  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Plan de estudios</h3>
      <a href="/admin/materias.php" class="btn btn-sm">Ver todo</a>
    </div>
    <?php foreach([1,2,3] as $anio): ?>
    <div style="font-size:12px;font-weight:600;color:var(--text3);margin:10px 0 6px;text-transform:uppercase"><?=$anio?>° Año</div>
    <?php foreach(array_filter($materias,fn($m)=>$m['anio']==$anio) as $m): ?>
    <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px">
      <span class="badge badge-<?=$m['color']?>"><?=strtoupper(substr($m['campo'],0,3))?></span>
      <span style="font-size:13px;flex:1"><?=h($m['nombre'])?></span>
      <span style="font-size:11px;color:var(--text3)"><?=$m['horas']?>h</span>
      <span style="font-size:11px;color:<?=count($m['profesor_ids'])>0?'var(--suc)':'var(--war)'?>">
        <?=count($m['profesor_ids'])?>👤
      </span>
    </div>
    <?php endforeach; ?>
    <?php endforeach; ?>
  </div>

  <div class="card">
    <div class="card-header">
      <h3 class="card-title">Docentes activos</h3>
      <a href="/admin/profesores.php" class="btn btn-sm">Gestionar</a>
    </div>
    <?php foreach(array_filter($profs,fn($u)=>($u['activo']??false)) as $p): ?>
    <div style="display:flex;align-items:center;gap:10px;padding:7px 0;border-bottom:1px solid var(--border)">
      <div style="width:32px;height:32px;border-radius:50%;background:var(--pri-l);display:flex;align-items:center;justify-content:center;font-size:12px;font-weight:600;color:var(--pri-d);flex-shrink:0">
        <?=strtoupper(substr($p['nombre'],0,1).substr($p['apellido'],0,1))?>
      </div>
      <div style="flex:1">
        <div style="font-size:13px;font-weight:500"><?=h($p['apellido'].', '.$p['nombre'])?></div>
        <div style="font-size:11px;color:var(--text3)"><?=count($p['materia_ids']??[])?> materia(s) asignada(s)</div>
      </div>
    </div>
    <?php endforeach; ?>
    <?php if(!array_filter($profs,fn($u)=>($u['activo']??false))): ?>
    <p style="color:var(--text3);font-size:13px">Sin docentes activos aún.</p>
    <?php endif; ?>
  </div>
</div>
<?php require_once ROOT.'/includes/footer.php'; ?>
