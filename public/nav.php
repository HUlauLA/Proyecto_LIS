<?php
require_once __DIR__ . '/../app/helpers/Util.php';
Util::requireLogin();
$user = $_SESSION['user'];
?>
<div class="nav" style="display:flex;gap:8px;flex-wrap:wrap;margin-bottom:16px">
  <a href="/finanzas/public/dashboard.php">Dashboard</a>
  <a href="/finanzas/public/entradas.php">Registrar entrada</a>
  <a href="/finanzas/public/salidas.php">Registrar salida</a>
  <a href="/finanzas/public/balance.php">Mostrar balance</a>
  <span style="margin-left:auto">👤 <?= Util::h($user['nombre']) ?> | <a href="/finanzas/public/logout.php">Salir</a></span>
</div>