<?php
require_once __DIR__ . '/../app/helpers/Util.php';
require_once __DIR__ . '/../app/models/Movimiento.php';

Util::requireLogin();
$user_id = (int)$_SESSION['user']['id'];

$tipo = ($_POST['tipo'] ?? '') === 'salida' ? 'salida' : 'entrada';
$categoria   = trim($_POST['categoria'] ?? 'General');
$descripcion = trim($_POST['descripcion'] ?? '');
$monto       = (float)($_POST['monto'] ?? 0);
$fecha       = $_POST['fecha'] ?? date('Y-m-d');

if ($monto < 0) $monto = abs($monto);

$factura_path = Util::guardarFactura($_FILES['factura'] ?? null);

$ok = false;
if ($tipo === 'entrada') {
    $ok = Movimiento::crearEntrada($user_id, $categoria, $descripcion, $monto, $fecha, $factura_path);
    $_SESSION['flash'] = $ok ? 'Entrada registrada.' : 'No se pudo registrar la entrada.';
    header('Location: /finanzas/public/entradas.php'); exit;
} else {
    $ok = Movimiento::crearSalida($user_id, $categoria, $descripcion, $monto, $fecha, $factura_path);
    $_SESSION['flash'] = $ok ? 'Salida registrada.' : 'No se pudo registrar la salida.';
    header('Location: /finanzas/public/salidas.php'); exit;
}