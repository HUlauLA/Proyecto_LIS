<?php

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../helpers/Util.php';

class AuthController {
    public static function register(): void {
        Util::startSession();
        $nombre    = trim($_POST['nombre'] ?? '');
        $email     = trim($_POST['email'] ?? '');
        $password  = $_POST['password']  ?? '';
        $password2 = $_POST['password2'] ?? '';

        if ($password !== $password2) {
            $_SESSION['flash'] = 'Las contraseñas no coinciden.';
            header('Location: /finanzas/public/register.php'); exit;
        }
        if (Usuario::buscarPorEmail($email)) {
            $_SESSION['flash'] = 'Ese correo ya está registrado.';
            header('Location: /finanzas/public/register.php'); exit;
        }
        try {
            Usuario::crear($nombre, $email, $password);
            $_SESSION['flash'] = 'Registro exitoso. Inicia sesión.';
            header('Location: /finanzas/public/login.php'); exit;
        } catch (Throwable $e) {
            $_SESSION['flash'] = 'Error: ' . $e->getMessage();
            header('Location: /finanzas/public/register.php'); exit;
        }
    }

    public static function login(): void {
        Util::startSession();
        $email    = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $user = Usuario::verificarCredenciales($email, $password);
        if ($user) {
            $_SESSION['user'] = $user;
            header('Location: /finanzas/public/dashboard.php'); exit;
        }
        $_SESSION['flash'] = 'Credenciales inválidas.';
        header('Location: /finanzas/public/login.php'); exit;
    }

    public static function logout(): void {
        Util::startSession();
        session_destroy();
        header('Location: /finanzas/public/login.php'); exit;
    }
}