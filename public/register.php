<?php
require_once __DIR__ . '/../app/helpers/Util.php';
Util::startSession();
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registro | Finanzas</title>
<link rel="stylesheet" href="/finanzas/public/assets/style.css">
</head><body>
<div class="container">
  <div class="card" style="max-width:560px;margin:48px auto;">
    <h2 class="center">Crear cuenta</h2>
    <?php if($flash): ?><div class="flash"><?= Util::h($flash) ?></div><?php endif; ?>
    <form method="post" action="/finanzas/public/register_post.php">
      <div class="row">
        <div class="col"><label>Nombre</label><input class="input" name="nombre" required></div>
        <div class="col"><label>Email</label><input class="input" type="email" name="email" required></div>
      </div>
      <div class="row">
        <div class="col"><label>Contraseña</label><input class="input" type="password" name="password" required></div>
        <div class="col"><label>Repetir contraseña</label><input class="input" type="password" name="password2" required></div>
      </div>
      <div style="margin-top:12px;display:flex;gap:8px;justify-content:space-between;">
        <button class="btn" type="submit">Registrarme</button>
        <a class="btn secondary" href="/finanzas/public/login.php">Volver</a>
      </div>
    </form>
  </div>
</div>
</body></html>