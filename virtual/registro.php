<?php
define('ROOT', __DIR__);
require_once ROOT . '/config.php';



$error = ''; $ok = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre   = trim($_POST['nombre']   ?? '');
    $apellido = trim($_POST['apellido'] ?? '');
    $email    = trim($_POST['email']    ?? '');
    $usuario  = trim($_POST['usuario']  ?? '');
    $pw       = $_POST['password']  ?? '';
    $pw2      = $_POST['password2'] ?? '';
    $rol      = $_POST['rol']       ?? 'alumno';
    $dni      = trim($_POST['dni']  ?? '');

    if (!$nombre || !$apellido || !$email || !$usuario || !$pw || !$rol)
        $error = 'Completá todos los campos obligatorios.';
    elseif ($pw !== $pw2)
        $error = 'Las contraseñas no coinciden.';
    elseif (strlen($pw) < 6)
        $error = 'La contraseña debe tener al menos 6 caracteres.';
    elseif (!in_array($rol, ['profesor', 'alumno']))
        $error = 'Rol inválido.';
    else {
        $usuarios = getUsuarios();
        foreach ($usuarios as $u) {
            if ($u['usuario'] === $usuario) { $error = 'Ese nombre de usuario ya está en uso.'; break; }
            if ($u['email']   === $email)   { $error = 'Ese email ya está registrado.'; break; }
        }
        if (!$error) {
            $nuevo = [
                'id'                    => nextId($usuarios),
                'nombre'                => $nombre,
                'apellido'              => $apellido,
                'email'                 => $email,
                'usuario'               => $usuario,
                'password'              => password_hash($pw, PASSWORD_DEFAULT),
                'rol'                   => $rol,
                'dni'                   => $dni,
                'activo'                => ($rol === 'alumno'),
                'pendiente_aprobacion'  => ($rol === 'profesor'),
                'materia_ids'           => [],
                'created_at'            => date('Y-m-d'),
            ];
            $usuarios[] = $nuevo;
            saveUsuarios($usuarios);
            $ok = $rol === 'profesor'
                ? 'Registro enviado. Un administrador debe activar tu cuenta antes de que puedas ingresar.'
                : '¡Registro exitoso! Ya podés ingresar con tu usuario y contraseña.';
        }
    }
}
$base = BASE_URL;
?><!DOCTYPE html>
<html lang="es">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Registro — AMET 220</title>
<link rel="stylesheet" href="<?=$base?>/assets/css/style.css">
</head>
<body>
<div class="login-wrap" style="align-items:flex-start;padding-top:40px">
  <div style="width:100%;max-width:440px">
    <div class="login-box">
      <div class="login-head">
        <div class="escudo">🎓</div>
        <h1>Instituto Superior 220 AMET</h1>
        <p>Crear cuenta nueva</p>
      </div>
      <div class="login-body">
        <?php if ($ok): ?>
          <div class="alert alert-success"><?=h($ok)?></div>
          <a href="<?=$base?>/index.php" class="btn btn-primary" style="width:100%;justify-content:center">Ir al login</a>
        <?php else: ?>
        <?php if ($error): ?>
          <div class="alert alert-danger"><?=h($error)?></div>
        <?php endif; ?>
        <form method="POST">
          <div class="form-group">
            <label>Rol *</label>
            <select name="rol">
              <option value="alumno"   <?= ($_POST['rol'] ?? 'alumno') === 'alumno'   ? 'selected' : '' ?>>Alumno</option>
              <option value="profesor" <?= ($_POST['rol'] ?? '')        === 'profesor' ? 'selected' : '' ?>>Docente</option>
            </select>
          </div>
          <div class="form-row c2">
            <div class="form-group">
              <label>Nombre *</label>
              <input type="text" name="nombre" value="<?=h($_POST['nombre'] ?? '')?>">
            </div>
            <div class="form-group">
              <label>Apellido *</label>
              <input type="text" name="apellido" value="<?=h($_POST['apellido'] ?? '')?>">
            </div>
          </div>
          <div class="form-group">
            <label>Email *</label>
            <input type="email" name="email" value="<?=h($_POST['email'] ?? '')?>">
          </div>
          <div class="form-group">
            <label>DNI (opcional)</label>
            <input type="text" name="dni" value="<?=h($_POST['dni'] ?? '')?>">
          </div>
          <div class="form-group">
            <label>Nombre de usuario *</label>
            <input type="text" name="usuario" value="<?=h($_POST['usuario'] ?? '')?>">
          </div>
          <div class="form-row c2">
            <div class="form-group">
              <label>Contraseña *</label>
              <input type="password" name="password">
            </div>
            <div class="form-group">
              <label>Repetir contraseña *</label>
              <input type="password" name="password2">
            </div>
          </div>
          <button type="submit" class="btn btn-primary" style="width:100%;justify-content:center;padding:10px">
            Registrarse
          </button>
        </form>
        <?php endif; ?>
      </div>
    </div>
    <div style="text-align:center;margin-top:14px;font-size:12px;color:rgba(255,255,255,.55)">
      ¿Ya tenés cuenta? <a href="<?=$base?>/index.php" style="color:rgba(255,255,255,.8)">Ingresar</a>
    </div>
  </div>
</div>
</body>
</html>
