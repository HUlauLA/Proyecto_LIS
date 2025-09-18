<?php
require_once __DIR__ . '/../app/helpers/Util.php';
Util::requireLogin();
$user = $_SESSION['user'];
?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Dashboard | Finanzas</title>
<link rel="stylesheet" href="/finanzas/public/assets/style.css">
</head><body>
<div class="container">
  <div class="nav">
    <a href="/finanzas/public/dashboard.php">Dashboard</a>
    <a href="/finanzas/public/entradas.php">Registrar entrada</a>
    <a href="/finanzas/public/salidas.php">Registrar salida</a>
    <a href="/finanzas/public/balance.php">Mostrar balance</a>
    <span style="margin-left:auto">👤 <?= Util::h($user['nombre']) ?> | <a href="/finanzas/public/logout.php">Salir</a></span>
  </div>
  <div class="card">
    <h2>Bienvenido, <?= Util::h($user['nombre']) ?></h2>
    <p>Ya estás dentro del sistema. Desde el menú puedes registrar movimientos y ver tu balance.</p>
  </div>
</div>
</body></html>