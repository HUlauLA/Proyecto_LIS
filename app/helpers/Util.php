<?php

class Util {
    public static function startSession(): void {
        if (session_status() !== PHP_SESSION_ACTIVE) session_start();
    }
    public static function requireLogin(): void {
        self::startSession();
        if (!isset($_SESSION['user'])) {
            header('Location: /finanzas/public/login.php'); exit;
        }
    }
    public static function h(string $s): string {
        return htmlspecialchars($s, ENT_QUOTES, 'UTF-8');
    }
    public static function guardarFactura(?array $file): ?string {
        if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) !== UPLOAD_ERR_OK) return null;
        $dir = __DIR__ . '/../../uploads/facturas/';
        if (!is_dir($dir)) @mkdir($dir, 0777, true);
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
        $name = 'fact_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . ($ext ? ".{$ext}" : '');
        $dest = $dir . $name;
        if (move_uploaded_file($file['tmp_name'], $dest)) {
            
            return '/finanzas/uploads/facturas/' . $name;
        }
        return null;
    }
}