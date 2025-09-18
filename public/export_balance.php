<?php
require_once __DIR__ . '/../app/helpers/Util.php';
require_once __DIR__ . '/../app/models/Movimiento.php';
Util::requireLogin();

date_default_timezone_set('America/El_Salvador');

$user     = $_SESSION['user'];
$entradas = Movimiento::listarEntradas((int)$user['id']);
$salidas  = Movimiento::listarSalidas((int)$user['id']);

$sum = [
  'entradas' => array_sum(array_map(fn($e)=>(float)$e['monto'], $entradas)),
  'salidas'  => array_sum(array_map(fn($s)=>(float)$s['monto'], $salidas)),
];
$sum['balance'] = $sum['entradas'] - $sum['salidas'];

function rangoFechas(array $entradas, array $salidas): array {
  $fechas = [];
  foreach ($entradas as $e) if (!empty($e['fecha'])) $fechas[] = $e['fecha'];
  foreach ($salidas as $s)  if (!empty($s['fecha'])) $fechas[] = $s['fecha'];
  if ($fechas) { sort($fechas); return [$fechas[0], end($fechas)]; }
  return [date('Y-m-01'), date('Y-m-t')];
}
[$desde, $hasta] = rangoFechas($entradas, $salidas);

function svgPoint(float $cx, float $cy, float $r, float $angleRad): array {
  return [$cx + $r * cos($angleRad), $cy + $r * sin($angleRad)];
}
function svgSlicePath(float $cx, float $cy, float $r, float $startRad, float $endRad): string {
  [$x1, $y1] = svgPoint($cx, $cy, $r, $startRad);
  [$x2, $y2] = svgPoint($cx, $cy, $r, $endRad);
  $largeArc = ($endRad - $startRad) > M_PI ? 1 : 0;
  return sprintf('M %.3f %.3f L %.3f %.3f A %.3f %.3f 0 %d 1 %.3f %.3f Z',
    $cx, $cy, $x1, $y1, $r, $r, $largeArc, $x2, $y2
  );
}
function pieSVG_string(float $v1, float $v2): string {
  $total = max($v1 + $v2, 0.0001);
  $cE = '#4e79a7';  
  $cS = '#e15759';   
  $r = 65; $cx = 95; $cy = 95;

  $p1   = $v1 / $total;
  $ang1 = $p1 * 2 * M_PI;
  $start = -M_PI/2; 
  $mid   = $start + $ang1; 
  $end   = $start + 2*M_PI;

  $d1 = svgSlicePath($cx,$cy,$r,$start,$mid);
  $d2 = svgSlicePath($cx,$cy,$r,$mid,$end);

  $fmt1   = number_format($v1, 2);
  $fmt2   = number_format($v2, 2);
  $label1 = "Entradas (\$$fmt1)";
  $label2 = "Salidas (\$$fmt2)";

  $y1 = 170; 
  $y2 = 190; 
  $y1_text = $y1 + 9; 
  $y2_text = $y2 + 9;

  return <<<SVG
<svg width="260" height="220" viewBox="0 0 190 200" xmlns="http://www.w3.org/2000/svg" aria-label="Gráfico Entradas vs Salidas">
  <defs><style>
    text{font-family: DejaVu Sans, Arial, sans-serif; font-size: 10px;}
  </style></defs>

  <circle cx="{$cx}" cy="{$cy}" r="{$r}" fill="#eeeeee"/>
  <path d="{$d1}" fill="{$cE}"/>
  <path d="{$d2}" fill="{$cS}"/>

  <!-- Leyenda en dos filas separadas -->
  <g>
    <rect x="20"  y="{$y1}" width="10" height="10" fill="{$cE}"/>
    <text x="36" y="{$y1_text}">{$label1}</text>
  </g>
  <g>
    <rect x="110" y="{$y2}" width="10" height="10" fill="{$cS}"/>
    <text x="126" y="{$y2_text}">{$label2}</text>
  </g>
</svg>
SVG;
}

$autoload = __DIR__ . '/../vendor/autoload.php';
if (!file_exists($autoload)) { die('Falta dompdf. Instala con Composer: composer require dompdf/dompdf'); }
require_once $autoload;

use Dompdf\Dompdf;
use Dompdf\Options;

$options = new Options();
$options->set('isRemoteEnabled', true);
$options->set('isHtml5ParserEnabled', true);
$dompdf = new Dompdf($options);

$svg      = pieSVG_string($sum['entradas'], $sum['salidas']);
$svg_base = base64_encode($svg);
$svg_img  = '<img alt="Pastel Entradas vs Salidas" style="display:block;margin:6px auto" width="260" height="220" src="data:image/svg+xml;base64,'.$svg_base.'">';

$html = '<html><head><meta charset="utf-8"><style>
  body{font-family:DejaVu Sans, Arial, sans-serif; color:#111;}
  .panel{background:#fff;border:2px solid #7aa6c2;border-radius:8px;padding:12px;margin-bottom:12px}
  .h2{font-size:16px;text-align:center;margin:0 0 8px 0;letter-spacing:1px}
  .grid{width:100%;display:table;border-spacing:12px 0}
  .col{display:table-cell;width:50%;vertical-align:top;padding-right:8px}
  table{width:100%;border-collapse:collapse}
  th,td{border:1px solid #a9b7c0;padding:6px 8px;text-align:left}
  th{background:#eef5fb}
  .tot{font-weight:bold;background:#f3f7fa}
  .center{text-align:center}
  .small{color:#555}
</style></head><body>';

$html .= '<div class="panel"><div class="h2">Reporte Mensual '.htmlspecialchars($desde).' / '.htmlspecialchars($hasta).'</div>
  <div class="grid">
    <div class="col">
      <table>
        <thead><tr><th colspan="2" class="center">Entradas</th></tr></thead><tbody>';
$tE = 0;
foreach($entradas as $e){ $tE += (float)$e['monto'];
  $html .= '<tr><td>'.htmlspecialchars($e['categoria']).'</td><td>$'.number_format((float)$e['monto'],2).'</td></tr>';
}
$html .= '<tr class="tot"><td>TOTAL</td><td>$'.number_format($tE,2).'</td></tr>';
$html .= '  </tbody></table>
    </div>
    <div class="col">
      <table>
        <thead><tr><th colspan="2" class="center">Salidas</th></tr></thead><tbody>';
$tS = 0;
foreach($salidas as $s){ $tS += (float)$s['monto'];
  $html .= '<tr><td>'.htmlspecialchars($s['categoria']).'</td><td>$'.number_format((float)$s['monto'],2).'</td></tr>';
}
$html .= '<tr class="tot"><td>TOTAL</td><td>$'.number_format($tS,2).'</td></tr>';
$html .= '  </tbody></table>
    </div>
  </div>
  <p class="center" style="margin-top:6px">Balance Mensual: <strong>$'.number_format($sum['balance'],2).'</strong></p>
</div>';

$html .= '<div class="panel">
  <div class="h2">Gráfico de balance mensual Entradas vs Salidas</div>
  <div class="center">'.$svg_img.'</div>
</div>';

$html .= '<p class="small">Usuario: '.htmlspecialchars($user['nombre']).' ('.htmlspecialchars($user['email']).') — Generado el '.date('d/m/Y H:i').'</p>';
$html .= '</body></html>';

$dompdf->loadHtml($html);
$dompdf->setPaper('A4', 'portrait');
$dompdf->render();
$dompdf->stream('balance.pdf', ['Attachment' => true]);
exit;