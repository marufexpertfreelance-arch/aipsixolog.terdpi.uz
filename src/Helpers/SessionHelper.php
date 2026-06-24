<?php
declare(strict_types=1);

namespace App\Helpers;

class SessionHelper
{
    /**
     * Безопасный запуск сессии
     */
    public static function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            // Улучшенные настройки безопасности сессии
            ini_set('session.cookie_httponly', '1');
            ini_set('session.use_only_cookies', '1');
            ini_set('session.cookie_secure', self::isHttps() ? '1' : '0');
            // Используем Lax вместо Strict для OAuth редиректов
            ini_set('session.cookie_samesite', 'Lax');
            
            session_start();
            
            // Регенерация ID сессии для защиты от фиксации сессии
            if (!isset($_SESSION['created'])) {
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            } elseif (time() - $_SESSION['created'] > 1800) {
                // Регенерация каждые 30 минут
                session_regenerate_id(true);
                $_SESSION['created'] = time();
            }
        }
    }

    /**
     * Проверяет, используется ли HTTPS
     */
    private static function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
            return true;
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            return true;
        }
        return false;
    }

    /**
     * Устанавливает flash-сообщение
     */
    public static function setFlash(string $key, string $message): void
    {
        $_SESSION['flash'][$key] = $message;
    }

    /**
     * Получает и удаляет flash-сообщение
     */
    public static function getFlash(string $key): ?string
    {
        $message = $_SESSION['flash'][$key] ?? null;
        unset($_SESSION['flash'][$key]);
        return $message;
    }

    /**
     * Очищает все flash-сообщения
     */
    public static function clearFlash(): void
    {
        unset($_SESSION['flash']);
    }
}

