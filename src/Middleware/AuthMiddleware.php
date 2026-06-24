<?php
declare(strict_types=1);

namespace App\Middleware;

class AuthMiddleware
{
    /**
     * Проверяет, авторизован ли администратор
     */
    public static function requireAdmin(): void
    {
        if (empty($_SESSION['admin_logged_in'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Проверяет, авторизован ли пользователь через HEMIS
     */
    public static function requireHemisAuth(): void
    {
        if (empty($_SESSION['hemis_user'])) {
            header('Location: /hemis/login');
            exit;
        }
    }

    /**
     * Проверяет, авторизован ли пользователь (любой способ)
     */
    public static function requireAuth(): void
    {
        if (empty($_SESSION['admin_logged_in']) && empty($_SESSION['hemis_user'])) {
            header('Location: /');
            exit;
        }
    }
}

