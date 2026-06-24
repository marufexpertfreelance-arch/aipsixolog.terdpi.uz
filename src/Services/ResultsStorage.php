<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Сервис для работы с результатами тестов
 */
final class ResultsStorage
{
    private string $customResultsFile;
    private string $eysenckResultsFile;
    private string $iqResultsFile;
    private string $lusherResultsFile;
    private string $aggressionResultsFile;

    private const TEACHER_ID_PREFIX = 'teacher_';
    
    // Кэш для результатов тестов
    private static ?array $customResultsCache = null;
    private static ?int $customResultsCacheMtime = null;
    private static ?array $eysenckResultsCache = null;
    private static ?int $eysenckResultsCacheMtime = null;
    private static ?array $iqResultsCache = null;
    private static ?int $iqResultsCacheMtime = null;
    private static ?array $lusherResultsCache = null;
    private static ?int $lusherResultsCacheMtime = null;
    private static ?array $aggressionResultsCache = null;
    private static ?int $aggressionResultsCacheMtime = null;

    private function isTeacherId(string $studentId): bool
    {
        return strncmp($studentId, self::TEACHER_ID_PREFIX, strlen(self::TEACHER_ID_PREFIX)) === 0;
    }

    /**
     * @return array<string, true>
     */
    private function getValidTeacherIdSet(): array
    {
        $teacherService = new TeacherService();
        $teachers = $teacherService->getAll();

        $valid = [];
        foreach ($teachers as $teacher) {
            $id = (string)($teacher['id'] ?? '');
            if ($id !== '' && ctype_digit($id)) {
                $valid[self::TEACHER_ID_PREFIX . $id] = true;
            }
        }

        return $valid;
    }

    /**
     * @return array<string, true>
     */
    private function getValidStudentIdSet(): array
    {
        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();

        $valid = [];
        foreach ($allStudents as $student) {
            $id = (string)($student['student_id'] ?? $student['id'] ?? '');
            if ($id !== '') {
                $valid[$id] = true;
            }
        }

        return $valid;
    }

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $storageDir = $root . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $this->customResultsFile     = $storageDir . '/results.jsonl';
        $this->eysenckResultsFile    = $storageDir . '/eysenck-results.jsonl';
        $this->iqResultsFile         = $storageDir . '/iq-results.jsonl';
        $this->lusherResultsFile     = $storageDir . '/lusher-results.jsonl';
        $this->aggressionResultsFile = $storageDir . '/aggression-results.jsonl';
    }

    /**
     * Полностью удалить все результаты по конкретному student_id
     * (custom + built-in тесты).
     */
    public function deleteAllResultsByStudentId(string $studentId): int
    {
        $deleted = 0;

        $deleted += $this->filterJsonlFileByStudentId($this->customResultsFile, $studentId);
        $deleted += $this->filterJsonlFileByStudentId($this->eysenckResultsFile, $studentId);
        $deleted += $this->filterJsonlFileByStudentId($this->iqResultsFile, $studentId);
        $deleted += $this->filterJsonlFileByStudentId($this->lusherResultsFile, $studentId);
        $deleted += $this->filterJsonlFileByStudentId($this->aggressionResultsFile, $studentId);

        return $deleted;
    }

    private function filterJsonlFileByStudentId(string $filePath, string $studentId): int
    {
        if (!file_exists($filePath)) {
            return 0;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES);
        if ($lines === false) {
            return 0;
        }

        $kept = [];
        $deleted = 0;

        foreach ($lines as $line) {
            $trimmed = trim((string)$line);
            if ($trimmed === '') {
                continue;
            }

            $row = json_decode($trimmed, true);
            if (!is_array($row)) {
                $kept[] = $trimmed;
                continue;
            }

            if (($row['student_id'] ?? null) === $studentId) {
                $deleted++;
                continue;
            }

            $kept[] = $trimmed;
        }

        file_put_contents($filePath, $kept !== [] ? (implode("\n", $kept) . "\n") : '');
        return $deleted;
    }

    /**
     * Получить все результаты (обычные тесты + Айзенк)
     * @return array<int, array<string, mixed>>
     */
    public function getAllResults(): array
    {
        $results = [];
        
        // Читаем результаты обычных тестов
        $customResults = $this->getCustomTestResults();
        foreach ($customResults as $result) {
            $results[] = array_merge($result, ['test_type' => 'custom']);
        }
        
        // Читаем результаты теста Айзенка
        $eysenckResults = $this->getEysenckResults();
        foreach ($eysenckResults as $result) {
            $results[] = array_merge($result, [
                'test_type' => 'eysenck',
                'test_id' => 'eysenck',
                'test_title' => 'Temperament',
            ]);
        }
        
        // Читаем результаты IQ теста
        $iqResults = $this->getIqResults();
        foreach ($iqResults as $result) {
            $results[] = array_merge($result, [
                'test_type' => 'iq',
                'test_id' => 'iq',
                'test_title' => 'IQ Test',
            ]);
        }
        
        // Читаем результаты теста Люшера
        $lusherResults = $this->getLusherResults();
        foreach ($lusherResults as $result) {
            $results[] = array_merge($result, [
                'test_type'  => 'lusher',
                'test_id'    => 'lusher',
                'test_title' => 'Lyusher Testi',
            ]);
        }
        
        // Читаем результаты теста Тажовуз (Buss-Darki)
        $aggressionResults = $this->getAggressionResults();
        foreach ($aggressionResults as $result) {
            $results[] = array_merge($result, [
                'test_type'  => 'aggression',
                'test_id'    => 'aggression',
                'test_title' => 'Tajovuz holati tashxisi',
            ]);
        }
        
        // Сортируем по дате (новые сначала)
        usort($results, function ($a, $b) {
            $dateA = $a['submitted_at'] ?? $a['completed_at'] ?? '';
            $dateB = $b['submitted_at'] ?? $b['completed_at'] ?? '';
            return strcmp($dateB, $dateA);
        });
        
        return $results;
    }

    /**
     * Получить результаты конкретного теста
     * @return array<int, array<string, mixed>>
     */
    public function getResultsByTestId(int $testId): array
    {
        if ($testId === 0 || $testId === -1) {
            return $this->getEysenckResults();
        }
        
        return $this->getCustomTestResultsByTestId($testId);
    }

    /**
     * Получить результаты конкретного студента
     * @return array<int, array<string, mixed>>
     */
    public function getResultsByStudentId(string $studentId): array
    {
        $results = [];
        
        // Ищем в обычных тестах
        $customResults = $this->getCustomTestResults();
        foreach ($customResults as $result) {
            if (($result['student_id'] ?? '') === $studentId) {
                $results[] = array_merge($result, ['test_type' => 'custom']);
            }
        }
        
        // Ищем в тесте Айзенка
        $eysenckResults = $this->getEysenckResults();
        foreach ($eysenckResults as $result) {
            if (($result['student_id'] ?? '') === $studentId) {
                $results[] = array_merge($result, [
                    'test_type' => 'eysenck',
                    'test_id'   => 'eysenck',
                    'test_title'=> 'Temperament',
                ]);
            }
        }
        
        // Ищем в IQ тесте
        $iqResults = $this->getIqResults();
        foreach ($iqResults as $result) {
            if (($result['student_id'] ?? '') === $studentId) {
                $results[] = array_merge($result, [
                    'test_type' => 'iq',
                    'test_id'   => 'iq',
                    'test_title'=> 'IQ Test',
                ]);
            }
        }
        
        // Ищем в Tajovuz тесте
        $aggressionResults = $this->getAggressionResults();
        foreach ($aggressionResults as $result) {
            if (($result['student_id'] ?? '') === $studentId) {
                $results[] = array_merge($result, [
                    'test_type' => 'aggression',
                    'test_id'   => 'aggression',
                    'test_title'=> 'Tajovuz holati tashxisi',
                ]);
            }
        }
        
        // Сортируем по дате
        usort($results, function ($a, $b) {
            $dateA = $a['submitted_at'] ?? $a['completed_at'] ?? '';
            $dateB = $b['submitted_at'] ?? $b['completed_at'] ?? '';
            return strcmp($dateB, $dateA);
        });
        
        return $results;
    }

    /**
     * Получить только результаты теста Айзенка
     * @return array<int, array<string, mixed>>
     */
    public function getEysenckResults(): array
    {
        if (!file_exists($this->eysenckResultsFile)) {
            return [];
        }

        // Проверяем кэш
        $currentMtime = filemtime($this->eysenckResultsFile);
        if (self::$eysenckResultsCache !== null && self::$eysenckResultsCacheMtime === $currentMtime) {
            return self::$eysenckResultsCache;
        }

        $results = [];
        $lines = file($this->eysenckResultsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && is_array($result)) {
                $results[] = $result;
            }
        }
        
        // Сохраняем в кэш
        self::$eysenckResultsCache = $results;
        self::$eysenckResultsCacheMtime = $currentMtime;
        
        return $results;
    }

    /**
     * Получить результаты IQ теста
     * @return array<int, array<string, mixed>>
     */
    public function getIqResults(): array
    {
        if (!file_exists($this->iqResultsFile)) {
            return [];
        }

        // Проверяем кэш
        $currentMtime = filemtime($this->iqResultsFile);
        if (self::$iqResultsCache !== null && self::$iqResultsCacheMtime === $currentMtime) {
            return self::$iqResultsCache;
        }

        $results = [];
        $lines = file($this->iqResultsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && is_array($result)) {
                $results[] = $result;
            }
        }
        
        // Сохраняем в кэш
        self::$iqResultsCache = $results;
        self::$iqResultsCacheMtime = $currentMtime;
        
        return $results;
    }

    /**
     * Получить результаты теста Люшера
     * @return array<int, array<string, mixed>>
     */
    public function getLusherResults(): array
    {
        if (!file_exists($this->lusherResultsFile)) {
            return [];
        }

        // Проверяем кэш
        $currentMtime = filemtime($this->lusherResultsFile);
        if (self::$lusherResultsCache !== null && self::$lusherResultsCacheMtime === $currentMtime) {
            return self::$lusherResultsCache;
        }

        $results = [];
        $lines = file($this->lusherResultsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && is_array($result)) {
                $results[] = $result;
            }
        }
        
        // Сохраняем в кэш
        self::$lusherResultsCache = $results;
        self::$lusherResultsCacheMtime = $currentMtime;
        
        return $results;
    }

    /**
     * Получить результаты теста Тажовуз (Buss-Darki)
     * @return array<int, array<string, mixed>>
     */
    public function getAggressionResults(): array
    {
        if (!file_exists($this->aggressionResultsFile)) {
            return [];
        }

        // Проверяем кэш
        $currentMtime = filemtime($this->aggressionResultsFile);
        if (self::$aggressionResultsCache !== null && self::$aggressionResultsCacheMtime === $currentMtime) {
            return self::$aggressionResultsCache;
        }

        $results = [];
        $lines = file($this->aggressionResultsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && is_array($result)) {
                $results[] = $result;
            }
        }
        
        // Сохраняем в кэш
        self::$aggressionResultsCache = $results;
        self::$aggressionResultsCacheMtime = $currentMtime;
        
        return $results;
    }

    /**
     * Получить статистику по тесту Тажовуз (Buss-Darki)
     * @return array<string, mixed>
     */
    public function getAggressionStatistics(): array
    {
        $aggressionResults = $this->getAggressionResults();

        $validStudentIds = $this->getValidStudentIdSet();
        $aggressionResults = array_values(array_filter($aggressionResults, function ($result) use ($validStudentIds) {
            $studentId = (string)($result['student_id'] ?? '');
            return $studentId !== '' && isset($validStudentIds[$studentId]);
        }));

        if (empty($aggressionResults)) {
            return [
                'total_completions' => 0,
                'unique_students'   => 0,
                'avg_ti'            => null,
                'avg_di'            => null,
                'ti_level_dist'     => [],
                'di_level_dist'     => [],
            ];
        }

        $studentIds = [];
        $tiScores   = [];
        $diScores   = [];
        $tiLevels   = [];
        $diLevels   = [];

        foreach ($aggressionResults as $result) {
            $studentId = $result['student_id'] ?? null;
            if ($studentId && !in_array($studentId, $studentIds, true)) {
                $studentIds[] = $studentId;
            }
            if (isset($result['ti'])) {
                $tiScores[] = (float)$result['ti'];
            }
            if (isset($result['di'])) {
                $diScores[] = (float)$result['di'];
            }
            $tiLevel = $result['ti_level'] ?? '';
            if ($tiLevel) {
                $tiLevels[$tiLevel] = ($tiLevels[$tiLevel] ?? 0) + 1;
            }
            $diLevel = $result['di_level'] ?? '';
            if ($diLevel) {
                $diLevels[$diLevel] = ($diLevels[$diLevel] ?? 0) + 1;
            }
        }

        return [
            'total_completions' => count($aggressionResults),
            'unique_students'   => count($studentIds),
            'avg_ti'            => !empty($tiScores) ? round(array_sum($tiScores) / count($tiScores), 2) : null,
            'avg_di'            => !empty($diScores) ? round(array_sum($diScores) / count($diScores), 2) : null,
            'ti_level_dist'     => $tiLevels,
            'di_level_dist'     => $diLevels,
        ];
    }

    /**
     * Получить только результаты обычных тестов
     * @return array<int, array<string, mixed>>
     */
    public function getCustomTestResults(): array
    {
        if (!file_exists($this->customResultsFile)) {
            return [];
        }

        // Проверяем кэш
        $currentMtime = filemtime($this->customResultsFile);
        if (self::$customResultsCache !== null && self::$customResultsCacheMtime === $currentMtime) {
            return self::$customResultsCache;
        }

        $results = [];
        $lines = file($this->customResultsFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && is_array($result)) {
                $results[] = $result;
            }
        }
        
        // Сохраняем в кэш
        self::$customResultsCache = $results;
        self::$customResultsCacheMtime = $currentMtime;
        
        return $results;
    }

    /**
     * Получить результаты конкретного обычного теста
     * @return array<int, array<string, mixed>>
     */
    private function getCustomTestResultsByTestId(int $testId): array
    {
        $allResults = $this->getCustomTestResults();
        $filtered = [];
        
        foreach ($allResults as $result) {
            if (($result['test_id'] ?? 0) === $testId) {
                $filtered[] = $result;
            }
        }
        
        return $filtered;
    }

    /**
     * Фильтровать результаты, оставляя только полные (все вопросы отвечены)
     * @param array<int, array<string, mixed>> $results Результаты теста
     * @param array<string, mixed>|null $test Данные теста (если null, проверка не выполняется)
     * @return array<int, array<string, mixed>>
     */
    public function filterCompleteResults(array $results, ?array $test = null): array
    {
        if ($test === null) {
            // Если тест не передан, возвращаем все результаты (для обратной совместимости)
            return $results;
        }

        $totalQuestions = count($test['questions'] ?? []);
        if ($totalQuestions === 0) {
            // Если в тесте нет вопросов, возвращаем все результаты
            return $results;
        }

        $completeResults = [];

        foreach ($results as $result) {
            $answers = $result['answers'] ?? [];
            $answeredQuestions = 0;

            // Проверяем каждый вопрос теста
            foreach ($test['questions'] ?? [] as $qIdx => $question) {
                $questionType = $question['type'] ?? 'text';
                $hasAnswer = false;

                if ($questionType === 'multiple_select') {
                    // Для multiple_select проверяем, что есть хотя бы один выбранный вариант
                    if (isset($answers[$qIdx]) && !empty(trim((string)$answers[$qIdx]))) {
                        $hasAnswer = true;
                    }
                } else {
                    // Для остальных типов проверяем, что ответ не пустой
                    if (isset($answers[$qIdx]) && !empty(trim((string)$answers[$qIdx]))) {
                        $hasAnswer = true;
                    }
                }

                if ($hasAnswer) {
                    $answeredQuestions++;
                }
            }

            // Если все вопросы отвечены, добавляем результат
            if ($answeredQuestions >= $totalQuestions) {
                $completeResults[] = $result;
            }
        }

        return $completeResults;
    }

    /**
     * Получить статистику по конкретному тесту
     * @param int $testId ID теста
     * @param array<string> $allowedGroups Разрешенные группы (опционально)
     * @param array<string> $allowedFaculties Разрешенные факультеты (опционально)
     * @param array<string, mixed>|null $test Данные теста (опционально, для фильтрации полных результатов)
     * @return array<string, mixed>
     */
    public function getTestStatistics(int $testId, array $allowedGroups = [], array $allowedFaculties = [], ?array $test = null, string $audience = 'students'): array
    {
        $results = $this->getResultsByTestId($testId);

        $audience = $audience ?: 'students';
        $audience = in_array($audience, ['students', 'teachers', 'all'], true) ? $audience : 'students';

        // Учитываем только валидных пользователей по выбранной аудитории
        $validStudentIds = ($audience === 'teachers') ? [] : $this->getValidStudentIdSet();
        $validTeacherIds = ($audience === 'students') ? [] : $this->getValidTeacherIdSet();

        $results = array_values(array_filter($results, function ($result) use ($audience, $validStudentIds, $validTeacherIds) {
            $studentId = (string)($result['student_id'] ?? '');
            if ($studentId === '') {
                return false;
            }

            if ($audience === 'students') {
                return isset($validStudentIds[$studentId]);
            }

            if ($audience === 'teachers') {
                return isset($validTeacherIds[$studentId]);
            }

            // all
            return isset($validStudentIds[$studentId]) || isset($validTeacherIds[$studentId]);
        }));
        
        // Фильтруем результаты по разрешенным группам/факультетам, если они указаны
        // Фильтры групп/факультетов применимы только к студентам
        if (($audience === 'students' || $audience === 'all') && (!empty($allowedGroups) || !empty($allowedFaculties))) {
            $results = $this->filterResultsByAllowedGroups($results, $allowedGroups, $allowedFaculties);
        }
        
        // Фильтруем только полные результаты (все вопросы отвечены)
        if ($test !== null) {
            $results = $this->filterCompleteResults($results, $test);
        }
        
        $stats = [
            'total_completions' => count($results),
            'unique_students' => 0,
            'average_scores' => [],
            'date_range' => [
                'first' => null,
                'last' => null,
            ],
        ];
        
        if (empty($results)) {
            return $stats;
        }
        
        // Уникальные студенты
        $studentIds = [];
        foreach ($results as $result) {
            $studentId = $result['student_id'] ?? null;
            if ($studentId && !in_array($studentId, $studentIds, true)) {
                $studentIds[] = $studentId;
            }
        }
        $stats['unique_students'] = count($studentIds);
        
        // Диапазон дат
        $dates = [];
        foreach ($results as $result) {
            $date = $result['submitted_at'] ?? $result['completed_at'] ?? null;
            if ($date) {
                $dates[] = $date;
            }
        }
        if (!empty($dates)) {
            sort($dates);
            $stats['date_range']['first'] = $dates[0];
            $stats['date_range']['last'] = end($dates);
        }
        
        // Для теста Айзенка - статистика по темпераментам
        if ($testId === 0 || $testId === -1) {
            $temperaments = [];
            foreach ($results as $result) {
                $temp = $result['temperament']['type'] ?? null;
                if ($temp) {
                    $temperaments[$temp] = ($temperaments[$temp] ?? 0) + 1;
                }
            }
            $stats['temperament_distribution'] = $temperaments;
        } else {
            // Для обычных тестов - проверяем, есть ли расчетные результаты
            $hasCalculatedScores = false;
            $calculatedScores = [];
            foreach ($results as $result) {
                $calculatedScore = $result['calculated_score'] ?? null;
                if ($calculatedScore !== null) {
                    $hasCalculatedScores = true;
                    $calculatedScores[] = (float)$calculatedScore;
                }
            }
            
            if ($hasCalculatedScores && !empty($calculatedScores)) {
                // Используем расчетные баллы для статистики
                $stats['average_calculated_score'] = round(array_sum($calculatedScores) / count($calculatedScores), 2);
                $stats['min_calculated_score'] = min($calculatedScores);
                $stats['max_calculated_score'] = max($calculatedScores);
            } else {
                // Fallback: средние баллы по вопросам (если шкала)
                $questionScores = [];
                foreach ($results as $result) {
                    $answers = $result['answers'] ?? [];
                    foreach ($answers as $qIndex => $answer) {
                        if (is_numeric($answer)) {
                            $questionScores[$qIndex][] = (int)$answer;
                        }
                    }
                }
                foreach ($questionScores as $qIndex => $scores) {
                    $stats['average_scores'][$qIndex] = round(array_sum($scores) / count($scores), 2);
                }
            }
        }
        
        return $stats;
    }

    /**
     * Получить общую статистику
     * @return array<string, mixed>
     */
    public function getOverallStatistics(string $audience = 'students'): array
    {
        $allResults = $this->getAllResults();
        $customResults = $this->getCustomTestResults();
        $eysenckResults = $this->getEysenckResults();
        $iqResults = $this->getIqResults();
        $lusherResults = $this->getLusherResults();

        $audience = in_array($audience, ['students', 'teachers', 'all'], true) ? $audience : 'students';

        $validStudentIds = ($audience === 'teachers') ? [] : $this->getValidStudentIdSet();
        $validTeacherIds = ($audience === 'students') ? [] : $this->getValidTeacherIdSet();

        $isAllowed = function (string $studentId) use ($audience, $validStudentIds, $validTeacherIds): bool {
            if ($studentId === '') {
                return false;
            }
            if ($audience === 'students') {
                return isset($validStudentIds[$studentId]);
            }
            if ($audience === 'teachers') {
                return isset($validTeacherIds[$studentId]);
            }
            return isset($validStudentIds[$studentId]) || isset($validTeacherIds[$studentId]);
        };

        $allResults = array_values(array_filter($allResults, function ($result) use ($isAllowed) {
            $studentId = (string)($result['student_id'] ?? '');
            return $isAllowed($studentId);
        }));

        $customResults = array_values(array_filter($customResults, function ($result) use ($isAllowed) {
            $studentId = (string)($result['student_id'] ?? '');
            return $isAllowed($studentId);
        }));

        $eysenckResults = array_values(array_filter($eysenckResults, function ($result) use ($isAllowed) {
            $studentId = (string)($result['student_id'] ?? '');
            return $isAllowed($studentId);
        }));

        $iqResults = array_values(array_filter($iqResults, function ($result) use ($isAllowed) {
            $studentId = (string)($result['student_id'] ?? '');
            return $isAllowed($studentId);
        }));

        $lusherResults = array_values(array_filter($lusherResults, function ($result) use ($isAllowed) {
            $studentId = (string)($result['student_id'] ?? '');
            return $isAllowed($studentId);
        }));
        
        // Уникальные студенты из результатов тестов
        $studentIds = [];
        foreach ($allResults as $result) {
            $studentId = $result['student_id'] ?? null;
            if ($studentId && !in_array($studentId, $studentIds, true)) {
                $studentIds[] = $studentId;
            }
        }
        
        // Получаем общее количество пользователей в выбранной аудитории
        if ($audience === 'teachers') {
            $teacherService = new TeacherService();
            $totalStudentsInSystem = count($teacherService->getAll());
        } elseif ($audience === 'all') {
            $studentStorage = new StudentStorage();
            $allStudents = $studentStorage->getAll();
            $teacherService = new TeacherService();
            $totalStudentsInSystem = count($allStudents) + count($teacherService->getAll());
        } else {
            $studentStorage = new StudentStorage();
            $allStudents = $studentStorage->getAll();
            $totalStudentsInSystem = count($allStudents);
        }
        
        // Статистика по типам тестов
        $aggressionResults = array_values(array_filter($this->getAggressionResults(), function ($result) use ($isAllowed) {
            return $isAllowed((string)($result['student_id'] ?? ''));
        }));
        $testTypes = [
            'custom'     => count($customResults),
            'eysenck'    => count($eysenckResults),
            'iq'         => count($iqResults),
            'lusher'     => count($lusherResults),
            'aggression' => count($aggressionResults),
        ];
        
        // Статистика по тестам (для обычных тестов)
        $testStats = [];
        $testIds = [];
        foreach ($customResults as $result) {
            $testId = $result['test_id'] ?? 0;
            if ($testId && !in_array($testId, $testIds, true)) {
                $testIds[] = $testId;
            }
        }
        
        foreach ($testIds as $testId) {
            $testResults = $this->getCustomTestResultsByTestId($testId);
            if (!empty($testResults)) {
                $testStats[$testId] = [
                    'id' => $testId,
                    'title' => $testResults[0]['test_title'] ?? 'Test #' . $testId,
                    'completions' => count($testResults),
                ];
            }
        }
        
        return [
            'total_results'                    => count($allResults),
            'total_students'                   => $totalStudentsInSystem,
            'students_with_results'            => count($studentIds),
            'test_types'                       => $testTypes,
            'test_statistics'                  => $testStats,
            'eysenck_temperament_distribution' => $this->getEysenckTemperamentDistribution(),
            'iq_statistics'                    => $this->getIqStatistics(),
            'lusher_statistics'                => $this->getLusherStatistics(),
            'aggression_statistics'            => $this->getAggressionStatistics(),
        ];
    }

    /**
     * Получить статистику по IQ тесту
     * @return array<string, mixed>
     */
    public function getIqStatistics(): array
    {
        $iqResults = $this->getIqResults();

        $validStudentIds = $this->getValidStudentIdSet();
        $iqResults = array_values(array_filter($iqResults, function ($result) use ($validStudentIds) {
            $studentId = (string)($result['student_id'] ?? '');
            return $studentId !== '' && isset($validStudentIds[$studentId]);
        }));
        
        if (empty($iqResults)) {
            return [
                'total_completions' => 0,
                'unique_students' => 0,
                'average_iq' => null,
                'min_iq' => null,
                'max_iq' => null,
                'category_distribution' => [],
            ];
        }
        
        // Уникальные студенты
        $studentIds = [];
        $iqScores = [];
        $categories = [];
        
        foreach ($iqResults as $result) {
            $studentId = $result['student_id'] ?? null;
            if ($studentId && !in_array($studentId, $studentIds, true)) {
                $studentIds[] = $studentId;
            }
            
            $iqScore = $result['iq_score'] ?? null;
            if ($iqScore !== null) {
                $iqScores[] = (int)$iqScore;
            }
            
            $categoryName = $result['category']['name'] ?? '';
            if ($categoryName) {
                $categories[$categoryName] = ($categories[$categoryName] ?? 0) + 1;
            }
        }
        
        $stats = [
            'total_completions' => count($iqResults),
            'unique_students' => count($studentIds),
            'category_distribution' => $categories,
        ];
        
        if (!empty($iqScores)) {
            $stats['average_iq'] = round(array_sum($iqScores) / count($iqScores), 1);
            $stats['min_iq'] = min($iqScores);
            $stats['max_iq'] = max($iqScores);
        } else {
            $stats['average_iq'] = null;
            $stats['min_iq'] = null;
            $stats['max_iq'] = null;
        }
        
        return $stats;
    }

    /**
     * Получить статистику по тесту Люшера
     * @return array<string, mixed>
     */
    public function getLusherStatistics(): array
    {
        $lusherResults = $this->getLusherResults();

        $validStudentIds = $this->getValidStudentIdSet();
        $lusherResults = array_values(array_filter($lusherResults, function ($result) use ($validStudentIds) {
            $studentId = (string)($result['student_id'] ?? '');
            return $studentId !== '' && isset($validStudentIds[$studentId]);
        }));
        
        if (empty($lusherResults)) {
            return [
                'total_completions' => 0,
                'unique_students' => 0,
                'preferred_colors_distribution' => [],
                'rejected_colors_distribution' => [],
            ];
        }
        
        // Уникальные студенты
        $studentIds = [];
        $preferredColors = [];
        $rejectedColors = [];
        
        foreach ($lusherResults as $result) {
            $studentId = $result['student_id'] ?? null;
            if ($studentId && !in_array($studentId, $studentIds, true)) {
                $studentIds[] = $studentId;
            }
            
            // Анализируем первый раунд (предпочитаемые)
            $round1 = $result['round1_selection'] ?? [];
            if (!empty($round1) && is_array($round1)) {
                // Первые 2 позиции - наиболее предпочитаемые
                for ($i = 0; $i < min(2, count($round1)); $i++) {
                    $colorId = $round1[$i] ?? null;
                    if ($colorId !== null) {
                        $preferredColors[$colorId] = ($preferredColors[$colorId] ?? 0) + 1;
                    }
                }
            }
            
            // Анализируем второй раунд (отвергаемые) - последние позиции
            $round2 = $result['round2_selection'] ?? [];
            if (!empty($round2) && is_array($round2)) {
                // Последние 2 позиции - наиболее отвергаемые
                for ($i = max(0, count($round2) - 2); $i < count($round2); $i++) {
                    $colorId = $round2[$i] ?? null;
                    if ($colorId !== null) {
                        $rejectedColors[$colorId] = ($rejectedColors[$colorId] ?? 0) + 1;
                    }
                }
            }
        }
        
        return [
            'total_completions' => count($lusherResults),
            'unique_students' => count($studentIds),
            'preferred_colors_distribution' => $preferredColors,
            'rejected_colors_distribution' => $rejectedColors,
        ];
    }

    /**
     * Получить распределение темпераментов по тесту Айзенка
     * @return array<string, int>
     */
    private function getEysenckTemperamentDistribution(): array
    {
        $results = $this->getEysenckResults();
        $distribution = [
            'Choleric' => 0,
            'Sanguine' => 0,
            'Phlegmatic' => 0,
            'Melancholic' => 0,
        ];
        
        foreach ($results as $result) {
            $temp = $result['temperament']['type'] ?? null;
            if ($temp && isset($distribution[$temp])) {
                $distribution[$temp]++;
            }
        }
        
        return $distribution;
    }

    /**
     * Получить список всех уникальных студентов
     * @return array<int, array<string, mixed>>
     */
    public function getAllStudents(): array
    {
        $allResults = $this->getAllResults();
        $students = [];

        $validStudentIds = $this->getValidStudentIdSet();
        
        foreach ($allResults as $result) {
            $studentId = $result['student_id'] ?? null;
            if ($studentId && isset($validStudentIds[(string)$studentId])) {
                if (!isset($students[$studentId])) {
                    $students[$studentId] = [
                        'student_id' => $studentId,
                        'student_name' => $result['student_name'] ?? 'Noma\'lum',
                        'tests_count' => 0,
                        'last_test_date' => null,
                    ];
                }
                $students[$studentId]['tests_count']++;
                
                $date = $result['submitted_at'] ?? $result['completed_at'] ?? null;
                if ($date && (!$students[$studentId]['last_test_date'] || $date > $students[$studentId]['last_test_date'])) {
                    $students[$studentId]['last_test_date'] = $date;
                }
            }
        }
        
        return array_values($students);
    }

    /**
     * Получить результаты по группе
     * @return array<int, array<string, mixed>>
     */
    public function getResultsByGroup(string $group, ?int $testId = null): array
    {
        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();
        
        // Получаем ID студентов из этой группы
        $studentIds = [];
        $normalize = function($str) {
            if ($str === null || $str === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$str));
            $apostrophes = [
                "\xCA\xBC", "\xE2\x80\x99", "\xE2\x80\x98",
                "\xE2\x80\x9C", "\xE2\x80\x9D", '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };
        
        $groupNormalized = $normalize($group);
        foreach ($allStudents as $student) {
            $studentGroup = $student['group'] ?? null;
            if ($studentGroup && $normalize($studentGroup) === $groupNormalized) {
                $studentId = $student['student_id'] ?? $student['id'] ?? null;
                if ($studentId) {
                    $studentIds[] = (string)$studentId;
                }
            }
        }
        
        if (empty($studentIds)) {
            return [];
        }
        
        // Получаем результаты теста
        $testId = $testId ?? 0; // По умолчанию тест Айзенка
        $allResults = $this->getResultsByTestId($testId);
        
        // Фильтруем результаты по ID студентов
        $filtered = [];
        foreach ($allResults as $result) {
            $resultStudentId = (string)($result['student_id'] ?? '');
            if (in_array($resultStudentId, $studentIds, true)) {
                $filtered[] = $result;
            }
        }
        
        return $filtered;
    }
    
    /**
     * Получить статистику по факультету
     * @return array<string, mixed>
     */
    public function getFacultyStatistics(string $faculty, ?int $testId = null): array
    {
        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();
        
        // Нормализация для сравнения
        $normalize = function($str) {
            if ($str === null || $str === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$str));
            $apostrophes = [
                "\xCA\xBC", "\xE2\x80\x99", "\xE2\x80\x98",
                "\xE2\x80\x9C", "\xE2\x80\x9D", '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };
        
        $facultyNormalized = $normalize($faculty);
        
        // Получаем студентов факультета и их группы
        $groups = [];
        $studentIds = [];
        foreach ($allStudents as $student) {
            $studentFaculty = $student['faculty'] ?? null;
            if ($studentFaculty && $normalize($studentFaculty) === $facultyNormalized) {
                $studentGroup = $student['group'] ?? null;
                if ($studentGroup) {
                    if (!isset($groups[$studentGroup])) {
                        $groups[$studentGroup] = [];
                    }
                    $studentId = $student['student_id'] ?? $student['id'] ?? null;
                    if ($studentId) {
                        $studentIds[] = (string)$studentId;
                        $groups[$studentGroup][] = (string)$studentId;
                    }
                }
            }
        }
        
        // Получаем результаты теста
        $testId = $testId ?? 0;
        $allResults = $this->getResultsByTestId($testId);
        
        // Распределяем по категориям
        $categories = [
            'Sust' => 0,        // Melancholic
            'O\'rtacha' => 0,   // Phlegmatic
            'Yuqori' => 0,      // Sanguine
            'Juda yuqori' => 0, // Choleric
        ];
        
        $totalStudents = count($studentIds);
        $studentsWithResults = [];
        
        foreach ($allResults as $result) {
            $resultStudentId = (string)($result['student_id'] ?? '');
            if (in_array($resultStudentId, $studentIds, true)) {
                if (!in_array($resultStudentId, $studentsWithResults, true)) {
                    $studentsWithResults[] = $resultStudentId;
                }
                
                $tempType = $result['temperament']['type'] ?? null;
                if ($tempType === 'Melancholic') {
                    $categories['Sust']++;
                } elseif ($tempType === 'Phlegmatic') {
                    $categories['O\'rtacha']++;
                } elseif ($tempType === 'Sanguine') {
                    $categories['Yuqori']++;
                } elseif ($tempType === 'Choleric') {
                    $categories['Juda yuqori']++;
                }
            }
        }
        
        // Рассчитываем проценты
        $totalWithResults = count($studentsWithResults);
        $percentages = [];
        foreach ($categories as $key => $count) {
            $percentages[$key] = $totalWithResults > 0 ? round(($count / $totalWithResults) * 100, 1) : 0;
        }
        
        return [
            'faculty' => $faculty,
            'total_students' => $totalStudents,
            'students_with_results' => $totalWithResults,
            'categories' => $categories,
            'percentages' => $percentages,
            'groups' => array_keys($groups),
        ];
    }
    
    /**
     * Получить статистику по группе
     * @return array<string, mixed>
     */
    public function getGroupStatistics(string $group, ?int $testId = null): array
    {
        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();
        
        // Нормализация для сравнения
        $normalize = function($str) {
            if ($str === null || $str === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$str));
            $apostrophes = [
                "\xCA\xBC", "\xE2\x80\x99", "\xE2\x80\x98",
                "\xE2\x80\x9C", "\xE2\x80\x9D", '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };
        
        $groupNormalized = $normalize($group);
        
        // Получаем студентов группы
        $studentIds = [];
        $studentsData = [];
        foreach ($allStudents as $student) {
            $studentGroup = $student['group'] ?? null;
            if ($studentGroup && $normalize($studentGroup) === $groupNormalized) {
                $studentId = $student['student_id'] ?? $student['id'] ?? null;
                if ($studentId) {
                    $studentIdStr = (string)$studentId;
                    $studentIds[] = $studentIdStr;
                    $studentsData[$studentIdStr] = $student;
                }
            }
        }
        
        // Получаем результаты теста
        $testId = $testId ?? 0;
        $allResults = $this->getResultsByTestId($testId);
        
        // Распределяем по категориям
        $categories = [
            'Sust' => 0,        // Melancholic
            'O\'rtacha' => 0,   // Phlegmatic
            'Yuqori' => 0,      // Sanguine
            'Juda yuqori' => 0, // Choleric
        ];
        
        $totalStudents = count($studentIds);
        $studentsWithResults = [];
        $studentsResults = [];
        
        foreach ($allResults as $result) {
            $resultStudentId = (string)($result['student_id'] ?? '');
            if (in_array($resultStudentId, $studentIds, true)) {
                if (!in_array($resultStudentId, $studentsWithResults, true)) {
                    $studentsWithResults[] = $resultStudentId;
                }
                
                $tempType = $result['temperament']['type'] ?? null;
                if ($tempType === 'Melancholic') {
                    $categories['Sust']++;
                } elseif ($tempType === 'Phlegmatic') {
                    $categories['O\'rtacha']++;
                } elseif ($tempType === 'Sanguine') {
                    $categories['Yuqori']++;
                } elseif ($tempType === 'Choleric') {
                    $categories['Juda yuqori']++;
                }
                
                // Сохраняем результат для студента
                $studentsResults[$resultStudentId] = $result;
            }
        }
        
        // Рассчитываем проценты
        $totalWithResults = count($studentsWithResults);
        $percentages = [];
        foreach ($categories as $key => $count) {
            $percentages[$key] = $totalWithResults > 0 ? round(($count / $totalWithResults) * 100, 1) : 0;
        }
        
        // Формируем список студентов с результатами
        $studentsList = [];
        foreach ($studentIds as $studentId) {
            $student = $studentsData[$studentId] ?? null;
            if ($student) {
                $result = $studentsResults[$studentId] ?? null;
                $studentsList[] = [
                    'student_id' => $studentId,
                    'name' => $student['name'] ?? $student['full_name'] ?? 'Noma\'lum',
                    'group' => $student['group'] ?? $group,
                    'faculty' => $student['faculty'] ?? 'Noma\'lum',
                    'result' => $result ? [
                        'E' => $result['scores']['E'] ?? $result['E'] ?? null,
                        'N' => $result['scores']['N'] ?? $result['N'] ?? null,
                        'L' => $result['scores']['L'] ?? $result['L'] ?? null,
                        'temperament' => $result['temperament']['type'] ?? null,
                        'completed_at' => $result['completed_at'] ?? null,
                    ] : null,
                ];
            }
        }
        
        return [
            'group' => $group,
            'total_students' => $totalStudents,
            'students_with_results' => $totalWithResults,
            'categories' => $categories,
            'percentages' => $percentages,
            'students' => $studentsList,
        ];
    }

    /**
     * Фильтровать результаты по разрешенным группам и факультетам
     * @param array<int, array<string, mixed>> $results Результаты для фильтрации
     * @param array<string> $allowedGroups Разрешенные группы
     * @param array<string> $allowedFaculties Разрешенные факультеты
     * @return array<int, array<string, mixed>> Отфильтрованные результаты
     */
    public function filterResultsByAllowedGroups(array $results, array $allowedGroups = [], array $allowedFaculties = []): array
    {
        // Если группы и факультеты не указаны, возвращаем все результаты
        if (empty($allowedGroups) && empty($allowedFaculties)) {
            return $results;
        }

        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();

        // Функция нормализации для сравнения
        $normalize = function($str) {
            if ($str === null || $str === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$str));
            $apostrophes = [
                "\xCA\xBC", "\xE2\x80\x99", "\xE2\x80\x98",
                "\xE2\x80\x9C", "\xE2\x80\x9D", '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };

        // Получаем ID студентов из разрешенных групп/факультетов
        $allowedStudentIds = [];
        
        if (!empty($allowedGroups)) {
            $normalizedAllowedGroups = array_map($normalize, $allowedGroups);
            foreach ($allStudents as $student) {
                $studentGroup = $student['group'] ?? null;
                if ($studentGroup) {
                    $normalizedStudentGroup = $normalize($studentGroup);
                    if (in_array($normalizedStudentGroup, $normalizedAllowedGroups, true)) {
                        $studentId = $student['student_id'] ?? $student['id'] ?? null;
                        if ($studentId) {
                            $allowedStudentIds[(string)$studentId] = true;
                        }
                    }
                }
            }
        } elseif (!empty($allowedFaculties)) {
            $normalizedAllowedFaculties = array_map($normalize, $allowedFaculties);
            foreach ($allStudents as $student) {
                $studentFaculty = $student['faculty'] ?? null;
                if ($studentFaculty) {
                    $normalizedStudentFaculty = $normalize($studentFaculty);
                    if (in_array($normalizedStudentFaculty, $normalizedAllowedFaculties, true)) {
                        $studentId = $student['student_id'] ?? $student['id'] ?? null;
                        if ($studentId) {
                            $allowedStudentIds[(string)$studentId] = true;
                        }
                    }
                }
            }
        }

        // Фильтруем результаты
        $filtered = [];
        foreach ($results as $result) {
            $resultStudentId = (string)($result['student_id'] ?? '');
            if (isset($allowedStudentIds[$resultStudentId])) {
                $filtered[] = $result;
            }
        }

        return $filtered;
    }

    /**
     * Очистить все результаты тестов
     * @return bool Успешно ли выполнена операция
     */
    public function clearAllResults(): bool
    {
        try {
            // Очищаем файл с результатами обычных тестов
            if (file_exists($this->customResultsFile)) {
                file_put_contents($this->customResultsFile, '');
            }
            
            // Очищаем файл с результатами теста Айзенка
            if (file_exists($this->eysenckResultsFile)) {
                file_put_contents($this->eysenckResultsFile, '');
            }
            
            // Очищаем файл с результатами IQ теста
            if (file_exists($this->iqResultsFile)) {
                file_put_contents($this->iqResultsFile, '');
            }
            
            // Очищаем файл с результатами теста Люшера
            if (file_exists($this->lusherResultsFile)) {
                file_put_contents($this->lusherResultsFile, '');
            }
            
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Очистить результаты только студентов (teacher_* сохраняем)
     */
    public function clearStudentResults(): bool
    {
        try {
            $this->filterResultsFile($this->customResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return strpos($id, self::TEACHER_ID_PREFIX) === 0;
            });

            $this->filterResultsFile($this->eysenckResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return strpos($id, self::TEACHER_ID_PREFIX) === 0;
            });

            $this->filterResultsFile($this->iqResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return strpos($id, self::TEACHER_ID_PREFIX) === 0;
            });

            $this->filterResultsFile($this->lusherResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return strpos($id, self::TEACHER_ID_PREFIX) === 0;
            });

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * Очистить результаты только преподавателей (student сохраняем)
     */
    public function clearTeacherResults(): bool
    {
        try {
            $this->filterResultsFile($this->customResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return $id !== '' && strpos($id, self::TEACHER_ID_PREFIX) !== 0;
            });

            $this->filterResultsFile($this->eysenckResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return $id !== '' && strpos($id, self::TEACHER_ID_PREFIX) !== 0;
            });

            $this->filterResultsFile($this->iqResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return $id !== '' && strpos($id, self::TEACHER_ID_PREFIX) !== 0;
            });

            $this->filterResultsFile($this->lusherResultsFile, function (array $row): bool {
                $id = (string)($row['student_id'] ?? '');
                return $id !== '' && strpos($id, self::TEACHER_ID_PREFIX) !== 0;
            });

            return true;
        } catch (\Throwable $e) {
            return false;
        }
    }

    /**
     * @param callable(array<string,mixed>):bool $keepRow
     */
    private function filterResultsFile(string $filePath, callable $keepRow): void
    {
        if (!file_exists($filePath)) {
            return;
        }

        $lines = file($filePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        if ($lines === false || $lines === []) {
            file_put_contents($filePath, '');
            return;
        }

        $kept = [];
        foreach ($lines as $line) {
            $line = trim((string)$line);
            if ($line === '') {
                continue;
            }

            $decoded = json_decode($line, true);
            if (!is_array($decoded)) {
                continue;
            }

            if ($keepRow($decoded)) {
                $kept[] = json_encode($decoded, JSON_UNESCAPED_UNICODE);
            }
        }

        file_put_contents($filePath, $kept !== [] ? implode("\n", $kept) . "\n" : '');
    }
}

