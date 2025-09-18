<?php
require_once __DIR__ . '/../app/helpers/Util.php';
require_once __DIR__ . '/../app/models/Movimiento.php';

Util::requireLogin();
date_default_timezone_set('America/El_Salvador'); 

$user     = $_SESSION['user'];
$entradas = Movimiento::listarEntradas((int)$user['id']);
$salidas  = Movimiento::listarSalidas((int)$user['id']);

// totales
$sum = [
  'entradas' => array_sum(array_map(fn($e)=>(float)$e['monto'], $entradas)),
  'salidas'  => array_sum(array_map(fn($s)=>(float)$s['monto'], $salidas)),
];
$sum['balance'] = $sum['entradas'] - $sum['salidas'];

function rangoFechas(array $entradas, array $salidas): array {
  $fechas = [];
  foreach ($entradas as $e) if (!empty($e['fecha'])) $fechas[] = $e['fecha'];
  foreach ($salidas as $s)  if (!empty($s['fecha'])) $fechas[] = $s['fecha'];
  if ($fechas) {
    sort($fechas);
    return [$fechas[0], end($fechas)];
  }
  $start = date('Y-m-01');
  $end   = date('Y-m-t');
  return [$start, $end];
}
[$desde, $hasta] = rangoFechas($entradas, $salidas);

function svgPoint(float $cx, float $cy, float $r, float $angleRad): array {
  return [$cx + $r * cos($angleRad), $cy + $r * sin($angleRad)];
}
function svgSlicePath(float $cx, float $cy, float $r, float $startRad, float $endRad): string {
  [$x1, $y1] = svgPoint($cx, $cy, $r, $startRad);
  [$x2, $y2] = svgPoint($cx, $cy, $r, $endRad);
  $largeArc = ($endRad - $startRad) > M_PI ? 1 : 0;
  return sprintf(
    'M %.3f %.3f L %.3f %.3f A %.3f %.3f 0 %d 1 %.3f %.3f Z',
    $cx, $cy, $x1, $y1, $r, $r, $largeArc, $x2, $y2
  );
}
function pieSVG(float $v1, float $v2): string {
  $total = max($v1 + $v2, 0.0001);
  $cE = '#4e79a7'; // entradas
  $cS = '#e15759'; // salidas
  $r = 65; $cx = 95; $cy = 95;
  $p1 = $v1 / $total;
  $ang1 = $p1 * 2 * M_PI;
  $start = -M_PI/2;
  $mid   = $start + $ang1;
  $end   = $start + 2 * M_PI;

  $d1 = svgSlicePath($cx, $cy, $r, $start, $mid);
  $d2 = svgSlicePath($cx, $cy, $r, $mid,   $end);

  return '
<svg width="260" height="220" viewBox="0 0 190 190" xmlns="http://www.w3.org/2000/svg" aria-label="Gráfico de balance mensual Entradas vs Salidas">
  <defs><style>
    text{font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px;}
  </style></defs>
  <circle cx="'.$cx.'" cy="'.$cy.'" r="'.$r.'" fill="#eeeeee"/>
  <path d="'.$d1.'" fill="'.$cE.'"/>
  <path d="'.$d2.'" fill="'.$cS.'"/>
  <rect x="20" y="160" width="10" height="10" fill="'.$cE.'"/>
  <text x="36" y="169">Entradas ('.number_format($v1,2).')</text>
  <rect x="110" y="160" width="10" height="10" fill="'.$cS.'"/>
  <text x="126" y="169">Salidas ('.number_format($v2,2).')</text>
</svg>';
}

?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Reporte de Balance</title>
<style>
  body{font-family:DejaVu Sans, Arial, sans-serif; background:#fafafa; color:#111; margin:0}
  .container{max-width:900px;margin:24px auto;padding:0 16px}
  .panel{background:#fff;border:2px solid #7aa6c2;border-radius:8px;padding:14px;margin-bottom:18px}
  .panel h2{margin:4px 0 10px 0;text-align:center;letter-spacing:1px}
  .sub{margin:8px 0 0 0;text-align:center}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:12px}
  table{width:100%;border-collapse:collapse}
  th,td{border:1px solid #a9b7c0;padding:6px 8px;text-align:left}
  th{background:#eef5fb}
  .tot{font-weight:bold;background:#f3f7fa}
  .center{text-align:center}
  .btn{display:inline-block;padding:8px 14px;border-radius:8px;border:1px solid #0f62fe;background:#0f62fe;color:#fff;text-decoration:none}
</style>
</head>
<body>
  <div class="container">
    <div class="panel">
      <h2>Reporte Mensual <?= htmlspecialchars($desde) ?> / <?= htmlspecialchars($hasta) ?></h2>
      <div class="grid">
        <div>
          <table>
            <thead><tr><th colspan="2" class="center">Entradas</th></tr></thead>
            <tbody>
            <?php
              $tE = 0;
              foreach($entradas as $e){
                $tE += (float)$e['monto'];
                echo '<tr><td>'.htmlspecialchars($e['categoria']).'</td><td>$'.number_format((float)$e['monto'],2).'</td></tr>';
              }
              echo '<tr class="tot"><td>TOTAL</td><td>$'.number_format($tE,2).'</td></tr>';
            ?>
            </tbody>
          </table>
        </div>
        <div>
          <table>
            <thead><tr><th colspan="2" class="center">Salidas</th></tr></thead>
            <tbody>
            <?php
              $tS = 0;
              foreach($salidas as $s){
                $tS += (float)$s['monto'];
                echo '<tr><td>'.htmlspecialchars($s['categoria']).'</td><td>$'.number_format((float)$s['monto'],2).'</td></tr>';
              }
              echo '<tr class="tot"><td>TOTAL</td><td>$'.number_format($tS,2).'</td></tr>';
            ?>
            </tbody>
          </table>
        </div>
      </div>
      <p class="center" style="margin-top:10px">Balance Mensual: <strong>$<?= number_format($sum['balance'],2) ?></strong></p>
    </div>

    <div class="panel">
      <h2>Gráfico de balance mensual Entradas vs Salidas</h2>
      <div class="center">
        <?= pieSVG($sum['entradas'], $sum['salidas']) ?>
      </div>
      <div class="center" style="margin-top:10px">
        <a class="btn" href="/finanzas/public/export_balance.php" target="_blank">Exportar PDF</a>
      </div>
    </div>
  </div>
</body>
</html>