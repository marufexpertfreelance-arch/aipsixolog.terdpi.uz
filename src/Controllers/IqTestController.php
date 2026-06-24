<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;

class IqTestController
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
        $this->testDataPath = $root . '/data/iq-test.json';
        
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
            $_SESSION['redirect_after_login'] = '/iq/start';
            header('Location: /hemis/login');
            exit;
        }

        // Очищаем предыдущие ответы, если студент хочет пройти тест заново
        unset($_SESSION['iq_answers']);

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        echo View::render('iq/start', [
            'title' => 'IQ Test',
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
            header('Location: /iq/start');
            exit;
        }

        // Восстанавливаем ответы из сессии
        $answers = $_SESSION['iq_answers'] ?? [];

        echo View::render('iq/question', [
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
        $answer = (int) ($_POST['answer'] ?? -1);

        if (!$questionId || $answer < 0) {
            header('Location: /iq/start');
            exit;
        }

        // Сохраняем ответ в сессию
        if (!isset($_SESSION['iq_answers'])) {
            $_SESSION['iq_answers'] = [];
        }
        $_SESSION['iq_answers'][$questionId] = $answer;

        $testData = $this->loadTestData();
        $totalQuestions = count($testData['questions']);

        // Переходим к следующему вопросу
        if ($questionId < $totalQuestions) {
            header('Location: /iq/question?q=' . ($questionId + 1));
        } else {
            header('Location: /iq/complete');
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
        $answers = $_SESSION['iq_answers'] ?? [];

        if (count($answers) < count($testData['questions'])) {
            header('Location: /iq/start');
            exit;
        }

        // Подсчитываем результаты
        $results = $this->calculateResults($testData, $answers);
        
        // Сохраняем результаты вместе с ответами студентов
        $results['answers'] = $answers; // Добавляем ответы студентов к результатам
        $this->saveResults($results);

        // Очищаем ответы из сессии
        unset($_SESSION['iq_answers']);

        echo View::render('iq/results', [
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
            header('Location: /iq/start');
            exit;
        }

        $testData = $this->loadTestData();

        echo View::render('iq/results', [
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

        $correctAnswers = 0;
        $totalQuestions = count($testData['questions']);
        
        // Подсчитываем правильные ответы
        foreach ($testData['questions'] as $question) {
            $questionId = $question['id'];
            $userAnswer = $answers[$questionId] ?? -1;
            $correctAnswer = $question['correct_answer'] ?? -1;
            
            if ($userAnswer === $correctAnswer) {
                $correctAnswers++;
            }
        }
        
        // Рассчитываем процент правильных ответов
        $percentage = ($correctAnswers / $totalQuestions) * 100;
        
        // Профессиональный расчет IQ с использованием нормального распределения
        // Конвертируем процент в процентиль (0-100% → 0-1)
        $percentile = $percentage / 100;
        
        // Используем обратную функцию нормального распределения для получения Z-score
        // Для упрощения используем аппроксимацию через стандартное отклонение
        // Средний результат (50%) = IQ 100, стандартное отклонение = 15
        
        // Конвертируем процентиль в Z-score используя аппроксимацию
        // Z-score = inverse normal CDF от процентиля
        $zScore = $this->percentileToZScore($percentile);
        
        // Рассчитываем IQ: среднее 100, стандартное отклонение 15
        $iqScore = (int)round(100 + ($zScore * 15));
        
        // Ограничиваем диапазон IQ (обычно 70-145 для реалистичности)
        if ($iqScore < 70) {
            $iqScore = 70;
        } elseif ($iqScore > 145) {
            $iqScore = 145;
        }
        
        // Определяем категорию IQ
        $category = $this->determineIqCategory($iqScore);
        
        return [
            'correct_answers' => $correctAnswers,
            'total_questions' => $totalQuestions,
            'percentage' => round($percentage, 1),
            'iq_score' => $iqScore,
            'category' => $category,
            'student_id' => $currentUser['user_id'] !== '' ? $currentUser['user_id'] : null,
            'student_name' => $currentUser['user_name'] !== '' ? $currentUser['user_name'] : null,
            'completed_at' => date('Y-m-d H:i:s'),
        ];
    }

    /** Конвертируем процентиль в Z-score (обратная функция нормального распределения) */
    private function percentileToZScore(float $percentile): float
    {
        // Ограничиваем процентиль в диапазоне 0.001-0.999 для избежания экстремальных значений
        if ($percentile <= 0.001) {
            $percentile = 0.001;
        } elseif ($percentile >= 0.999) {
            $percentile = 0.999;
        }
        
        // Используем аппроксимацию обратной функции нормального распределения (inverse CDF)
        // Формула Acklam's approximation - точная и эффективная
        
        // Константы для аппроксимации
        $a0 = -3.969683028665376e+01;
        $a1 =  2.209460984245205e+02;
        $a2 = -2.759285104469687e+02;
        $a3 =  1.383577518672690e+02;
        $a4 = -3.066479806614716e+01;
        $a5 =  2.506628277459239e+00;
        
        $b0 = -5.447609879822406e+01;
        $b1 =  1.615858368580409e+02;
        $b2 = -1.556989798598866e+02;
        $b3 =  6.680131188771972e+01;
        $b4 = -1.328068155288572e+01;
        
        $c0 = -7.784894002430293e-03;
        $c1 = -3.223964580411365e-01;
        $c2 = -2.400758277161838e+00;
        $c3 = -2.549732539343734e+00;
        $c4 =  4.374664141464968e+00;
        $c5 =  2.938163982698783e+00;
        
        $d0 =  7.784695709041462e-03;
        $d1 =  3.224671290700398e-01;
        $d2 =  2.445134137142996e+00;
        $d3 =  3.754408661907416e+00;
        
        // Определяем, в какой области находится процентиль
        $split1 = 0.425;
        $split2 = 5.0;
        $const1 = 0.180625;
        $const2 = 1.6;
        
        $q = $percentile - 0.5;
        
        if (abs($q) <= $split1) {
            // Центральная область
            $r = $const1 - $q * $q;
            $z = $q * ((((($a5 * $r + $a4) * $r + $a3) * $r + $a2) * $r + $a1) * $r + $a0) /
                     ((((($b4 * $r + $b3) * $r + $b2) * $r + $b1) * $r + $b0) * $r + 1.0);
        } else {
            // Хвосты распределения
            $r = ($q < 0) ? $percentile : 1.0 - $percentile;
            $r = sqrt(-log($r));
            
            if ($r <= $split2) {
                $r = $r - $const2;
                $z = ((((($c5 * $r + $c4) * $r + $c3) * $r + $c2) * $r + $c1) * $r + $c0) /
                     ((($d3 * $r + $d2) * $r + $d1) * $r + $d0);
            } else {
                // Для очень экстремальных значений используем упрощенную формулу
                $r = $r - $split2;
                $z = ((((($c5 * $r + $c4) * $r + $c3) * $r + $c2) * $r + $c1) * $r + $c0) /
                     ((($d3 * $r + $d2) * $r + $d1) * $r + $d0);
            }
            
            if ($q < 0) {
                $z = -$z;
            }
        }
        
        return $z;
    }

    /** Определяем категорию IQ (по стандартной классификации Векслера) */
    private function determineIqCategory(int $iqScore): array
    {
        $category = '';
        $description = '';
        
        // Классификация по шкале Векслера (стандартное отклонение 15)
        if ($iqScore >= 145) {
            $category = 'Genial daraja';
            $description = 'Sizning intellektual qobiliyatingiz genial darajada. Bu juda kam uchraydigan daraja va siz murakkab muammolarni hal qilishda ajoyib qobiliyatlarga egasiz.';
        } elseif ($iqScore >= 130) {
            $category = 'Juda yuqori';
            $description = 'Sizning intellektual qobiliyatingiz juda yuqori darajada. Siz murakkab muammolarni hal qilishda ajoyib qobiliyatlarga egasiz va yuqori intellektual faoliyatga qodirsiz.';
        } elseif ($iqScore >= 120) {
            $category = 'Yuqori';
            $description = 'Sizning intellektual qobiliyatingiz yuqori darajada. Siz mantiqiy fikrlash va muammolarni hal qilishda yaxshi qobiliyatlarga egasiz.';
        } elseif ($iqScore >= 110) {
            $category = 'O\'rtadan yuqori';
            $description = 'Sizning intellektual qobiliyatingiz o\'rtadan yuqori darajada. Siz yaxshi mantiqiy fikrlash qobiliyatiga egasiz va murakkab vazifalarni bajarishga qodirsiz.';
        } elseif ($iqScore >= 90) {
            $category = 'O\'rtacha';
            $description = 'Sizning intellektual qobiliyatingiz o\'rtacha darajada. Bu normal va sog\'lom daraja hisoblanadi. Ko\'pchilik odamlar shu darajada bo\'ladi.';
        } elseif ($iqScore >= 80) {
            $category = 'O\'rtadan past';
            $description = 'Sizning intellektual qobiliyatingiz o\'rtadan past darajada. Ammo bu sizning qobiliyatlaringizni rivojlantirish imkoniyatini ko\'rsatadi va qo\'shimcha mashqlar bilan yaxshilash mumkin.';
        } elseif ($iqScore >= 70) {
            $category = 'Past';
            $description = 'Sizning intellektual qobiliyatingiz past darajada. Ammo bu test natijasi faqat bir ko\'rsatkichdir va boshqa omillar ham muhimdir. Qo\'shimcha yordam va mashqlar bilan yaxshilash mumkin.';
        } else {
            $category = 'Juda past';
            $description = 'Sizning intellektual qobiliyatingiz juda past darajada. Bu test natijasi faqat bir ko\'rsatkichdir va boshqa omillar ham muhimdir. Mutaxassislar bilan maslahatlashish tavsiya etiladi.';
        }
        
        return [
            'name' => $category,
            'description' => $description,
        ];
    }

    /** Сохраняем результаты */
    private function saveResults(array $results): void
    {
        $storagePath = dirname(__DIR__, 2) . '/storage/iq-results.jsonl';
        $line = json_encode($results, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($storagePath, $line, FILE_APPEND);
        
        // Также сохраняем в сессию для быстрого доступа
        $_SESSION['iq_last_results'] = $results;
    }

    /** Загружаем результаты студента */
    private function loadResults(): ?array
    {
        // Сначала проверяем сессию
        if (isset($_SESSION['iq_last_results'])) {
            return $_SESSION['iq_last_results'];
        }

        // Затем проверяем файл
        $storagePath = dirname(__DIR__, 2) . '/storage/iq-results.jsonl';
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
    public function hasCompletedTest(): bool
    {
        return $this->loadResults() !== null;
    }
}

