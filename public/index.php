<?php
require_once __DIR__ . '/../app/helpers/Util.php';
Util::startSession();
if (isset($_SESSION['user'])) {
  header('Location: /finanzas/public/dashboard.php');
} else {
  header('Location: /finanzas/public/login.php');
}
exit;