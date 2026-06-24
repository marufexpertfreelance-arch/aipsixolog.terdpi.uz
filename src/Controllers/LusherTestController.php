<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;

class LusherTestController
{
    private string $testDataPath;
    private string $resultsPath;

    /**
     * @return array{user_id: string, user_name: string, user_type: string}
     */
    private function getCurrentUser(): array
    {
        if (!empty($_SESSION['teacher_user'])) {
            $teacher = $_SESSION['teacher_user'];
            $id = 'teacher_' . (string)($teacher['id'] ?? '');
            return [
                'user_id' => $id,
                'user_name' => (string)($teacher['full_name'] ?? 'O\'qituvchi'),
                'user_type' => 'teacher',
            ];
        }

        if (!empty($_SESSION['hemis_user'])) {
            $u = $_SESSION['hemis_user'];
            $id = (string)($u['login'] ?? ($u['student_id'] ?? ''));
            return [
                'user_id' => $id,
                'user_name' => (string)($u['name'] ?? 'Talaba'),
                'user_type' => 'student',
            ];
        }

        return [
            'user_id' => '',
            'user_name' => '',
            'user_type' => 'guest',
        ];
    }

    public function __construct()
    {
        // Определяем путь к файлу данных
        $root = dirname(__DIR__, 2);
        $this->testDataPath = $root . '/data/lusher-test.json';
        $this->resultsPath = $root . '/storage/lusher-results.jsonl';
        
        // Создаем директории если их нет
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
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            $_SESSION['redirect_after_login'] = '/lusher/start';
            header('Location: /hemis/login');
            exit;
        }

        // Очищаем предыдущие выборы, если студент хочет пройти тест заново
        unset($_SESSION['lusher_round1']);
        unset($_SESSION['lusher_round2']);

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        echo View::render('lusher/start', [
            'title' => 'Lyusher Testi',
            'test' => $testData,
        ]);
    }

    /** Показываем первый раунд выбора (предпочтения) */
    public function selectionRound1(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        // Восстанавливаем выбор из сессии
        $selection = $_SESSION['lusher_round1'] ?? [];

        echo View::render('lusher/selection-round1', [
            'title' => 'Birinchi bosqich',
            'test' => $testData,
            'selection' => $selection,
        ]);
    }

    /** Показываем второй раунд выбора (отвергаемые) */
    public function selectionRound2(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        // Проверяем, что первый раунд пройден
        if (empty($_SESSION['lusher_round1']) || count($_SESSION['lusher_round1']) !== 8) {
            header('Location: /lusher/round1');
            exit;
        }

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        // Восстанавливаем выбор из сессии
        $selection = $_SESSION['lusher_round2'] ?? [];

        echo View::render('lusher/selection-round2', [
            'title' => 'Ikkinchi bosqich',
            'test' => $testData,
            'round1_selection' => $_SESSION['lusher_round1'],
            'selection' => $selection,
        ]);
    }

    /** Обрабатываем выбор цветов */
    public function processSelection(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        $round = isset($_POST['round']) ? (int)$_POST['round'] : 0;
        $selection = isset($_POST['selection']) ? json_decode($_POST['selection'], true) : [];

        if (!is_array($selection) || count($selection) !== 8) {
            $_SESSION['lusher_error'] = 'Barcha 8 ta rangni tanlashingiz kerak.';
            header('Location: /lusher/round' . $round);
            exit;
        }

        // Проверяем, что все индексы уникальны и в диапазоне 0-7
        if (count(array_unique($selection)) !== 8) {
            $_SESSION['lusher_error'] = 'Har bir rangni faqat bir marta tanlashingiz kerak.';
            header('Location: /lusher/round' . $round);
            exit;
        }

        foreach ($selection as $colorId) {
            if (!is_numeric($colorId) || $colorId < 0 || $colorId > 7) {
                $_SESSION['lusher_error'] = 'Noto\'g\'ri rang tanlandi.';
                header('Location: /lusher/round' . $round);
                exit;
            }
        }

        // Сохраняем выбор в сессию
        if ($round === 1) {
            $_SESSION['lusher_round1'] = array_map('intval', $selection);
            header('Location: /lusher/round2');
        } elseif ($round === 2) {
            $_SESSION['lusher_round2'] = array_map('intval', $selection);
            header('Location: /lusher/complete');
        } else {
            header('Location: /lusher/start');
        }
        exit;
    }

    /** Завершаем тест и показываем результаты */
    public function complete(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        // Проверяем, что оба раунда пройдены
        $round1 = $_SESSION['lusher_round1'] ?? [];
        $round2 = $_SESSION['lusher_round2'] ?? [];

        if (count($round1) !== 8 || count($round2) !== 8) {
            header('Location: /lusher/start');
            exit;
        }

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        // Подсчитываем результаты
        $results = $this->calculateResults($testData, $round1, $round2);
        
        // Сохраняем результаты
        $this->saveResults($results);

        // Очищаем выборы из сессии
        unset($_SESSION['lusher_round1']);
        unset($_SESSION['lusher_round2']);

        echo View::render('lusher/results', [
            'title' => 'Test natijalari',
            'results' => $results,
            'test' => $testData,
        ]);
    }

    /** Показываем сохраненные результаты */
    public function results(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        $results = $this->loadResults();
        if (!$results) {
            header('Location: /lusher/start');
            exit;
        }

        $testData = $this->loadTestData();

        echo View::render('lusher/results', [
            'title' => 'Test natijalari',
            'results' => $results,
            'test' => $testData,
        ]);
    }

    /** Загружаем данные теста */
    private function loadTestData(): ?array
    {
        if (!file_exists($this->testDataPath)) {
            return null;
        }

        $content = file_get_contents($this->testDataPath);
        $data = json_decode($content, true);

        return $data ?: null;
    }

    /** Подсчитываем результаты */
    private function calculateResults(array $testData, array $round1, array $round2): array
    {
        $currentUser = $this->getCurrentUser();

        $colors = $testData['colors'] ?? [];
        $interpretations = $testData['color_interpretations'] ?? [];
        $positionInterps = $testData['position_interpretations'] ?? [];

        // Анализируем первый раунд
        $preferred = [];
        $neutral = [];
        $rejected = [];

        foreach ($round1 as $position => $colorId) {
            $positionNum = $position + 1;
            $color = $colors[$colorId] ?? null;
            
            if ($color) {
                $colorInterp = $interpretations[(string)$colorId] ?? [];
                $posInterp = $positionInterps[(string)$positionNum] ?? [];
                
                if ($positionNum <= 2) {
                    // Предпочитаемые (1-2 позиция)
                    $preferred[] = [
                        'color' => $color,
                        'position' => $positionNum,
                        'position_name' => $posInterp['name'] ?? '',
                        'position_desc' => $posInterp['description'] ?? '',
                        'interpretation' => $colorInterp['preferred'] ?? '',
                    ];
                } elseif ($positionNum <= 4) {
                    // Нейтральные (3-4 позиция)
                    $neutral[] = [
                        'color' => $color,
                        'position' => $positionNum,
                        'position_name' => $posInterp['name'] ?? '',
                        'position_desc' => $posInterp['description'] ?? '',
                        'interpretation' => $colorInterp['neutral'] ?? '',
                    ];
                } else {
                    // Отвергаемые (5-8 позиция, особенно 7-8)
                    $rejected[] = [
                        'color' => $color,
                        'position' => $positionNum,
                        'position_name' => $posInterp['name'] ?? '',
                        'position_desc' => $posInterp['description'] ?? '',
                        'interpretation' => $colorInterp['rejected'] ?? '',
                        'is_stress' => $positionNum >= 7,
                    ];
                }
            }
        }

        // Анализируем второй раунд для сравнения
        $round2Analysis = [];
        foreach ($round2 as $position => $colorId) {
            $color = $colors[$colorId] ?? null;
            if ($color) {
                $round2Analysis[] = [
                    'color' => $color,
                    'position' => $position + 1,
                ];
            }
        }

        // Определяем стресс-факторы (отвергаемые в обоих раундах)
        $stressFactors = [];
        foreach ($rejected as $rej) {
            if ($rej['is_stress']) {
                $stressFactors[] = $rej;
            }
        }

        // Формируем характеристики
        $characteristics = [];
        foreach ($preferred as $pref) {
            $characteristics[] = [
                'type' => 'preferred',
                'color' => $pref['color'],
                'description' => $pref['interpretation'],
            ];
        }

        return [
            'round1_selection' => $round1,
            'round2_selection' => $round2,
            'preferred' => $preferred,
            'neutral' => $neutral,
            'rejected' => $rejected,
            'round2_analysis' => $round2Analysis,
            'stress_factors' => $stressFactors,
            'characteristics' => $characteristics,
            'student_id' => $currentUser['user_id'] !== '' ? $currentUser['user_id'] : null,
            'student_name' => $currentUser['user_name'] !== '' ? $currentUser['user_name'] : null,
            'completed_at' => date('Y-m-d H:i:s'),
        ];
    }

    /** Сохраняем результаты */
    private function saveResults(array $results): void
    {
        $record = [
            'test_type' => 'lusher',
            'student_id' => $results['student_id'],
            'student_name' => $results['student_name'],
            'round1_selection' => $results['round1_selection'],
            'round2_selection' => $results['round2_selection'],
            'interpretation' => [
                'preferred' => $results['preferred'],
                'neutral' => $results['neutral'],
                'rejected' => $results['rejected'],
                'characteristics' => $results['characteristics'],
                'stress_factors' => $results['stress_factors'],
            ],
            'completed_at' => $results['completed_at'],
        ];

        $line = json_encode($record, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($this->resultsPath, $line, FILE_APPEND);
        
        // Также сохраняем в сессию для быстрого доступа
        $_SESSION['lusher_last_results'] = $results;
    }

    /** Загружаем результаты студента */
    private function loadResults(): ?array
    {
        // Сначала проверяем сессию
        if (isset($_SESSION['lusher_last_results'])) {
            return $_SESSION['lusher_last_results'];
        }

        // Затем проверяем файл
        if (!file_exists($this->resultsPath)) {
            return null;
        }

        $currentUser = $this->getCurrentUser();
        $studentId = $currentUser['user_id'] !== '' ? $currentUser['user_id'] : null;
        if (!$studentId) {
            return null;
        }

        $lines = file($this->resultsPath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach (array_reverse($lines) as $line) {
            $result = json_decode($line, true);
            if ($result && isset($result['student_id']) && $result['student_id'] === $studentId) {
                // Восстанавливаем структуру результатов
                return [
                    'round1_selection' => $result['round1_selection'] ?? [],
                    'round2_selection' => $result['round2_selection'] ?? [],
                    'preferred' => $result['interpretation']['preferred'] ?? [],
                    'neutral' => $result['interpretation']['neutral'] ?? [],
                    'rejected' => $result['interpretation']['rejected'] ?? [],
                    'characteristics' => $result['interpretation']['characteristics'] ?? [],
                    'stress_factors' => $result['interpretation']['stress_factors'] ?? [],
                    'student_id' => $result['student_id'],
                    'student_name' => $result['student_name'],
                    'completed_at' => $result['completed_at'],
                ];
            }
        }

        return null;
    }
}

