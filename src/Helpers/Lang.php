<?php
declare(strict_types=1);

namespace App\Helpers;

class Lang
{
    private static ?array $strings = null;
    private static string $currentLang = 'uz';
    private static array $cache = [];

    public const LANGUAGES = [
        'uz' => ['name' => "O'zbekcha", 'flag' => '🇺🇿'],
        'ru' => ['name' => 'Русский',   'flag' => '🇷🇺'],
        'en' => ['name' => 'English',   'flag' => '🇬🇧'],
    ];

    public static function init(): void
    {
        $lang = $_COOKIE['locale'] ?? 'uz';
        if (!array_key_exists($lang, self::LANGUAGES)) {
            $lang = 'uz';
        }
        self::$currentLang = $lang;
        self::$strings = self::load($lang);
    }

    public static function current(): string
    {
        if (self::$strings === null) {
            self::init();
        }
        return self::$currentLang;
    }

    public static function get(string $key, string $fallback = ''): string
    {
        if (self::$strings === null) {
            self::init();
        }
        return self::$strings[$key] ?? ($fallback !== '' ? $fallback : $key);
    }

    private static function load(string $lang): array
    {
        if (isset(self::$cache[$lang])) {
            return self::$cache[$lang];
        }
        $file = dirname(__DIR__, 2) . '/lang/' . $lang . '.php';
        if (is_file($file)) {
            $data = require $file;
            self::$cache[$lang] = is_array($data) ? $data : [];
        } else {
            self::$cache[$lang] = [];
        }
        return self::$cache[$lang];
    }
}
