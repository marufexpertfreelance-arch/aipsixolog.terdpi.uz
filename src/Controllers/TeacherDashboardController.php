<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Services\TestStorage;
use App\Services\ResultsStorage;

class TeacherDashboardController
{
    private TestStorage $testStorage;
    private ResultsStorage $resultsStorage;

    public function __construct()
    {
        $this->testStorage = new TestStorage();
        $this->resultsStorage = new ResultsStorage();
    }

    public function index(): void
    {
        // Проверяем авторизацию преподавателя
        if (empty($_SESSION['teacher_user'])) {
            $_SESSION['redirect_after_login'] = '/teacher/dashboard';
            header('Location: /teachers/login');
            exit;
        }

        $teacher = $_SESSION['teacher_user'];
        $teacherId = 'teacher_' . ($teacher['id'] ?? 'unknown');
        
        // 1. Получаем результаты встроенных тестов (их преподаватель МОЖЕТ видеть)
        $eysenckResult = $this->getEysenckResult($teacherId);
        $iqResult = $this->getIqResult($teacherId);
        $lusherResult = $this->getLusherResult($teacherId);
        
        // 2. Получаем custom тесты, назначенные этому преподавателю
        $allTests = $this->testStorage->getAll();
        $assignedTests = [];
        
        foreach ($allTests as $test) {
            $allowedTeachers = $test['allowed_teachers'] ?? [];
            if (in_array($teacher['id'], $allowedTeachers)) {
                $assignedTests[] = $test;
            }
        }
        
        // 3. Проверяем, какие custom тесты уже пройдены (НО результаты НЕ показываем!)
        $customResults = $this->resultsStorage->getResultsByStudentId($teacherId);
        $completedCustomTestIds = [];
        
        foreach ($customResults as $result) {
            if (($result['test_type'] ?? '') === 'custom') {
                $testId = (int)($result['test_id'] ?? 0);
                if ($testId > 0) {
                    $completedCustomTestIds[] = $testId;
                }
            }
        }
        
        $error = $_SESSION['teacher_error'] ?? null;
        unset($_SESSION['teacher_error']);
        
        echo View::render('teachers/dashboard', [
            'title' => 'O\'qituvchi kabineti',
            'teacher' => $teacher,
            'eysenckResult' => $eysenckResult,
            'iqResult' => $iqResult,
            'lusherResult' => $lusherResult,
            'assignedTests' => $assignedTests,
            'completedCustomTestIds' => $completedCustomTestIds,
            'error' => $error,
        ]);
    }

    /** Получить результат теста Айзенка */
    private function getEysenckResult(string $teacherId): ?array
    {
        $resultsPath = dirname(__DIR__, 2) . '/storage/eysenck-results.jsonl';
        if (!file_exists($resultsPath)) {
            return null;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $teacherId)) {
                return $result;
            }
        }

        return null;
    }

    /** Получить результат IQ теста */
    private function getIqResult(string $teacherId): ?array
    {
        $resultsPath = dirname(__DIR__, 2) . '/storage/iq-results.jsonl';
        if (!file_exists($resultsPath)) {
            return null;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $teacherId)) {
                return $result;
            }
        }

        return null;
    }

    /** Получить результат теста Люшера */
    private function getLusherResult(string $teacherId): ?array
    {
        $resultsPath = dirname(__DIR__, 2) . '/storage/lusher-results.jsonl';
        if (!file_exists($resultsPath)) {
            return null;
        }

        $lines = file($resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach ($lines as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $teacherId)) {
                return $result;
            }
        }

        return null;
    }
}

