<?php
/**
 * Скрипт для импорта данных структуры университета из HEMIS API
 * Запуск: php import_hemis_data.php
 */

declare(strict_types=1);

require __DIR__ . '/vendor/autoload.php';

use App\Services\UniversityStructureService;

echo "=== Импорт данных структуры университета из HEMIS ===\n\n";

// Путь к скачанному JSON файлу
$jsonFile = __DIR__ . '/hemis student/response_1766066455694.json';

if (!file_exists($jsonFile)) {
    echo "❌ Файл не найден: {$jsonFile}\n";
    echo "Убедитесь, что файл находится в папке 'hemis student'\n";
    exit(1);
}

echo "📁 Файл найден: {$jsonFile}\n\n";

try {
    $service = new UniversityStructureService();
    
    echo "🔄 Импортируем данные...\n";
    $stats = $service->importFromJsonFile($jsonFile);
    
    echo "\n✅ Импорт завершён успешно!\n\n";
    echo "📊 Статистика импорта:\n";
    echo "   - Факультетов импортировано: {$stats['faculties_imported']}\n";
    echo "   - Направлений импортировано: {$stats['specialties_imported']}\n";
    echo "   - Групп импортировано: {$stats['groups_imported']}\n";
    echo "\n";
    echo "📈 Всего в базе:\n";
    echo "   - Факультетов: {$stats['faculties_total']}\n";
    echo "   - Направлений: {$stats['specialties_total']}\n";
    echo "   - Групп: {$stats['groups_total']}\n";
    
    if (isset($stats['pagination'])) {
        $pagination = $stats['pagination'];
        echo "\n";
        echo "📄 Пагинация API:\n";
        echo "   - Страница: {$pagination['page']} из {$pagination['pageCount']}\n";
        echo "   - Всего записей: {$pagination['totalCount']}\n";
        
        if ($pagination['page'] < $pagination['pageCount']) {
            echo "\n";
            echo "⚠️  Загружена только страница {$pagination['page']} из {$pagination['pageCount']}\n";
            echo "   Для полной загрузки скачайте остальные страницы и запустите скрипт повторно.\n";
            echo "   Или используйте параметр limit=200 в API для загрузки большего количества.\n";
        }
    }
    
    echo "\n";
    echo "💾 Данные сохранены в: storage/university_structure.json\n";
    
} catch (\Throwable $e) {
    echo "❌ Ошибка: " . $e->getMessage() . "\n";
    exit(1);
}

echo "\n=== Готово ===\n";
