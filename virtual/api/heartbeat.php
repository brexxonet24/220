<?php
define('ROOT',dirname(__DIR__));
require_once ROOT.'/config.php';
header('Content-Type: application/json');

if(!isset($_SESSION['user_id'])){ echo json_encode(['ok'=>false,'msg'=>'No autorizado']); exit; }
if($_SERVER['REQUEST_METHOD']!=='POST'){ echo json_encode(['ok'=>false]); exit; }

$data=json_decode(file_get_contents('php://input'),true);
if(!$data||!isset($data['actividad_id'],$data['usuario_id'])){ echo json_encode(['ok'=>false]); exit; }

$uid=(int)$data['usuario_id'];
$aid=(int)$data['actividad_id'];
if($uid!==$_SESSION['user_id']){ echo json_encode(['ok'=>false,'msg'=>'ID inválido']); exit; }

// Verificar actividad existe
$act=null;
foreach(getActividades() as $a){ if($a['id']===$aid){$act=$a;break;} }
if(!$act){ echo json_encode(['ok'=>false]); exit; }

// Actualizar sesión parcial
$t=getTracking($uid,$aid);
$t['sa']=(int)($data['sa']??0);
$t['si']=(int)($data['si']??0);
$t['tc']=(int)($data['tc']??0);
$t['fe']=(int)($data['fe']??0);
$t['pa']=(int)($data['pa']??0);
$t['ts']=$data['ts']??date('Y-m-d H:i:s');
$t['completada']=($t['sa']??0)>=($act['minimo_minutos']??0)*60;
if(!isset($t['inicio'])) $t['inicio']=date('Y-m-d H:i:s');
saveTracking($uid,$aid,$t);

echo json_encode(['ok'=>true,'sa'=>$t['sa'],'completada'=>$t['completada']]);
