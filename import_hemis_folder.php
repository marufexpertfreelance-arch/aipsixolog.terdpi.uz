<?php
/**
 * Массовый импорт структуры университета из всех JSON файлов в папке "hemis student".
 *
 * Использование:
 *   php import_hemis_folder.php
 *
 * Логика:
 * - Берёт все *.json из папки "hemis student"
 * - Импортирует по очереди (данные объединяются)
 */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Services\UniversityStructureService;

echo "=== Массовый импорт HEMIS group-list JSON ===\n\n";

$folder = __DIR__ . '/hemis student';

if (!is_dir($folder)) {
    echo "❌ Папка не найдена: {$folder}\n";
    exit(1);
}

$files = glob($folder . '/*.json') ?: [];

if (empty($files)) {
    echo "❌ В папке нет JSON файлов: {$folder}\n";
    exit(1);
}

// Сортируем по имени (обычно удобно, если вы сохраняете page_1.json, page_2.json и т.д.)
sort($files, SORT_NATURAL | SORT_FLAG_CASE);

$service = new UniversityStructureService();

$totalImported = [
    'files' => 0,
    'faculties' => 0,
    'specialties' => 0,
    'groups' => 0,
];

foreach ($files as $file) {
    $totalImported['files']++;

    echo "📄 Импорт: " . basename($file) . " ... ";

    try {
        $stats = $service->importFromJsonFile($file);
        $totalImported['faculties'] += (int)($stats['faculties_imported'] ?? 0);
        $totalImported['specialties'] += (int)($stats['specialties_imported'] ?? 0);
        $totalImported['groups'] += (int)($stats['groups_imported'] ?? 0);

        echo "OK";
        if (isset($stats['pagination']['page'], $stats['pagination']['pageCount'])) {
            echo " (page {$stats['pagination']['page']}/{$stats['pagination']['pageCount']})";
        }
        echo "\n";
    } catch (Throwable $e) {
        echo "FAILED\n";
        echo "   ❌ " . $e->getMessage() . "\n";
    }
}

$finalStats = $service->getStatistics();

echo "\n=== Готово ===\n";
echo "Файлов обработано: {$totalImported['files']}\n";
echo "Импортировано (сумма по файлам):\n";
echo "  - Факультеты: {$totalImported['faculties']}\n";
echo "  - Направления: {$totalImported['specialties']}\n";
echo "  - Группы: {$totalImported['groups']}\n";

echo "\nИтог в storage/university_structure.json:\n";
echo "  - Факультеты: " . ($finalStats['faculties_count'] ?? 0) . "\n";
echo "  - Направления: " . ($finalStats['specialties_count'] ?? 0) . "\n";
echo "  - Группы: " . ($finalStats['groups_count'] ?? 0) . "\n";
echo "  - Последнее обновление: " . ($finalStats['last_updated'] ?? 'Never') . "\n";
