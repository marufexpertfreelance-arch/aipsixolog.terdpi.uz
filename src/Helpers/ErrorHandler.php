<?php
declare(strict_types=1);

namespace App\Helpers;

class ErrorHandler
{
    /**
     * Регистрирует обработчики ошибок
     */
    public static function register(): void
    {
        set_error_handler([self::class, 'handleError']);
        set_exception_handler([self::class, 'handleException']);
        register_shutdown_function([self::class, 'handleShutdown']);
    }

    /**
     * Обработка ошибок
     */
    public static function handleError(int $errno, string $errstr, string $errfile, int $errline): bool
    {
        if (!(error_reporting() & $errno)) {
            return false;
        }

        $errorMessage = sprintf(
            '[%s] %s in %s:%d',
            self::getErrorType($errno),
            $errstr,
            basename($errfile),
            $errline
        );

        error_log($errorMessage);

        // В режиме разработки показываем ошибку
        if (self::isDevelopment()) {
            self::renderError('Xatolik', $errorMessage);
        } else {
            self::renderError('Xatolik yuz berdi', 'Iltimos, keyinroq qayta urinib ko\'ring.');
        }

        return true;
    }

    /**
     * Обработка исключений
     */
    public static function handleException(\Throwable $exception): void
    {
        $errorMessage = sprintf(
            'Exception: %s in %s:%d',
            $exception->getMessage(),
            basename($exception->getFile()),
            $exception->getLine()
        );

        error_log($errorMessage);

        if (self::isDevelopment()) {
            self::renderError('Istisno', $errorMessage, $exception->getTraceAsString());
        } else {
            self::renderError('Xatolik yuz berdi', 'Iltimos, keyinroq qayta urinib ko\'ring.');
        }
    }

    /**
     * Обработка фатальных ошибок
     */
    public static function handleShutdown(): void
    {
        $error = error_get_last();
        if ($error && in_array($error['type'], [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE])) {
            self::handleError(
                $error['type'],
                $error['message'],
                $error['file'],
                $error['line']
            );
        }
    }

    /**
     * Получает тип ошибки по коду
     */
    private static function getErrorType(int $errno): string
    {
        $types = [
            E_ERROR => 'ERROR',
            E_WARNING => 'WARNING',
            E_PARSE => 'PARSE',
            E_NOTICE => 'NOTICE',
            E_CORE_ERROR => 'CORE_ERROR',
            E_CORE_WARNING => 'CORE_WARNING',
            E_COMPILE_ERROR => 'COMPILE_ERROR',
            E_COMPILE_WARNING => 'COMPILE_WARNING',
            E_USER_ERROR => 'USER_ERROR',
            E_USER_WARNING => 'USER_WARNING',
            E_USER_NOTICE => 'USER_NOTICE',
            E_STRICT => 'STRICT',
            E_RECOVERABLE_ERROR => 'RECOVERABLE_ERROR',
            E_DEPRECATED => 'DEPRECATED',
            E_USER_DEPRECATED => 'USER_DEPRECATED',
        ];

        return $types[$errno] ?? 'UNKNOWN';
    }

    /**
     * Проверяет, режим разработки ли это
     */
    private static function isDevelopment(): bool
    {
        return ($_ENV['APP_ENV'] ?? 'production') === 'development';
    }

    /**
     * Отображает страницу ошибки
     */
    private static function renderError(string $title, string $message, ?string $trace = null): void
    {
        http_response_code(500);
        
        echo '<!DOCTYPE html>
<html lang="uz">
<head>
    <meta charset="utf-8">
    <title>Xatolik</title>
    <style>
        body { font-family: system-ui, sans-serif; max-width: 800px; margin: 50px auto; padding: 20px; }
        .error-box { background: #fee; border: 2px solid #fcc; border-radius: 8px; padding: 20px; }
        h1 { color: #c00; margin-top: 0; }
        pre { background: #f5f5f5; padding: 10px; border-radius: 4px; overflow-x: auto; }
    </style>
</head>
<body>
    <div class="error-box">
        <h1>' . htmlspecialchars($title, ENT_QUOTES, 'UTF-8') . '</h1>
        <p>' . htmlspecialchars($message, ENT_QUOTES, 'UTF-8') . '</p>';
        
        if ($trace && self::isDevelopment()) {
            echo '<pre>' . htmlspecialchars($trace, ENT_QUOTES, 'UTF-8') . '</pre>';
        }
        
        echo '</div>
</body>
</html>';
        exit;
    }
}

