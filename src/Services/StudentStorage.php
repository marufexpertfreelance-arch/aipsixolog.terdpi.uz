<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Сервис для хранения информации о студентах, которые входили через HEMIS
 */
final class StudentStorage
{
    private string $file;
    private ?array $cache = null;
    private ?int $cacheMtime = null;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $storageDir = $root . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $this->file = $storageDir . '/students.json';
        
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }
    
    /**
     * Очистить кэш (вызывать после изменений)
     */
    private function clearCache(): void
    {
        $this->cache = null;
        $this->cacheMtime = null;
    }

    /**
     * Сохранить или обновить информацию о студенте
     * @param array<string, mixed> $studentData
     */
    public function saveStudent(array $studentData): void
    {
        $students = $this->getAll();
        
        // Используем student_id или id как уникальный ключ
        $studentId = $studentData['student_id'] ?? $studentData['id'] ?? null;
        if (!$studentId) {
            error_log('StudentStorage::saveStudent() - No student ID provided, cannot save');
            return; // Не можем сохранить без ID
        }
        
        // Логируем данные только в режиме разработки
        $isDevelopment = ($_ENV['APP_ENV'] ?? 'production') === 'development';
        if ($isDevelopment) {
            error_log('StudentStorage::saveStudent() - Saving student:');
            error_log('  - Student ID: ' . $studentId);
            error_log('  - Group: ' . var_export($studentData['group'] ?? 'null', true));
            error_log('  - Specialty: ' . var_export($studentData['specialty'] ?? 'null', true));
            error_log('  - Faculty: ' . var_export($studentData['faculty'] ?? 'null', true));
        }
        
        // Проверяем, существует ли уже студент
        $found = false;
        foreach ($students as &$student) {
            $existingId = $student['student_id'] ?? $student['id'] ?? null;
            if ($existingId === $studentId) {
                // Обновляем данные (сохраняем все поля, включая group, specialty, faculty)
                $student = array_merge($student, $studentData, [
                    'last_login' => date('Y-m-d H:i:s'),
                ]);
                $found = true;
                if ($isDevelopment) {
                    error_log('StudentStorage::saveStudent() - Updated existing student');
                }
                break;
            }
        }
        
        if (!$found) {
            // Добавляем нового студента (сохраняем все данные, включая group, specialty, faculty)
            $students[] = array_merge($studentData, [
                'first_login' => date('Y-m-d H:i:s'),
                'last_login' => date('Y-m-d H:i:s'),
            ]);
            if ($isDevelopment) {
                error_log('StudentStorage::saveStudent() - Added new student');
            }
        }
        
        $this->saveAll($students);
        
        // Очищаем кэш после сохранения
        $this->clearCache();
    }

    /**
     * Получить всех студентов
     * @return array<int, array<string, mixed>>
     */
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
     * Получить студента по ID
     * @param string|int $studentId
     * @return array<string, mixed>|null
     */
    public function findById($studentId): ?array
    {
        foreach ($this->getAll() as $student) {
            $id = $student['student_id'] ?? $student['id'] ?? null;
            if ((string)$id === (string)$studentId) {
                return $student;
            }
        }
        return null;
    }

    /**
     * Сохранить всех студентов
     * @param array<int, array<string, mixed>> $students
     */
    private function saveAll(array $students): void
    {
        file_put_contents(
            $this->file,
            json_encode(array_values($students), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
        
        // Очищаем кэш после сохранения
        $this->clearCache();
    }
}

