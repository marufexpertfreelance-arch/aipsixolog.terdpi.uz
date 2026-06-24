<?php
declare(strict_types=1);

namespace App;

class View
{
    public static function render(string $template, array $data = []): string
    {
        // Извлекаем переменные из массива для использования в шаблоне
        extract($data, EXTR_SKIP);

        // Начинаем буферизацию вывода
        ob_start();

        // Подключаем шаблон
        $templatePath = __DIR__ . '/../views/' . $template . '.php';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("View template not found: {$template}");
        }

        require $templatePath;

        // Получаем содержимое буфера
        $content = ob_get_clean();

        // Layout больше не используется - все шаблоны самодостаточные
        return $content;
    }

    public static function renderPartial(string $template, array $data = []): string
    {
        extract($data, EXTR_SKIP);
        ob_start();
        $templatePath = __DIR__ . '/../views/' . $template . '.php';
        if (!file_exists($templatePath)) {
            throw new \RuntimeException("View template not found: {$template}");
        }
        require $templatePath;
        return ob_get_clean();
    }
}

