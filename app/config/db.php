<?php

class DB {
    private static $host = '127.0.0.1';
    private static $db   = 'finanzas';
    private static $user = 'root';
    private static $pass = '';     
    private static $charset = 'utf8mb4';

    public static function conn(): PDO {
        static $pdo = null;
        if ($pdo === null) {
            $dsn = "mysql:host=" . self::$host . ";dbname=" . self::$db . ";charset=" . self::$charset;
            $opts = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
            ];
            try {
                $pdo = new PDO($dsn, self::$user, self::$pass, $opts);
            } catch (PDOException $e) {
                die("Error de conexión: " . $e->getMessage());
            }
        }
        return $pdo;
    }
}