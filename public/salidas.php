<?php
require_once __DIR__ . '/../app/helpers/Util.php';
require_once __DIR__ . '/../app/models/Movimiento.php';

Util::requireLogin();
$user  = $_SESSION['user'];
$flash = $_SESSION['flash'] ?? null; unset($_SESSION['flash']);
$items = Movimiento::listarSalidas((int)$user['id']);
?>
<!doctype html>
<html lang="es"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1">
<title>Registrar salida | Finanzas</title>
<link rel="stylesheet" href="/finanzas/public/assets/style.css">
<style>
  .container{max-width:1000px;margin:24px auto;padding:0 16px}
  .card{background:#f6f6f6;border:1px solid #ddd;border-radius:12px;padding:16px;margin-bottom:16px}
  .row{display:flex;gap:12px;flex-wrap:wrap}
  .col{flex:1 1 300px}
  .input, select {width:100%;padding:10px;border-radius:8px;border:1px solid #ccc}
  .btn{display:inline-block;padding:10px 16px;border-radius:8px;border:1px solid #0f62fe;background:#0f62fe;color:#fff;cursor:pointer}
  .table{width:100%;border-collapse:collapse}
  .table th,.table td{border-bottom:1px solid #ddd;padding:8px;text-align:left}
  img.thumb{height:48px;border-radius:6px;border:1px solid #ccc;cursor:pointer}
  .flash{padding:10px;border-radius:8px;background:#e8f6ee;color:#155724;border:1px solid #c3e6cb;margin-bottom:12px}
</style>
<script>
function showBig(src){
  const w = window.open('', '_blank');
  w.document.write(`<img src="${src}" style="max-width:100%;height:auto">`);
}
</script>
</head><body>
<div class="container">
  <?php include __DIR__ . '/_nav.php'; ?>

  <?php if($flash): ?><div class="flash"><?= Util::h($flash) ?></div><?php endif; ?>

  <div class="card">
    <h2>Registrar salida</h2>
    <form method="post" action="/finanzas/public/mov_post.php" enctype="multipart/form-data">
      <input type="hidden" name="tipo" value="salida">
      <div class="row">
        <div class="col">
          <label>Categoría</label>
          <input class="input" name="categoria" placeholder="Alimentos, Transporte, etc." required>
        </div>
        <div class="col">
          <label>Monto</label>
          <input class="input" type="number" step="0.01" min="0" name="monto" required>
        </div>
      </div>
      <div class="row">
        <div class="col">
          <label>Fecha</label>
          <input class="input" type="date" name="fecha" value="<?= date('Y-m-d') ?>" required>
        </div>
        <div class="col">
          <label>Factura (imagen)</label>
          <input class="input" type="file" name="factura" accept="image/*">
        </div>
      </div>
      <label>Descripción (opcional)</label>
      <input class="input" name="descripcion" placeholder="Detalle de la salida">
      <div style="margin-top:12px">
        <button class="btn" type="submit">Guardar salida</button>
      </div>
    </form>
  </div>

  <div class="card">
    <h3>Salidas recientes</h3>
    <table class="table">
      <thead>
        <tr><th>Fecha</th><th>Categoría</th><th>Descripción</th><th>Monto</th><th>Factura</th></tr>
      </thead>
      <tbody>
      <?php foreach($items as $it): ?>
        <tr>
          <td><?= Util::h($it['fecha']) ?></td>
          <td><?= Util::h($it['categoria']) ?></td>
          <td><?= Util::h($it['descripcion'] ?? '') ?></td>
          <td>$<?= number_format((float)$it['monto'], 2) ?></td>
          <td>
            <?php if(!empty($it['factura_path'])): ?>
              <img class="thumb" src="<?= Util::h($it['factura_path']) ?>" onclick="showBig(this.src)" alt="factura">
            <?php else: ?>—<?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>
</body></html>