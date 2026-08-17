<?php
define('ROOT', __DIR__);
require_once ROOT . '/config.php';

if (isset($_SESSION['user_id'])) {
    $u = findUser($_SESSION['user_id']);
    if ($u) redirect("/{$u['rol']}/index.php");
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario  = trim($_POST['usuario'] ?? '');
    $password = $_POST['password'] ?? '';
    if ($usuario && $password) {
        $found = null;
        foreach (getUsuarios() as $u) {
            if ($u['usuario'] === $usuario && ($u['activo'] ?? true)) {
                $found = $u; break;
            }
        }
        if ($found && password_verify($password, $found['password'])) {
            $_SESSION['user_id'] = $found['id'];
            redirect("/{$found['rol']}/index.php");
        } else {
            $error = 'Usuario o contraseña incorrectos.';
        }
    } else {
        $error = 'Completá todos los campos.';
    }
}
$base = BASE_URL;
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Ingresar — Instituto 220 AMET</title>
<link rel="stylesheet" href="<?=$base?>/assets/css/style.css">
</head>
<body>
<div class="login-wrap">
  <div style="width:100%;max-width:380px">
    <div class="login-box">
      <div class="login-head">
        <div class="escudo">🎓</div>
        <h1>Instituto Superior 220</h1>
        <p>AMET · Técnico Superior en Programación</p>
      </div>
      <div class="login-body">
        <p style="font-size:13px;color:#5F5E5A;margin-bottom:18px;text-align:center">Plataforma de Cursada Virtual</p>
        <?php if ($error): ?>
        <div class="alert alert-danger"><?=h($error)?></div>
        <?php endif; ?>
        <?php if (isset($_GET['msg']) && $_GET['msg'] === 'pendiente'): ?>
        <div class="alert alert-warning">Tu cuenta docente está pendiente de aprobación.</div>
        <?php endif; ?>
        <form method="POST">
          <div class="form-group">
            <label>Usuario</label>
            <input type="text" name="usuario" placeholder="Tu nombre de usuario"
                   autocomplete="username" autofocus value="<?=h($_POST['usuario'] ?? '')?>">
          </div>
          <div class="form-group">
            <label>Contraseña</label>
            <input type="password" name="password" placeholder="••••••••" autocomplete="current-password">
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px">
            Ingresar
          </button>
        </form>
      </div>
    </div>
    <div style="text-align:center;margin-top:14px;font-size:12px;color:rgba(255,255,255,.55)">
      ¿Sos docente y no tenés cuenta?
      <a href="<?=$base?>/registro.php" style="color:rgba(255,255,255,.8)">Registrate acá</a>
    </div>
  </div>
</div>
</body>
</html>
