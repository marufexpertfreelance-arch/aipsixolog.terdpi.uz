<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

class AuthController
{
    private const LOGIN = 'Marvarid';
    private const PASSWORD = 'Marvarid@2024!';

    private function isAuthenticated(): bool
    {
        return isset($_SESSION['admin_id']) || !empty($_SESSION['admin_logged_in']);
    }

    public function loginForm(): void
    {
        if ($this->isAuthenticated()) {
            header('Location: /admin');
            exit;
        }

        $title = 'Вход в кабинет психолога';
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_error']);

        require __DIR__ . '/../../../views/admin/login.php';
    }

    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if ($login === self::LOGIN && $password === self::PASSWORD) {
            $_SESSION['admin_id'] = 1;
            $_SESSION['admin_name'] = 'Психолог';
            $_SESSION['admin_logged_in'] = true;
            header('Location: /admin');
            exit;
        }

        $_SESSION['admin_error'] = 'Неверный логин или пароль.';
        header('Location: /admin/login');
        exit;
    }

    public function logout(): void
    {
        unset($_SESSION['admin_id'], $_SESSION['admin_name'], $_SESSION['admin_logged_in']);
        header('Location: /');
        exit;
    }
}
