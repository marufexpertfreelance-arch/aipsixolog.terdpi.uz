<?php
declare(strict_types=1);

namespace App\Controllers;

class LocaleController
{
    private const ALLOWED = ['uz', 'ru', 'en'];
    private const COOKIE_NAME = 'locale';
    private const COOKIE_TTL = 31536000; // 1 year

    /**
     * Установить язык и перенаправить обратно (GET /locale?lang=uz|ru|en).
     */
    public function set(): void
    {
        $lang = isset($_GET['lang']) ? strtolower(trim((string) $_GET['lang'])) : '';
        if (!in_array($lang, self::ALLOWED, true)) {
            $lang = 'uz';
        }

        $path = '/';
        $domain = '';
        $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
        $httponly = true;
        $samesite = 'Lax';

        setcookie(self::COOKIE_NAME, $lang, [
            'expires' => time() + self::COOKIE_TTL,
            'path' => $path,
            'domain' => $domain,
            'secure' => $secure,
            'httponly' => $httponly,
            'samesite' => $samesite,
        ]);

        $_COOKIE[self::COOKIE_NAME] = $lang;

        $back = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : '';
        if ($back !== '' && strpos($back, $_SERVER['HTTP_HOST'] ?? '') !== false) {
            header('Location: ' . $back);
        } else {
            header('Location: /');
        }
        exit;
    }
}
