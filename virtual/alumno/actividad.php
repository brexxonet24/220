<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
if(!isset($_SESSION['user_id'])) redirect('/index.php');
$user=findUser($_SESSION['user_id']);
if(!$user||$user['rol']!=='alumno') redirect('/index.php');

$id=(int)($_GET['id']??0);
$act=null;
foreach(getActividades() as $a){ if($a['id']===$id&&($a['visible']??true)){$act=$a;break;} }
if(!$act){ flash('Actividad no encontrada.','danger'); redirect('/alumno/actividades.php'); }

$ahora=date('H:i'); $hoy=date('Y-m-d');
$enHorario=$ahora>=$act['hora_inicio']&&$ahora<=$act['hora_fin'];
$enFecha=$hoy>=($act['fecha_desde']??'0000-00-00')&&$hoy<=($act['fecha_hasta']??'9999-99-99');
$sesionPrevia=getTracking($user['id'],$id);
$comp=$sesionPrevia['completada']??false;

// Entregar código
if($_SERVER['REQUEST_METHOD']==='POST'&&isset($_POST['entregar'])){
    $t=getTracking($user['id'],$id);
    $t['codigo_entregado']=$_POST['codigo_entregado']??'';
    $t['entrega_texto']=$_POST['entrega_texto']??'';
    $t['completada']=true;
    saveTracking($user['id'],$id,$t);
    flash('Entrega registrada correctamente.');
    redirect('/alumno/actividades.php');
}

$page_title=h($act['titulo']);
$mat=findMateria($act['materia_id']??0);
[$bl,$bc]=tipoBadge($act['tipo']);
$esCodigo=$act['tipo']==='codigo';
$extra_css=$esCodigo?['https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.css','https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/theme/dracula.min.css']:[];
$extra_js=array_merge(
    [BASE_URL.'/assets/js/tracker.js'],
    $esCodigo?[
        'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/codemirror.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/xml/xml.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/javascript/javascript.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/css/css.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/htmlmixed/htmlmixed.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/codemirror/5.65.16/mode/php/php.min.js',
        BASE_URL.'/assets/js/editor.js',
    ]:[]
);
require_once ROOT.'/includes/header.php';
?>

<!-- Tracking bar -->
<div class="track-bar" id="trackbar">
  <div class="track-dot dot-active" id="t-dot"></div>
  <span id="t-status">Iniciando seguimiento...</span>
  <span style="margin-left:auto;font-weight:600;font-family:monospace" id="t-timer">0:00</span>
</div>
<div class="grid g3" style="margin-bottom:12px">
  <div class="stat card-sm"><div class="stat-label">Tiempo activo</div><div class="stat-val" id="m-activo" style="font-size:18px">0:00</div></div>
  <div class="stat card-sm"><div class="stat-label">Idle</div><div class="stat-val" id="m-idle" style="font-size:18px">0:00</div></div>
  <div class="stat card-sm"><div class="stat-label">Interrupciones</div><div class="stat-val" id="m-int" style="font-size:18px">0</div></div>
</div>
<div style="margin-bottom:4px;display:flex;justify-content:space-between;font-size:12px;color:var(--text3)">
  <span>Progreso (mínimo requerido: <?=$act['minimo_minutos']?> min)</span>
  <span id="p-label">0%</span>
</div>
<div class="prog" style="margin-bottom:14px"><div class="prog-fill" id="p-bar" style="width:0%"></div></div>
<div id="alert-box"></div>

<div class="card">
  <div style="display:flex;align-items:center;gap:10px;margin-bottom:14px">
    <span class="badge <?=$bc?>"><?=$bl?></span>
    <?php if($mat): ?><span style="font-size:12px;color:var(--text3)"><?=h($mat['nombre'])?></span><?php endif; ?>
    <span style="margin-left:auto">
      <a href="/alumno/actividades.php" class="btn btn-sm">← Salir</a>
    </span>
  </div>
  <h2 style="font-size:16px;font-weight:600;margin-bottom:10px"><?=h($act['titulo'])?></h2>
  <?php if($act['descripcion']): ?>
  <div style="font-size:13px;line-height:1.7;color:var(--text2);margin-bottom:16px;white-space:pre-wrap"><?=h($act['descripcion'])?></div>
  <?php endif; ?>

  <form method="POST" id="form-actividad">
  <?php if($act['tipo']==='lectura'): ?>
    <div style="background:var(--surface2);border-radius:var(--r);padding:20px;text-align:center;color:var(--text3)">
      📄 El material de lectura se mostraría aquí (PDF embebido o texto del docente)
    </div>

  <?php elseif($act['tipo']==='cuestionario'): ?>
    <?php $preguntas=array_filter(explode("\n",trim($act['descripcion']??'')),fn($l)=>trim($l)); ?>
    <?php foreach(array_values($preguntas) as $i=>$p): ?>
    <div style="margin-bottom:14px">
      <div style="font-size:13px;font-weight:500;margin-bottom:5px"><?=$i+1?>. <?=h($p)?></div>
      <textarea name="resp_<?=$i?>" rows="3" style="width:100%;padding:8px;border:1px solid var(--border2);border-radius:var(--r);font-size:13px;font-family:inherit;resize:vertical"></textarea>
    </div>
    <?php endforeach; ?>

  <?php elseif($act['tipo']==='video'): ?>
    <div style="background:#000;border-radius:var(--r);aspect-ratio:16/9;display:flex;align-items:center;justify-content:center;color:#fff;font-size:13px;margin-bottom:14px">
      [Video del docente — el seguimiento continúa durante la reproducción]
    </div>

  <?php elseif($act['tipo']==='consigna'): ?>
    <div class="form-group">
      <label>Tu respuesta / trabajo</label>
      <textarea name="entrega_texto" rows="6" placeholder="Escribí tu respuesta acá..." style="width:100%"></textarea>
    </div>

  <?php elseif($esCodigo): ?>
    <div style="margin-bottom:10px">
      <div class="alert alert-warning">⚠ <strong>Pegado deshabilitado.</strong> Debés escribir el código manualmente. Los intentos de pegado quedan registrados y visibles para el docente.</div>
    </div>
    <div class="editor-container">
      <div class="editor-toolbar">
        <span style="font-weight:500">Editor — <?=h($act['codigo_lenguaje']??'HTML')?></span>
        <span style="margin-left:auto;display:flex;gap:8px">
          <?php if(in_array($act['codigo_lenguaje']??'htmlmixed',['htmlmixed','html'])): ?>
          <button type="button" id="btn-preview" class="btn btn-sm">▶ Vista previa</button>
          <?php endif; ?>
        </span>
      </div>
      <textarea id="code-editor" name="codigo_entregado" style="width:100%"><?=h($act['codigo_template']??'')?></textarea>
    </div>
    <div id="preview-wrap" style="display:none;margin-top:12px">
      <div style="font-size:12px;color:var(--text3);margin-bottom:4px">Vista previa:</div>
      <iframe id="preview-frame" style="width:100%;height:360px;border:1px solid var(--border2);border-radius:var(--r)" srcdoc=""></iframe>
    </div>
  <?php endif; ?>

  <?php if(!$comp&&($enHorario||$esCodigo)): ?>
  <div style="margin-top:20px;display:flex;gap:10px">
    <button type="submit" name="entregar" class="btn btn-primary" onclick="syncEditor()">
      ✓ Finalizar y entregar
    </button>
    <a href="/alumno/actividades.php" class="btn" onclick="return confirm('¿Salir sin entregar? El tiempo registrado se guardará.')">Salir sin entregar</a>
  </div>
  <?php elseif($comp): ?>
  <div class="alert alert-success" style="margin-top:14px">✓ Esta actividad ya fue completada.</div>
  <?php endif; ?>
  </form>
</div>

<script>
window.AMET_BASE = '<?=BASE_URL?>';
// Init tracker
TRACKER.init({
  actividadId: <?=$act['id']?>,
  usuarioId: <?=$user['id']?>,
  minimoMinutos: <?=$act['minimo_minutos']??0?>,
});

// Block paste on the whole page (non-editor)
document.addEventListener('paste',function(e){
  const tag=document.activeElement.tagName.toLowerCase();
  if(tag!=='div'&&!document.activeElement.classList.contains('CodeMirror-code')) {
    // For non-code-editor fields, allow paste normally
  }
});

// Sync CodeMirror to textarea before submit
function syncEditor(){
  if(typeof EDITOR!=='undefined') {
    document.querySelector('[name=codigo_entregado]').value=EDITOR.getValue();
  }
}

<?php if($esCodigo): ?>
document.addEventListener('DOMContentLoaded',function(){
  EDITOR.init('code-editor',{
    mode:'<?=h($act['codigo_lenguaje']??'htmlmixed')?>',
    template:<?=json_encode($act['codigo_template']??'')?>
  });
  const btn=document.getElementById('btn-preview');
  const wrap=document.getElementById('preview-wrap');
  const frame=document.getElementById('preview-frame');
  if(btn){
    btn.addEventListener('click',function(){
      wrap.style.display=wrap.style.display==='none'?'block':'none';
      if(wrap.style.display==='block') frame.srcdoc=EDITOR.getValue();
    });
  }
});
<?php endif; ?>
</script>
<?php require_once ROOT.'/includes/footer.php'; ?>
