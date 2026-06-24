<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;

class RanglarTestController
{
    private string $testDataPath;
    private string $resultsPath;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $this->testDataPath = $root . '/data/ranglar-test.json';
        $this->resultsPath = $root . '/storage/ranglar-results.jsonl';
        
        $dataDir = dirname($this->testDataPath);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
        
        $storageDir = dirname($this->resultsPath);
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }
    }

    /** Показываем форму начала теста */
    public function start(): void
    {
        if (empty($_SESSION['hemis_user'])) {
            $_SESSION['redirect_after_login'] = '/ranglar/start';
            header('Location: /hemis/login');
            exit;
        }

        unset($_SESSION['ranglar_preferred']);
        unset($_SESSION['ranglar_rejected']);

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        echo View::render('ranglar/start', [
            'title' => 'Ranglar Metodikasi',
            'test' => $testData,
        ]);
    }

    /** Обработка выбора цветов */
    public function select(): void
    {
        if (empty($_SESSION['hemis_user'])) {
            header('Location: /hemis/login');
            exit;
        }

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $preferred = $_POST['preferred'] ?? null;
            $rejected = $_POST['rejected'] ?? [];
            
            if ($preferred !== null && $preferred !== '') {
                $_SESSION['ranglar_preferred'] = (int)$preferred;
            }
            
            if (is_array($rejected)) {
                $_SESSION['ranglar_rejected'] = array_map('intval', array_filter($rejected));
            } else {
                $_SESSION['ranglar_rejected'] = [];
            }
            
            header('Location: /ranglar/complete');
            exit;
        }

        echo View::render('ranglar/selection', [
            'title' => 'Ranglar Metodikasi - Rang tanlash',
            'test' => $testData,
        ]);
    }

    /** Завершение теста и расчет результатов */
    public function complete(): void
    {
        if (empty($_SESSION['hemis_user'])) {
            header('Location: /hemis/login');
            exit;
        }

        $preferred = $_SESSION['ranglar_preferred'] ?? null;
        $rejected = $_SESSION['ranglar_rejected'] ?? [];

        if ($preferred === null) {
            header('Location: /ranglar/start');
            exit;
        }

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        $results = $this->calculateResults($testData, $preferred, $rejected);
        $this->saveResults($results);

        header('Location: /ranglar/results');
        exit;
    }

    /** Показываем результаты */
    public function results(): void
    {
        if (empty($_SESSION['hemis_user'])) {
            header('Location: /hemis/login');
            exit;
        }

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        $preferred = $_SESSION['ranglar_preferred'] ?? null;
        $rejected = $_SESSION['ranglar_rejected'] ?? [];

        if ($preferred === null) {
            header('Location: /ranglar/start');
            exit;
        }

        $results = $this->calculateResults($testData, $preferred, $rejected);

        echo View::render('ranglar/results', [
            'title' => 'Ranglar Metodikasi - Natijalar',
            'test' => $testData,
            'results' => $results,
        ]);
    }

    /** Загружаем данные теста */
    private function loadTestData(): ?array
    {
        if (!file_exists($this->testDataPath)) {
            return null;
        }

        $json = file_get_contents($this->testDataPath);
        $data = json_decode($json, true);

        return $data ?: null;
    }

    /** Рассчитываем результаты */
    private function calculateResults(array $testData, int $preferred, array $rejected): array
    {
        $colors = $testData['colors'] ?? [];
        $interpretations = $testData['interpretations'] ?? [];

        $preferredColor = null;
        $preferredInterpretation = null;
        $rejectedColors = [];
        $rejectedInterpretations = [];

        foreach ($colors as $color) {
            if ($color['id'] === $preferred) {
                $preferredColor = $color;
                $preferredInterpretation = $interpretations['preferred'][(string)$preferred] ?? '';
                break;
            }
        }

        foreach ($colors as $color) {
            if (in_array($color['id'], $rejected, true)) {
                $rejectedColors[] = $color;
                $rejectedInterpretations[] = [
                    'color' => $color,
                    'interpretation' => $interpretations['rejected'][(string)$color['id']] ?? '',
                ];
            }
        }

        return [
            'preferred_color' => $preferredColor,
            'preferred_interpretation' => $preferredInterpretation,
            'rejected_colors' => $rejectedColors,
            'rejected_interpretations' => $rejectedInterpretations,
            'student_id' => $_SESSION['hemis_user']['login'] ?? $_SESSION['hemis_user']['student_id'] ?? null,
            'student_name' => $_SESSION['hemis_user']['name'] ?? null,
            'completed_at' => date('Y-m-d H:i:s'),
        ];
    }

    /** Сохраняем результаты */
    private function saveResults(array $results): void
    {
        $line = json_encode($results, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($this->resultsPath, $line, FILE_APPEND);
        
        $_SESSION['ranglar_last_results'] = $results;
    }
}

