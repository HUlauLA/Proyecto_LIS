<?php
require_once __DIR__ . '/../app/helpers/Util.php';
Util::startSession();
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Login | Finanzas</title>
<link rel="stylesheet" href="/finanzas/public/assets/style.css">
</head><body>
<div class="container">
  <div class="card" style="max-width:480px;margin:48px auto;">
    <h2 class="center">Iniciar sesión</h2>
    <?php if($flash): ?><div class="flash"><?= Util::h($flash) ?></div><?php endif; ?>
    <form method="post" action="/finanzas/public/login_post.php">
      <label>Email</label>
      <input class="input" type="email" name="email" required>
      <label>Contraseña</label>
      <input class="input" type="password" name="password" required>
      <div style="margin-top:12px;display:flex;gap:8px;justify-content:space-between;">
        <button class="btn" type="submit">Entrar</button>
        <a class="btn secondary" href="/finanzas/public/register.php">Crear cuenta</a>
      </div>
    </form>
  </div>
</div>
</body></html>