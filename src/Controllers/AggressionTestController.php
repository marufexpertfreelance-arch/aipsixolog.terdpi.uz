<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;

class AggressionTestController
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
                'user_id'   => $id,
                'user_name' => (string)($teacher['full_name'] ?? 'O\'qituvchi'),
                'user_type' => 'teacher',
            ];
        }

        if (!empty($_SESSION['hemis_user'])) {
            $u = $_SESSION['hemis_user'];
            $id = (string)($u['login'] ?? ($u['student_id'] ?? ''));
            return [
                'user_id'   => $id,
                'user_name' => (string)($u['name'] ?? 'Talaba'),
                'user_type' => 'student',
            ];
        }

        return [
            'user_id'   => '',
            'user_name' => '',
            'user_type' => 'guest',
        ];
    }

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $this->testDataPath = $root . '/data/aggression-test.json';

        $dataDir = dirname($this->testDataPath);
        if (!is_dir($dataDir)) {
            mkdir($dataDir, 0755, true);
        }
    }

    /** Sahifasini ko'rsatamiz */
    public function start(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            $_SESSION['redirect_after_login'] = '/aggression/start';
            header('Location: /hemis/login');
            exit;
        }

        unset($_SESSION['aggression_answers']);

        $testData = $this->loadTestData();
        if (!$testData) {
            die('Test yuklanmadi');
        }

        echo View::render('aggression/start', [
            'title' => 'Tajovuz holati tashxisi',
            'test'  => $testData,
        ]);
    }

    /** Savol ko'rsatamiz */
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

        $currentQuestion = (int)($_GET['q'] ?? 1);
        $totalQuestions  = count($testData['questions']);

        if ($currentQuestion < 1 || $currentQuestion > $totalQuestions) {
            header('Location: /aggression/start');
            exit;
        }

        $answers = $_SESSION['aggression_answers'] ?? [];

        echo View::render('aggression/question', [
            'title'           => 'Savol ' . $currentQuestion . ' / ' . $totalQuestions,
            'test'            => $testData,
            'question'        => $testData['questions'][$currentQuestion - 1],
            'currentQuestion' => $currentQuestion,
            'totalQuestions'  => $totalQuestions,
            'currentAnswer'   => $answers[$currentQuestion] ?? null,
        ]);
    }

    /** Javobni qayta ishlaymiz */
    public function answer(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        $questionId = (int)($_POST['question_id'] ?? 0);
        $answer     = $_POST['answer'] ?? null; // 'yes' or 'no'

        if (!$questionId || !in_array($answer, ['yes', 'no'], true)) {
            header('Location: /aggression/start');
            exit;
        }

        if (!isset($_SESSION['aggression_answers'])) {
            $_SESSION['aggression_answers'] = [];
        }
        $_SESSION['aggression_answers'][$questionId] = ($answer === 'yes') ? 1 : 0;

        $testData       = $this->loadTestData();
        $totalQuestions = count($testData['questions']);

        if ($questionId < $totalQuestions) {
            header('Location: /aggression/question?q=' . ($questionId + 1));
        } else {
            header('Location: /aggression/complete');
        }
        exit;
    }

    /** Testni yakunlaymiz */
    public function complete(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        $testData = $this->loadTestData();
        $answers  = $_SESSION['aggression_answers'] ?? [];

        if (count($answers) < count($testData['questions'])) {
            header('Location: /aggression/start');
            exit;
        }

        $results = $this->calculateResults($testData, $answers);
        $this->saveResults($results);

        unset($_SESSION['aggression_answers']);

        echo View::render('aggression/results', [
            'title'   => 'Test natijalari',
            'results' => $results,
            'test'    => $testData,
        ]);
    }

    /** Saqlangan natijalarni ko'rsatamiz */
    public function results(): void
    {
        $currentUser = $this->getCurrentUser();
        if ($currentUser['user_id'] === '') {
            header('Location: /hemis/login');
            exit;
        }

        $results = $this->loadResults();
        if (!$results) {
            header('Location: /aggression/start');
            exit;
        }

        $testData = $this->loadTestData();

        echo View::render('aggression/results', [
            'title'   => 'Test natijalari',
            'results' => $results,
            'test'    => $testData,
        ]);
    }

    /** Test ma'lumotlarini yuklaymiz */
    private function loadTestData(): ?array
    {
        if (!file_exists($this->testDataPath)) {
            return null;
        }

        $content = file_get_contents($this->testDataPath);
        $data    = json_decode($content, true);

        return $data ?: null;
    }

    /** Natijalarni hisoblaymiz (Buss-Darki kaliti bo'yicha) */
    private function calculateResults(array $testData, array $answers): array
    {
        $currentUser = $this->getCurrentUser();
        $scoringKey  = $testData['scoring_key'] ?? [];
        $scales      = $testData['scales'] ?? [];

        // Har bir shkala uchun ball hisoblaymiz
        $scaleScores = [];
        foreach ($scales as $scale) {
            $scaleId  = (string)$scale['id'];
            $k        = $scale['k'];
            $key      = $scoringKey[$scaleId] ?? ['plus' => [], 'minus' => []];
            $rawScore = 0;

            // "+" javoblari (javob = "ha" = 1 bo'lsa ball beriladi)
            foreach ($key['plus'] as $qId) {
                if (($answers[$qId] ?? 0) === 1) {
                    $rawScore++;
                }
            }
            // "-" javoblari (javob = "yo'q" = 0 bo'lsa ball beriladi)
            foreach ($key['minus'] as $qId) {
                if (($answers[$qId] ?? 1) === 0) {
                    $rawScore++;
                }
            }

            // Normallashtirilgan ball (k koeffitsientiga ko'paytiriladi)
            $maxRaw   = count($key['plus']) + count($key['minus']);
            $normalized = $maxRaw > 0 ? round(($rawScore / $maxRaw) * $k, 1) : 0;

            $scaleScores[$scale['key']] = [
                'scale_id'   => (int)$scaleId,
                'name'       => $scale['name'],
                'raw_score'  => $rawScore,
                'max_raw'    => $maxRaw,
                'k'          => $k,
                'score'      => $normalized,
                'color'      => $scale['color'],
            ];
        }

        // Umumiy ko'rsatkichlar (formulalar)
        // TI = (jismoniy + verbal + bilvosita) / 3
        $ti = ($scaleScores['jismoniy']['score']
            + $scaleScores['verbal']['score']
            + $scaleScores['bilvosita']['score']) / 3;

        // DI = (shubha + hafagarchi) / 2
        $di = ($scaleScores['shubha']['score']
            + $scaleScores['hafagarchi']['score']) / 2;

        // Meyor chegaralari
        $tiNorm = ['min' => 17, 'max' => 25]; // 21 +/- 4
        $diNorm = ['min' => 3.5, 'max' => 9.5]; // 6.5 +/- 3

        $tiLevel = $this->getLevel($ti, $tiNorm['min'], $tiNorm['max']);
        $diLevel = $this->getLevel($di, $diNorm['min'], $diNorm['max']);

        return [
            'scale_scores'  => $scaleScores,
            'ti'            => round($ti, 2),
            'di'            => round($di, 2),
            'ti_norm'       => $tiNorm,
            'di_norm'       => $diNorm,
            'ti_level'      => $tiLevel,
            'di_level'      => $diLevel,
            'interpretation'=> $this->getInterpretation($scaleScores, $ti, $di),
            'student_id'    => $currentUser['user_id'] !== '' ? $currentUser['user_id'] : null,
            'student_name'  => $currentUser['user_name'] !== '' ? $currentUser['user_name'] : null,
            'completed_at'  => date('Y-m-d H:i:s'),
        ];
    }

    /** Daraja aniqlaymiz */
    private function getLevel(float $score, float $min, float $max): string
    {
        if ($score < $min) {
            return 'past';
        } elseif ($score > $max) {
            return 'yuqori';
        }
        return 'me\'yor';
    }

    /** Natijani talqin qilamiz */
    private function getInterpretation(array $scaleScores, float $ti, float $di): array
    {
        $interpretation = [];

        foreach ($scaleScores as $key => $scale) {
            $k     = $scale['k'];
            $score = $scale['score'];
            // Odatda k ning 60-70% dan yuqori bo'lsa - yuqori
            $threshold = $k * 0.6;
            $high      = $score >= $threshold;

            $interpretation[$key] = [
                'high'      => $high,
                'threshold' => $threshold,
                'label'     => $high
                    ? 'Yuqori daraja'
                    : 'Me\'yor doirasida',
                'color'     => $high ? '#ef4444' : '#10b981',
            ];
        }

        return $interpretation;
    }

    /** Natijalarni saqlaymiz */
    private function saveResults(array $results): void
    {
        $storagePath = dirname(__DIR__, 2) . '/storage/aggression-results.jsonl';
        $line        = json_encode($results, JSON_UNESCAPED_UNICODE) . "\n";
        file_put_contents($storagePath, $line, FILE_APPEND);

        $_SESSION['aggression_last_results'] = $results;
    }

    /** Talaba natijalarini yuklaymiz */
    private function loadResults(): ?array
    {
        if (isset($_SESSION['aggression_last_results'])) {
            return $_SESSION['aggression_last_results'];
        }

        $storagePath = dirname(__DIR__, 2) . '/storage/aggression-results.jsonl';
        if (!file_exists($storagePath)) {
            return null;
        }

        $currentUser = $this->getCurrentUser();
        $studentId   = $currentUser['user_id'] !== '' ? $currentUser['user_id'] : null;
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
}
