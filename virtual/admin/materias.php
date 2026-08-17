<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='admin') redirect('/index.php');
$page_title='Plan de Estudios';
$materias=getMaterias();
$usuarios=getUsuarios();
$profs=array_filter($usuarios,fn($u)=>$u['rol']==='profesor'&&($u['activo']??false));

$campos=[
  'general'=>['label'=>'Campo General','color'=>'badge-blue'],
  'tecnico'=>['label'=>'Campo Técnico Específico','color'=>'badge-green'],
  'fundamento'=>['label'=>'Campo del Fundamento','color'=>'badge-purple'],
  'practica'=>['label'=>'Campo de la Práctica','color'=>'badge-coral'],
];
$totalHoras=array_sum(array_column($materias,'horas'));
require_once ROOT.'/includes/header.php';
?>
<div style="margin-bottom:16px;display:flex;gap:12px;flex-wrap:wrap">
  <?php foreach($campos as $k=>$c): ?>
  <span class="badge <?=$c['color']?>" style="font-size:12px;padding:4px 10px"><?=$c['label']?></span>
  <?php endforeach; ?>
  <span style="margin-left:auto;font-size:13px;color:var(--text3);align-self:center">Total plan: <strong><?=$totalHoras?> hs</strong></span>
</div>

<?php foreach([1,2,3] as $anio): ?>
<?php $matAnio=array_filter($materias,fn($m)=>$m['anio']==$anio); ?>
<div class="card" style="margin-bottom:16px">
  <div class="card-header">
    <h3 class="card-title"><?=$anio?>° Año</h3>
    <span style="font-size:13px;color:var(--text3)"><?=array_sum(array_column(array_values($matAnio),'horas'))?> hs</span>
  </div>
  <div class="table-wrap">
  <table class="tbl">
    <thead><tr><th>Materia</th><th>Campo</th><th>Hs</th><th>Docentes asignados</th></tr></thead>
    <tbody>
    <?php foreach($matAnio as $m): ?>
    <tr>
      <td class="campo-<?=$m['campo']?>" style="padding-left:14px;font-weight:500"><?=h($m['nombre'])?></td>
      <td><span class="badge <?=$campos[$m['campo']]['color']??'badge-gray'?>"><?=$campos[$m['campo']]['label']??$m['campo']?></span></td>
      <td><?=$m['horas']?></td>
      <td>
        <?php $asignados=array_filter($profs,fn($p)=>in_array($m['id'],$p['materia_ids']??[])); ?>
        <?php if($asignados): ?>
          <?php foreach($asignados as $p): ?>
          <span class="badge badge-blue" style="margin:1px"><?=h($p['apellido'].', '.$p['nombre'])?></span>
          <?php endforeach; ?>
        <?php else: ?>
          <span style="color:var(--war);font-size:12px">Sin docente asignado</span>
        <?php endif; ?>
      </td>
    </tr>
    <?php endforeach; ?>
    </tbody>
  </table>
  </div>
</div>
<?php endforeach; ?>
<?php require_once ROOT.'/includes/footer.php'; ?>
