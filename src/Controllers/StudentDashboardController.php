<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Services\TestStorage;
use App\Services\ResultsStorage;

/**
 * Контроллер личного кабинета студента
 */
class StudentDashboardController
{
    private TestStorage $testStorage;
    private ResultsStorage $resultsStorage;

    public function __construct()
    {
        $this->testStorage = new TestStorage();
        $this->resultsStorage = new ResultsStorage();
    }

    /** Показываем личный кабинет студента */
    public function index(): void
    {
        // Проверяем авторизацию студента
        if (empty($_SESSION['hemis_user'])) {
            $_SESSION['redirect_after_login'] = '/dashboard';
            header('Location: /hemis/login');
            exit;
        }

        $user = $_SESSION['hemis_user'];
        $studentId = $user['login'] ?? $user['student_id'] ?? null;
        
        // Проверяем, проходил ли студент тест Айзенка
        $hasEysenckResults = $this->hasEysenckResults($studentId);
        
        // Проверяем, проходил ли студент IQ тест
        $hasIqResults = $this->hasIqResults($studentId);
        
        // Проверяем, проходил ли студент тест Люшера
        $hasLusherResults = $this->hasLusherResults($studentId);
        
        // Проверяем, проходил ли студент тест агрессивности
        $hasAggressionResults = $this->hasAggressionResults($studentId);
        
        // Получаем список доступных тестов разделенных по типам
        $allTests = $this->getAvailableTests($studentId);
        $temperamentTests = array_filter($allTests, fn($test) => ($test['type'] ?? '') === 'eysenck' || ($test['type'] ?? '') === 'iq' || ($test['type'] ?? '') === 'lusher' || ($test['type'] ?? '') === 'aggression');
        $customTests = array_filter($allTests, fn($test) => ($test['type'] ?? '') === 'custom');
        
        // Получаем результаты студента и фильтруем удаленные тесты
        $allResults = $studentId ? $this->resultsStorage->getResultsByStudentId($studentId) : [];
        $studentResults = [];
        
        // Добавляем результаты IQ теста
        if ($hasIqResults) {
            $iqResult = $this->getIqResult($studentId);
            if ($iqResult) {
                $studentResults[] = [
                    'test_id' => 'iq',
                    'test_type' => 'iq',
                    'test_title' => 'IQ Test',
                    'calculated_score' => $iqResult['iq_score'],
                    'interpretation' => [
                        'category' => $iqResult['category']['name'],
                        'description' => $iqResult['category']['description'],
                    ],
                    'submitted_at' => $iqResult['completed_at'],
                ];
            }
        }
        
        // Добавляем результаты теста Люшера
        if ($hasLusherResults) {
            $lusherResult = $this->getLusherResult($studentId);
            if ($lusherResult) {
                $studentResults[] = [
                    'test_id' => 'lusher',
                    'test_type' => 'lusher',
                    'test_title' => 'Lyusher Testi',
                    'completed_at' => $lusherResult['completed_at'],
                    'submitted_at' => $lusherResult['completed_at'],
                ];
            }
        }
        
        // Добавляем результаты теста на агрессию
        if ($hasAggressionResults) {
            $aggressionResult = $this->getAggressionResult($studentId);
            if ($aggressionResult) {
                $studentResults[] = [
                    'test_id' => 'aggression',
                    'test_type' => 'aggression',
                    'test_title' => 'Tajovuz holati tashxisi',
                    'calculated_score' => $aggressionResult['indexes']['TI']['score'] ?? null,
                    'interpretation' => [
                        'category' => $aggressionResult['indexes']['TI']['category'] ?? 'Noma\'lum',
                    ],
                    'completed_at' => $aggressionResult['completed_at'] ?? $aggressionResult['submitted_at'] ?? '',
                    'submitted_at' => $aggressionResult['submitted_at'] ?? $aggressionResult['completed_at'] ?? '',
                ];
            }
        }
        
        foreach ($allResults as $result) {
            // Для теста Айзенка всегда показываем
            if (($result['test_type'] ?? '') === 'eysenck') {
                $studentResults[] = $result;
                continue;
            }
            
            // Для теста Люшера или агрессии пропускаем, т.к. уже добавили выше
            if (($result['test_type'] ?? '') === 'lusher' || ($result['test_type'] ?? '') === 'aggression') {
                continue;
            }
            
            // Для кастомных тестов (созданных психологом) НЕ показываем результаты студентам
            // Студенты должны видеть только результаты встроенных тестов (Айзенк, IQ, Люшер)
            if (($result['test_type'] ?? '') === 'custom') {
                // Пропускаем кастомные тесты - студенты не должны видеть их результаты
                continue;
            }
        }

        $error = $_SESSION['hemis_error'] ?? null;
        unset($_SESSION['hemis_error']);
        
        echo View::render('student/dashboard', [
            'title' => 'Shaxsiy kabinet',
            'user' => $user,
            'hasEysenckResults' => $hasEysenckResults,
            'hasIqResults' => $hasIqResults,
            'hasLusherResults' => $hasLusherResults,
            'hasAggressionResults' => $hasAggressionResults,
            'temperamentTests' => $temperamentTests,
            'customTests' => $customTests,
            'studentResults' => $studentResults,
            'error' => $error,
        ]);
    }

    /** Проверяем наличие результатов теста Айзенка */
    private function hasEysenckResults(?string $studentId): bool
    {
        if (!$studentId) {
            return false;
        }

        $resultsPath = dirname(__DIR__, 2) . '/storage/eysenck-results.jsonl';
        if (!file_exists($resultsPath)) {
            return false;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return true;
            }
        }

        return false;
    }

    /** Проверяем наличие результатов IQ теста */
    private function hasIqResults(?string $studentId): bool
    {
        if (!$studentId) {
            return false;
        }

        $resultsPath = dirname(__DIR__, 2) . '/storage/iq-results.jsonl';
        if (!file_exists($resultsPath)) {
            return false;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return true;
            }
        }

        return false;
    }

    /** Получаем результаты IQ теста */
    private function getIqResult(?string $studentId): ?array
    {
        if (!$studentId) {
            return null;
        }

        $resultsPath = dirname(__DIR__, 2) . '/storage/iq-results.jsonl';
        if (!file_exists($resultsPath)) {
            return null;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach (array_reverse($lines) as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return $result;
            }
        }

        return null;
    }

    /** Проверяем наличие результатов теста Люшера */
    private function hasLusherResults(?string $studentId): bool
    {
        if (!$studentId) {
            return false;
        }

        $resultsPath = dirname(__DIR__, 2) . '/storage/lusher-results.jsonl';
        if (!file_exists($resultsPath)) {
            return false;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return true;
            }
        }

        return false;
    }

    /** Получаем результаты теста Люшера */
    private function getLusherResult(?string $studentId): ?array
    {
        if (!$studentId) {
            return null;
        }

        $resultsPath = dirname(__DIR__, 2) . '/storage/lusher-results.jsonl';
        if (!file_exists($resultsPath)) {
            return null;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach (array_reverse($lines) as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return $result;
            }
        }

        return null;
    }

    /** Проверяем наличие результатов теста на агрессию */
    private function hasAggressionResults(?string $studentId): bool
    {
        if (!$studentId) {
            return false;
        }

        $resultsPath = dirname(__DIR__, 2) . '/storage/aggression-results.jsonl';
        if (!file_exists($resultsPath)) {
            return false;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return true;
            }
        }

        return false;
    }

    /** Получаем результаты теста на агрессию */
    private function getAggressionResult(?string $studentId): ?array
    {
        if (!$studentId) {
            return null;
        }

        $resultsPath = dirname(__DIR__, 2) . '/storage/aggression-results.jsonl';
        if (!file_exists($resultsPath)) {
            return null;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach (array_reverse($lines) as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return $result;
            }
        }

        return null;
    }

    /** Получаем список доступных тестов */
    private function getAvailableTests(?string $studentId): array
    {
        $tests = [
            [
                'id' => 'eysenck',
                'name' => 'Temperament',
                'description' => 'Temperamentingizni aniqlash uchun psixologik test',
                'icon_type' => 'brain',
                'questions_count' => 57,
                'duration' => '15-20 daqiqa',
                'url' => '/eysenck/start',
                'results_url' => '/eysenck/results',
                'type' => 'eysenck',
            ],
            [
                'id' => 'iq',
                'name' => 'IQ Test',
                'description' => 'Intellektual qobiliyatlarni baholash uchun test',
                'icon_type' => 'brain',
                'questions_count' => 40,
                'duration' => '20-30 daqiqa',
                'url' => '/iq/start',
                'results_url' => '/iq/results',
                'type' => 'iq',
            ],
            [
                'id' => 'lusher',
                'name' => 'Lyusher Testi',
                'description' => 'Ranglar orqali psixologik holatni aniqlash uchun test',
                'icon_type' => 'brain',
                'questions_count' => 2,
                'duration' => '10-15 daqiqa',
                'url' => '/lusher/start',
                'results_url' => '/lusher/results',
                'type' => 'lusher',
            ],
            [
                'id' => 'aggression',
                'name' => 'Tajovuz holati tashxisi',
                'description' => 'Bassa-Darki so\'rovnomasi asosida',
                'icon_type' => 'brain',
                'questions_count' => 75,
                'duration' => '20-30 daqiqa',
                'url' => '/aggression/start',
                'results_url' => '/aggression/results',
                'type' => 'aggression',
            ],
        ];
        
        // Добавляем тесты, созданные психологом
        // getAll() уже возвращает только существующие (не удаленные) тесты
        $customTests = $this->testStorage->getAll();
        
        // Получаем группу студента из сессии
        $user = $_SESSION['hemis_user'] ?? [];
        $userGroup = $user['group'] ?? null;
        $userFaculty = $user['faculty'] ?? null;
        
        // Логируем данные студента для отладки
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            error_log("StudentDashboard: Student data - Group: '{$userGroup}', Faculty: '{$userFaculty}'");
        }
        
        foreach ($customTests as $test) {
            $testId = (int)($test['id'] ?? 0);
            
            // Дополнительная проверка: убеждаемся, что тест существует
            if ($testId <= 0 || empty($test['title'])) {
                continue; // Пропускаем некорректные тесты
            }
            
            // Дополнительная проверка через findById для надежности
            $existingTest = $this->testStorage->findById($testId);
            if ($existingTest === null) {
                continue; // Тест был удален, пропускаем
            }
            
            // Проверяем доступ по группам с нормализацией
            $allowedGroups = $test['allowed_groups'] ?? [];
            $allowedFaculties = $test['allowed_faculties'] ?? [];
            $openForAll = $test['open_for_all'] ?? false;
            
            // Логируем данные теста для отладки
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                error_log("StudentDashboard: Test {$testId} - Allowed Groups: " . json_encode($allowedGroups));
                error_log("StudentDashboard: Test {$testId} - Allowed Faculties: " . json_encode($allowedFaculties));
                error_log("StudentDashboard: Test {$testId} - Open for all: " . ($openForAll ? 'true' : 'false'));
            }
            
            // Функция нормализации для сравнения (более точная нормализация)
            $normalize = function($str) {
                if ($str === null || $str === '') {
                    return '';
                }
                // Приводим к нижнему регистру и обрезаем пробелы
                $normalized = mb_strtolower(trim((string)$str));
                // Нормализуем множественные пробелы в один
                $normalized = preg_replace('/\s+/', ' ', $normalized);
                // Удаляем неразрывные пробелы и другие невидимые символы
                $normalized = str_replace(["\xC2\xA0", "\xE2\x80\x8B"], ' ', $normalized);
                return trim($normalized);
            };
            
            // ВАЖНО: Проверяем флаг open_for_all
            // Если open_for_all = true И оба массива пусты → доступ разрешен
            // Если open_for_all = false И оба массива пусты → доступ запрещен
            // Если указаны группы или факультеты → проверяем доступ по ним
            $hasAccess = false; // Флаг доступа
            
            // Если оба массива пусты - проверяем флаг open_for_all
            if (empty($allowedGroups) && empty($allowedFaculties)) {
                if ($openForAll) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("StudentDashboard: Test {$testId} - No groups/faculties selected, but open_for_all=true, test is open for all students");
                    }
                } else {
                    // open_for_all = false и нет групп/факультетов → доступ запрещен
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("StudentDashboard: Test {$testId} - No groups/faculties selected and open_for_all=false, access denied");
                    }
                }
            }
            
            // Если указаны группы - проверяем по группам
            if (!empty($allowedGroups) && is_array($allowedGroups)) {
                $normalizedAllowedGroups = array_map($normalize, array_filter($allowedGroups, function($g) {
                    return $g !== null && $g !== '';
                }));
                $normalizedUserGroup = $userGroup ? $normalize($userGroup) : '';
                
                if (!empty($normalizedUserGroup) && in_array($normalizedUserGroup, $normalizedAllowedGroups, true)) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("StudentDashboard: Test {$testId} - Access granted by group match: '{$userGroup}' (normalized: '{$normalizedUserGroup}')");
                    }
                } else {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("StudentDashboard: Test {$testId} - User group '{$userGroup}' (normalized: '{$normalizedUserGroup}') not in allowed groups: " . json_encode($normalizedAllowedGroups));
                    }
                }
            }
            
            // Если указаны факультеты - проверяем по факультетам (ИЛИ логика - если подходит группа или факультет)
            if (!$hasAccess && !empty($allowedFaculties) && is_array($allowedFaculties)) {
                $normalizedAllowedFaculties = array_map($normalize, array_filter($allowedFaculties, function($f) {
                    return $f !== null && $f !== '';
                }));
                $normalizedUserFaculty = $userFaculty ? $normalize($userFaculty) : '';
                
                if (!empty($normalizedUserFaculty) && in_array($normalizedUserFaculty, $normalizedAllowedFaculties, true)) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("StudentDashboard: Test {$testId} - Access granted by faculty match: '{$userFaculty}' (normalized: '{$normalizedUserFaculty}')");
                    }
                } else {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("StudentDashboard: Test {$testId} - User faculty '{$userFaculty}' (normalized: '{$normalizedUserFaculty}') not in allowed faculties: " . json_encode($normalizedAllowedFaculties));
                    }
                }
            }
            
            // Если доступ не предоставлен ни по группе, ни по факультету - пропускаем тест
            if (!$hasAccess) {
                continue;
            }
            
            $questionsCount = count($test['questions'] ?? []);
            if ($questionsCount === 0) {
                continue; // Пропускаем тесты без вопросов
            }
            
            $estimatedDuration = max(5, (int)($questionsCount * 0.5)) . '-' . max(10, (int)($questionsCount * 1)) . ' daqiqa';
            
            // Проверяем, есть ли результаты для этого теста И ответил ли студент на все вопросы
            $hasResults = false;
            $isComplete = false;
            if ($studentId) {
                $studentResults = $this->resultsStorage->getResultsByStudentId($studentId);
                foreach ($studentResults as $result) {
                    if (($result['test_id'] ?? null) == $testId && ($result['test_type'] ?? '') === 'custom') {
                        $hasResults = true;
                        // Проверяем, ответил ли студент на все вопросы
                        $answers = $result['answers'] ?? [];
                        // Подсчитываем количество непустых ответов
                        $answeredCount = 0;
                        foreach ($answers as $answer) {
                            // Проверяем, что ответ не пустой
                            $answerStr = trim((string)$answer);
                            if (!empty($answerStr)) {
                                $answeredCount++;
                            }
                        }
                        // Тест считается завершенным только если ответов столько же, сколько вопросов
                        $isComplete = ($answeredCount >= $questionsCount);
                        if ($isComplete) {
                            break; // Нашли полный результат, можно выйти
                        }
                    }
                }
            }
            
            $tests[] = [
                'id' => $testId,
                'name' => $test['title'] ?? 'Test',
                'description' => $test['description'] ?? '',
                'icon_type' => 'document',
                'questions_count' => $questionsCount,
                'duration' => $estimatedDuration,
                'url' => '/tests/take?id=' . $testId,
                'results_url' => '/tests/results?id=' . $testId,
                'type' => 'custom',
                'has_results' => $hasResults,
                'is_complete' => $isComplete, // Тест завершен полностью (ответы на все вопросы)
            ];
        }
        
        return $tests;
    }
}

