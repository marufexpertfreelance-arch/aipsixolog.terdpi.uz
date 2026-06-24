<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;

class EysenckTestController
{
    private string $testDataPath;

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
        $this->testDataPath = $root . '/data/eysenck-test.json';
        
        // Создаем директорию data если её нет
        $dataDir = dirname($this->testDataPath);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
    }

    /** Показываем форму начала теста */
    public function start(): void
    {
        // Проверяем авторизацию (студент или преподаватель)
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            $_SESSION['redirect_after_login'] = '/eysenck/start';
            header('Location: /hemis/login');
            exit;
        }

        // Очищаем предыдущие ответы, если студент хочет пройти тест заново
        unset($_SESSION['eysenck_answers']);

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        echo View::render('eysenck/start', [
            'title' => 'Temperament',
            'test' => $testData,
        ]);
    }

    /** Показываем вопрос теста */
    public function question(): void
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

        $currentQuestion = (int) ($_GET['q'] ?? 1);
        $totalQuestions = count($testData['questions']);

        if ($currentQuestion < 1 || $currentQuestion > $totalQuestions) {
            header('Location: /eysenck/start');
            exit;
        }

        // Восстанавливаем ответы из сессии
        $answers = $_SESSION['eysenck_answers'] ?? [];

        echo View::render('eysenck/question', [
            'title' => 'Savol ' . $currentQuestion . ' / ' . $totalQuestions,
            'test' => $testData,
            'question' => $testData['questions'][$currentQuestion - 1],
            'currentQuestion' => $currentQuestion,
            'totalQuestions' => $totalQuestions,
            'currentAnswer' => $answers[$currentQuestion] ?? null,
        ]);
    }

    /** Обрабатываем ответ на вопрос */
    public function answer(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        $questionId = (int) ($_POST['question_id'] ?? 0);
        $answer = $_POST['answer'] ?? null;

        if (!$questionId || !$answer) {
            header('Location: /eysenck/start');
            exit;
        }

        // Сохраняем ответ в сессию
        if (!isset($_SESSION['eysenck_answers'])) {
            $_SESSION['eysenck_answers'] = [];
        }
        $_SESSION['eysenck_answers'][$questionId] = $answer === 'yes' ? 1 : 0;

        $testData = $this->loadTestData();
        $totalQuestions = count($testData['questions']);

        // Переходим к следующему вопросу
        if ($questionId < $totalQuestions) {
            header('Location: /eysenck/question?q=' . ($questionId + 1));
        } else {
            header('Location: /eysenck/complete');
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

        $testData = $this->loadTestData();
        $answers = $_SESSION['eysenck_answers'] ?? [];

        if (count($answers) < count($testData['questions'])) {
            header('Location: /eysenck/start');
            exit;
        }

        // Подсчитываем результаты
        $results = $this->calculateResults($testData, $answers);
        
        // Сохраняем результаты
        $this->saveResults($results);

        // Очищаем ответы из сессии
        unset($_SESSION['eysenck_answers']);

        echo View::render('eysenck/results', [
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
            header('Location: /eysenck/start');
            exit;
        }

        $testData = $this->loadTestData();

        echo View::render('eysenck/results', [
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
    private function calculateResults(array $testData, array $answers): array
    {
        $currentUser = $this->getCurrentUser();

        $scores = [
            'E' => 0, // Экстраверсия
            'N' => 0, // Нейротизм
            'L' => 0, // Ложь
        ];

        foreach ($testData['questions'] as $question) {
            $questionId = $question['id'];
            $dimension = $question['dimension'];
            $answer = $answers[$questionId] ?? 0;

            if (isset($scores[$dimension])) {
                $scores[$dimension] += $answer;
            }
        }

        // Определяем темперамент
        $temperament = $this->determineTemperament($scores);

        return [
            'scores' => $scores,
            'temperament' => $temperament,
            'student_id' => $currentUser['user_id'] !== '' ? $currentUser['user_id'] : null,
            'student_name' => $currentUser['user_name'] !== '' ? $currentUser['user_name'] : null,
            'completed_at' => date('Y-m-d H:i:s'),
        ];
    }

    /** Определяем темперамент */
    private function determineTemperament(array $scores): array
    {
        $E = $scores['E'];
        $N = $scores['N'];
        $L = $scores['L'];

        // Проверяем валидность (L не должен быть больше 5)
        $isValid = $L <= 5;

        // Средние значения для классификации
        $E_avg = 12;
        $N_avg = 12;

        $E_type = $E >= $E_avg ? 'high' : 'low';
        $N_type = $N >= $N_avg ? 'high' : 'low';

        // Определяем темперамент
        $temperament = null;
        $description = '';

        if ($E_type === 'high' && $N_type === 'high') {
            $temperament = 'Choleric';
            $description = 'Xolerik - Faol, qiziqarli, g\'ayratli, lekin tez-tez g\'azablanishi mumkin.';
        } elseif ($E_type === 'high' && $N_type === 'low') {
            $temperament = 'Sanguine';
            $description = 'Sangvinik - Qiziqarli, optimistik, ijtimoiy, lekin ba\'zan betartib.';
        } elseif ($E_type === 'low' && $N_type === 'low') {
            $temperament = 'Phlegmatic';
            $description = 'Flegmatik - Xotirjam, tinch, barqaror, lekin ba\'zan passiv.';
        } elseif ($E_type === 'low' && $N_type === 'high') {
            $temperament = 'Melancholic';
            $description = 'Melanxolik - Chuqur, o\'ylovchi, sezgir, lekin ba\'zan qayg\'uli.';
        }

        return [
            'type' => $temperament,
            'description' => $description,
            'is_valid' => $isValid,
            'E_score' => $E,
            'N_score' => $N,
            'L_score' => $L,
        ];
    }

    /** Сохраняем результаты */
    private function saveResults(array $results): void
    {
        $storagePath = dirname(__DIR__, 2) . '/storage/eysenck-results.jsonl';
        $line = json_encode($results, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($storagePath, $line, FILE_APPEND);
        
        // Также сохраняем в сессию для быстрого доступа
        $_SESSION['eysenck_last_results'] = $results;
    }

    /** Загружаем результаты студента */
    private function loadResults(): ?array
    {
        // Сначала проверяем сессию
        if (isset($_SESSION['eysenck_last_results'])) {
            return $_SESSION['eysenck_last_results'];
        }

        // Затем проверяем файл
        $storagePath = dirname(__DIR__, 2) . '/storage/eysenck-results.jsonl';
        if (!file_exists($storagePath)) {
            return null;
        }

        $currentUser = $this->getCurrentUser();
        $studentId = $currentUser['user_id'] !== '' ? $currentUser['user_id'] : null;
        if (!$studentId) {
            return null;
        }

        $lines = file($storagePath, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
        foreach (array_reverse($lines) as $line) {
            $result = json_decode($line, true);
            if ($result && ($result['student_id'] === $studentId)) {
                return $result;
            }
        }

        return null;
    }

    /** Проверяем, проходил ли студент тест */
    private function hasCompletedTest(): bool
    {
        return $this->loadResults() !== null;
    }
}

