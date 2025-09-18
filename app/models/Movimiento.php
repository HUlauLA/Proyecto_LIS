<?php

require_once __DIR__ . '/../config/db.php';

class Movimiento {
    public static function crearEntrada(int $user_id, string $categoria, string $descripcion, float $monto, string $fecha, ?string $factura_path): bool {
        $pdo = DB::conn();
        $sql = "INSERT INTO entradas (user_id, categoria, descripcion, monto, fecha, factura_path)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$user_id, $categoria, $descripcion, $monto, $fecha, $factura_path]);
    }

    public static function crearSalida(int $user_id, string $categoria, string $descripcion, float $monto, string $fecha, ?string $factura_path): bool {
        $pdo = DB::conn();
        $sql = "INSERT INTO salidas (user_id, categoria, descripcion, monto, fecha, factura_path)
                VALUES (?, ?, ?, ?, ?, ?)";
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([$user_id, $categoria, $descripcion, $monto, $fecha, $factura_path]);
    }

    public static function listarEntradas(int $user_id): array {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM entradas WHERE user_id = ? ORDER BY fecha DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function listarSalidas(int $user_id): array {
        $pdo = DB::conn();
        $stmt = $pdo->prepare("SELECT * FROM salidas WHERE user_id = ? ORDER BY fecha DESC");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll();
    }

    public static function resumen(int $user_id): array {
        $pdo = DB::conn();
        $sqlEntradas = "SELECT SUM(monto) FROM entradas WHERE user_id = ?";
        $sqlSalidas  = "SELECT SUM(monto) FROM salidas WHERE user_id = ?";
        
        $stmt = $pdo->prepare($sqlEntradas);
        $stmt->execute([$user_id]);
        $entradas = (float)($stmt->fetchColumn() ?? 0);

        $stmt = $pdo->prepare($sqlSalidas);
        $stmt->execute([$user_id]);
        $salidas = (float)($stmt->fetchColumn() ?? 0);

        return [
            'entradas' => $entradas,
            'salidas'  => $salidas,
            'balance'  => $entradas - $salidas
        ];
    }
}