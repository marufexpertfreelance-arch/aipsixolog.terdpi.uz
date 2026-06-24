<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Простое файловое хранилище тестов психолога.
 * Все тесты лежат в storage/tests.json.
 */
final class TestStorage
{
    private string $file;
    private ?array $cache = null;
    private ?int $cacheMtime = null;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $storageDir = $root . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0777, true);
        }

        $this->file = $storageDir . '/tests.json';

        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

    /** @return array<int, array<string, mixed>> */
    public function getAll(): array
    {
        // Проверяем, изменился ли файл с момента последней загрузки
        $currentMtime = file_exists($this->file) ? filemtime($this->file) : 0;
        
        // Если кэш актуален, возвращаем его
        if ($this->cache !== null && $this->cacheMtime === $currentMtime) {
            return $this->cache;
        }
        
        // Загружаем данные из файла
        $json = file_get_contents($this->file);
        if ($json === false || $json === '') {
            $this->cache = [];
            $this->cacheMtime = $currentMtime;
            return [];
        }
        $data = json_decode($json, true);
        $result = is_array($data) ? $data : [];
        
        // Сохраняем в кэш
        $this->cache = $result;
        $this->cacheMtime = $currentMtime;
        
        return $result;
    }
    
    /**
     * Очистить кэш (вызывать после изменений)
     */
    private function clearCache(): void
    {
        $this->cache = null;
        $this->cacheMtime = null;
    }

    /** @return array<string, mixed>|null */
    public function findById(int $id): ?array
    {
        $allTests = $this->getAll();
        $foundTests = [];
        
        foreach ($allTests as $test) {
            if ((int)($test['id'] ?? 0) === $id) {
                $foundTests[] = $test;
            }
        }
        
        if (empty($foundTests)) {
        return null;
        }
        
        // Если найдено несколько тестов с одинаковым ID, логируем предупреждение
        if (count($foundTests) > 1) {
            error_log('TestStorage::findById() - WARNING: Found ' . count($foundTests) . ' tests with ID ' . $id . '. Returning the first one.');
            // Возвращаем первый тест, но исправляем дубликаты
            $this->fixDuplicateIds();
        }
        
        return $foundTests[0];
    }
    
    /**
     * Найти все тесты с заданным ID
     * @return array<int, array<string, mixed>>
     */
    public function findAllById(int $id): array
    {
        $allTests = $this->getAll();
        $foundTests = [];
        
        foreach ($allTests as $test) {
            if ((int)($test['id'] ?? 0) === $id) {
                $foundTests[] = $test;
            }
        }
        
        return $foundTests;
    }

    /** @param array<int, array<string, mixed>> $tests */
    public function saveAll(array $tests): void
    {
        // Исправляем дубликаты ID перед сохранением
        $tests = $this->fixDuplicateIdsInArray($tests);
        
        file_put_contents(
            $this->file,
            json_encode(array_values($tests), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
        
        // Очищаем кэш после сохранения
        $this->clearCache();
    }

    /** @param array<string, mixed> $testData */
    public function create(array $testData): array
    {
        $tests = $this->getAll();
        $maxId = 0;

        foreach ($tests as $t) {
            $id = (int)($t['id'] ?? 0);
            if ($id > $maxId) {
                $maxId = $id;
            }
        }

        $testData['id'] = $maxId + 1;
        $tests[] = $testData;
        $this->saveAll($tests);

        return $testData;
    }

    /** Удалить тест по ID */
    public function delete(int $id): bool
    {
        $tests = $this->getAll();
        $filtered = array_filter($tests, function ($test) use ($id) {
            return (int)($test['id'] ?? 0) !== $id;
        });
        
        if (count($filtered) === count($tests)) {
            return false; // Тест не найден
        }
        
        $this->saveAll(array_values($filtered));
        return true;
    }

    /** Обновить тест */
    public function update(int $id, array $testData): bool
    {
        $tests = $this->getAll();
        $found = false;
        $foundCount = 0;
        
        // Сначала проверяем, сколько тестов с таким ID
        foreach ($tests as $test) {
            if ((int)($test['id'] ?? 0) === $id) {
                $foundCount++;
            }
        }
        
        // Если найдено несколько тестов с одинаковым ID, исправляем дубликаты
        if ($foundCount > 1) {
            error_log('TestStorage::update() - WARNING: Found ' . $foundCount . ' tests with ID ' . $id . '. Fixing duplicates before update.');
            $tests = $this->fixDuplicateIds();
        }
        
        // Обновляем первый найденный тест (или единственный, если дубликаты исправлены)
        foreach ($tests as &$test) {
            if ((int)($test['id'] ?? 0) === $id) {
                $testData['id'] = $id;
                $testData['created_at'] = $test['created_at'] ?? date('Y-m-d H:i');
                $test = $testData;
                $found = true;
                break;
            }
        }
        
        if (!$found) {
            return false;
        }
        
        $this->saveAll($tests);
        return true;
    }
    
    /**
     * Исправить дубликаты ID в хранилище
     * Автоматически переназначает ID для дубликатов
     * @return array<int, array<string, mixed>> Исправленный массив тестов
     */
    private function fixDuplicateIds(): array
    {
        $tests = $this->getAll();
        $fixedTests = $this->fixDuplicateIdsInArray($tests);
        
        // Сохраняем исправленные данные (без рекурсии)
        file_put_contents(
            $this->file,
            json_encode(array_values($fixedTests), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
        
        // Очищаем кэш после исправления
        $this->clearCache();
        
        return $fixedTests;
    }
    
    /**
     * Исправить дубликаты ID в массиве тестов
     * Автоматически переназначает ID для дубликатов
     * @param array<int, array<string, mixed>> $tests Массив тестов
     * @return array<int, array<string, mixed>> Исправленный массив тестов
     */
    private function fixDuplicateIdsInArray(array $tests): array
    {
        $idCounts = [];
        $maxId = 0;
        $idToTests = []; // Карта ID -> индексы тестов
        
        // Подсчитываем количество каждого ID и находим максимальный
        foreach ($tests as $index => $test) {
            $id = (int)($test['id'] ?? 0);
            if ($id > 0) {
                if (!isset($idCounts[$id])) {
                    $idCounts[$id] = 0;
                    $idToTests[$id] = [];
                }
                $idCounts[$id]++;
                $idToTests[$id][] = $index;
                if ($id > $maxId) {
                    $maxId = $id;
                }
            }
        }
        
        // Находим дубликаты и исправляем их
        $newId = $maxId + 1;
        foreach ($idToTests as $id => $indices) {
            if ($idCounts[$id] > 1) {
                // Есть дубликаты для этого ID
                // Оставляем первый тест с этим ID, остальным переназначаем ID
                $firstIndex = true;
                foreach ($indices as $index) {
                    if (!$firstIndex) {
                        // Это дубликат - переназначаем ID
                        $originalId = $id;
                        $tests[$index]['id'] = $newId;
                        error_log('TestStorage::fixDuplicateIdsInArray() - Test "' . ($tests[$index]['title'] ?? 'Unknown') . '" ID changed from ' . $originalId . ' to ' . $newId);
                        $newId++;
                    }
                    $firstIndex = false;
                }
            }
        }
        
        return $tests;
    }
}
