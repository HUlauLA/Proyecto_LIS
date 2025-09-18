<?php

require_once __DIR__ . '/../config/db.php';

class Usuario {
    public static function crear(string $nombre, string $email, string $password): bool {
        $pdo = DB::conn();
        $hash = password_hash($password, PASSWORD_BCRYPT);
        $sql = "INSERT INTO users (nombre, email, password_hash) VALUES (?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$nombre, $email, $hash]);
    }

    public static function buscarPorEmail(string $email): ?array {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? LIMIT 1");
        $stmt->execute([$email]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function verificarCredenciales(string $email, string $password): ?array {
        $user = self::buscarPorEmail($email);
        if ($user && password_verify($password, $user['password_hash'])) {
            return [
                'id'     => (int)$user['id'],
                'nombre' => $user['nombre'],
                'email'  => $user['email'],
            ];
        }
        return null;
    }
}