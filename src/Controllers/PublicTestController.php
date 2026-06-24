<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\TestStorage;
use App\Services\ResultsStorage;
use App\Services\TestCalculationService;
use App\View;

class PublicTestController
{
    private TestStorage $storage;
    private ResultsStorage $resultsStorage;

    public function __construct()
    {
        $this->storage = new TestStorage();
        $this->resultsStorage = new ResultsStorage();
    }

    /** Показываем форму прохождения теста */
    public function takeForm(): void
    {
        // Определяем тип пользователя: студент или преподаватель
        $isTeacher = !empty($_SESSION['teacher_user']);
        $isStudent = !empty($_SESSION['hemis_user']);
        
        // Проверяем авторизацию
        if (!$isTeacher && !$isStudent) {
            $_SESSION['redirect_after_login'] = '/tests/take?id=' . urlencode($_GET['id'] ?? '');
            header('Location: /hemis/login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $errorKey = $isTeacher ? 'teacher_error' : 'hemis_error';
            $redirectUrl = $isTeacher ? '/teacher/dashboard' : '/dashboard';
            $_SESSION[$errorKey] = 'Noto\'g\'ri test ID.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $test = $this->storage->findById($id);

        if ($test === null) {
            $errorKey = $isTeacher ? 'teacher_error' : 'hemis_error';
            $redirectUrl = $isTeacher ? '/teacher/dashboard' : '/dashboard';
            $_SESSION[$errorKey] = 'Test topilmadi.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        // Получаем данные пользователя из сессии
        $user = $isTeacher ? ($_SESSION['teacher_user'] ?? []) : ($_SESSION['hemis_user'] ?? []);
        $studentId = $isTeacher 
            ? 'teacher_' . ($user['id'] ?? 'unknown')
            : ($user['login'] ?? $user['student_id'] ?? null);
        
        // Проверяем, не прошел ли студент уже этот тест полностью
        if ($studentId) {
            $studentResults = $this->resultsStorage->getResultsByStudentId($studentId);
            $questionsCount = count($test['questions'] ?? []);
            
            foreach ($studentResults as $result) {
                if (($result['test_id'] ?? null) == $id && ($result['test_type'] ?? '') === 'custom') {
                    // Проверяем, ответил ли студент на все вопросы
                    $answers = $result['answers'] ?? [];
                    $answeredCount = 0;
                    foreach ($answers as $answer) {
                        $answerStr = trim((string)$answer);
                        if (!empty($answerStr)) {
                            $answeredCount++;
                        }
                    }
                    
                    // Если тест полностью пройден (ответы на все вопросы), запрещаем повторное прохождение
                    if ($answeredCount >= $questionsCount && $questionsCount > 0) {
                        $_SESSION['hemis_error'] = 'Siz bu testni allaqachon to\'liq topshirdingiz. Testni faqat bir marta o\'tish mumkin.';
                        header('Location: /dashboard');
                        exit;
                    }
                }
            }
        }
        
        // Проверяем доступ: для преподавателей - по allowed_teachers, для студентов - по группам/факультетам
        if ($isTeacher) {
            // Логика для преподавателей
            $allowedTeachers = $test['allowed_teachers'] ?? [];
            $teacherId = $user['id'] ?? null;
            
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            error_log("PublicTestController::takeForm() - Teacher ID: {$teacherId}, Allowed Teachers: " . json_encode($allowedTeachers));
            }
            
            // Если список преподавателей пуст, тест не назначен никому из преподавателей
            if (empty($allowedTeachers) || !is_array($allowedTeachers)) {
                $_SESSION['teacher_error'] = 'Bu test sizga tayinlanmagan.';
                header('Location: /teacher/dashboard');
                exit;
            }
            
            // Проверяем, есть ли ID преподавателя в списке
            if (!in_array($teacherId, $allowedTeachers, false)) {
                $_SESSION['teacher_error'] = 'Bu test sizga tayinlanmagan.';
                header('Location: /teacher/dashboard');
                exit;
            }
            
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            error_log("PublicTestController::takeForm() - ACCESS GRANTED for teacher {$teacherId}");
            }
        } else {
            // Логика для студентов (как раньше)
            $allowedGroups = $test['allowed_groups'] ?? [];
            $allowedFaculties = $test['allowed_faculties'] ?? [];
            $openForAll = $test['open_for_all'] ?? false;
            $userGroup = $user['group'] ?? null;
            $userFaculty = $user['faculty'] ?? null;
            
            // Функция нормализации для сравнения (trim + lowercase + удаление невидимых символов)
            $normalize = function($str) {
                if ($str === null || $str === '') {
                    return '';
                }
                // Удаляем все пробелы (включая неразрывные), приводим к нижнему регистру
                $normalized = mb_strtolower(trim((string)$str));
                // Удаляем невидимые символы и лишние пробелы
                $normalized = preg_replace('/\s+/', ' ', $normalized);
                return trim($normalized);
            };
            
            // Логируем только в режиме разработки
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            error_log("PublicTestController::takeForm() - Test ID: {$id}");
            error_log("PublicTestController::takeForm() - Test Title: " . ($test['title'] ?? 'null'));
            error_log("PublicTestController::takeForm() - Allowed Groups (raw): " . json_encode($allowedGroups));
            error_log("PublicTestController::takeForm() - Allowed Faculties (raw): " . json_encode($allowedFaculties));
            error_log("PublicTestController::takeForm() - Open for all: " . ($openForAll ? 'true' : 'false'));
            error_log("PublicTestController::takeForm() - User Group (raw): " . var_export($userGroup, true));
            error_log("PublicTestController::takeForm() - User Faculty (raw): " . var_export($userFaculty, true));
            }
            
            // ВАЖНО: Проверяем флаг open_for_all
            // Если open_for_all = true И оба массива пусты → доступ разрешен
            // Если open_for_all = false И оба массива пусты → доступ запрещен
            // Если указаны группы или факультеты → проверяем доступ по ним
            $hasAccess = false;
            
            if (empty($allowedGroups) && empty($allowedFaculties)) {
                // Проверяем флаг open_for_all
                if ($openForAll) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::takeForm() - Test {$id} has no groups/faculties selected, but open_for_all=true, access granted to all");
                    }
                } else {
                    // open_for_all = false и нет групп/факультетов → доступ запрещен
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::takeForm() - Test {$id} has no groups/faculties selected and open_for_all=false, access denied");
                    }
                }
            }
            
            // Если указаны группы - проверяем СТРОГО по группам
            if (!$hasAccess && !empty($allowedGroups) && is_array($allowedGroups)) {
                $normalizedAllowedGroups = array_map($normalize, array_filter($allowedGroups, function($g) {
                    return $g !== null && $g !== '';
                }));
                $normalizedUserGroup = $userGroup ? $normalize($userGroup) : '';
                
                // Детальное логирование только в режиме разработки
                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                error_log("PublicTestController::takeForm() - Normalized User Group: '{$normalizedUserGroup}'");
                error_log("PublicTestController::takeForm() - Normalized Allowed Groups: " . json_encode($normalizedAllowedGroups));
                error_log("PublicTestController::takeForm() - In array check: " . (in_array($normalizedUserGroup, $normalizedAllowedGroups, true) ? 'true' : 'false'));
                }
                
                if (!empty($normalizedUserGroup) && in_array($normalizedUserGroup, $normalizedAllowedGroups, true)) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                error_log("PublicTestController::takeForm() - ACCESS GRANTED - User group matches");
                    }
                } else {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::takeForm() - ACCESS DENIED - User group '{$userGroup}' (normalized: '{$normalizedUserGroup}') not in allowed groups: " . json_encode($normalizedAllowedGroups));
                    }
                }
            }
            // Если группы не указаны, но указаны факультеты - проверяем по факультетам
            if (!$hasAccess && !empty($allowedFaculties) && is_array($allowedFaculties)) {
                $normalizedAllowedFaculties = array_map($normalize, array_filter($allowedFaculties, function($f) {
                    return $f !== null && $f !== '';
                }));
                $normalizedUserFaculty = $userFaculty ? $normalize($userFaculty) : '';
                
                if (!empty($normalizedUserFaculty) && in_array($normalizedUserFaculty, $normalizedAllowedFaculties, true)) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::takeForm() - ACCESS GRANTED - User faculty matches");
                    }
                } else {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::takeForm() - User faculty '{$userFaculty}' (normalized: '{$normalizedUserFaculty}') not in allowed faculties: " . json_encode($normalizedAllowedFaculties));
                    }
                }
            }
            
            // Если доступ не предоставлен - блокируем
            if (!$hasAccess) {
                $_SESSION['hemis_error'] = 'Sizga bu test uchun ruxsat berilmagan.';
                    header('Location: /dashboard');
                    exit;
            }
        }

        echo View::render('tests/take', [
            'title' => ($test['title'] ?? 'Test') . ' – So\'rovnoma',
            'test' => $test,
        ]);
    }

    /** Обрабатываем отправку результатов теста */
    public function submit(): void
    {
        // Определяем тип пользователя: студент или преподаватель
        $isTeacher = !empty($_SESSION['teacher_user']);
        $isStudent = !empty($_SESSION['hemis_user']);
        
        // Проверяем авторизацию
        if (!$isTeacher && !$isStudent) {
            $_SESSION['hemis_error'] = 'Testdan o\'tish uchun tizimga kirishingiz kerak.';
            header('Location: /hemis/login');
            exit;
        }

        $id = isset($_POST['test_id']) ? (int)$_POST['test_id'] : 0;

        if ($id <= 0) {
            $errorKey = $isTeacher ? 'teacher_error' : 'hemis_error';
            $redirectUrl = $isTeacher ? '/teacher/dashboard' : '/dashboard';
            $_SESSION[$errorKey] = 'Noto\'g\'ri test ID.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        $test = $this->storage->findById($id);

        if ($test === null) {
            $errorKey = $isTeacher ? 'teacher_error' : 'hemis_error';
            $redirectUrl = $isTeacher ? '/teacher/dashboard' : '/dashboard';
            $_SESSION[$errorKey] = 'Test topilmadi.';
            header('Location: ' . $redirectUrl);
            exit;
        }

        // Получаем данные пользователя
        $user = $isTeacher ? ($_SESSION['teacher_user'] ?? []) : ($_SESSION['hemis_user'] ?? []);
        
        // Проверяем доступ: для преподавателей - по allowed_teachers, для студентов - по группам/факультетам
        if ($isTeacher) {
            // Логика для преподавателей
            $allowedTeachers = $test['allowed_teachers'] ?? [];
            $teacherId = $user['id'] ?? null;
            
            if (empty($allowedTeachers) || !is_array($allowedTeachers) || !in_array($teacherId, $allowedTeachers, false)) {
                $_SESSION['teacher_error'] = 'Bu test sizga tayinlanmagan.';
                header('Location: /teacher/dashboard');
                exit;
            }
        } else {
            // Логика для студентов (как раньше)
            $allowedGroups = $test['allowed_groups'] ?? [];
            $allowedFaculties = $test['allowed_faculties'] ?? [];
            $openForAll = $test['open_for_all'] ?? false;
            $userGroup = $user['group'] ?? null;
            $userFaculty = $user['faculty'] ?? null;
            
            // Функция нормализации для сравнения (trim + lowercase)
            $normalize = function($str) {
                if ($str === null || $str === '') {
                    return '';
                }
                // Удаляем все пробелы (включая неразрывные), приводим к нижнему регистру
                $normalized = mb_strtolower(trim((string)$str));
                // Удаляем невидимые символы и лишние пробелы
                $normalized = preg_replace('/\s+/', ' ', $normalized);
                return trim($normalized);
            };
            
            // ВАЖНО: Проверяем флаг open_for_all
            // Если open_for_all = true И оба массива пусты → доступ разрешен
            // Если open_for_all = false И оба массива пусты → доступ запрещен
            // Если указаны группы или факультеты → проверяем доступ по ним
            $hasAccess = false;
            
            if (empty($allowedGroups) && empty($allowedFaculties)) {
                // Проверяем флаг open_for_all
                if ($openForAll) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::submit() - Test {$id} has no groups/faculties selected, but open_for_all=true, access granted to all");
                    }
                } else {
                    // open_for_all = false и нет групп/факультетов → доступ запрещен
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::submit() - Test {$id} has no groups/faculties selected and open_for_all=false, access denied");
                    }
                }
            }
            
            // Если указаны группы - проверяем СТРОГО по группам
            if (!$hasAccess && !empty($allowedGroups) && is_array($allowedGroups)) {
                $normalizedAllowedGroups = array_map($normalize, array_filter($allowedGroups, function($g) {
                    return $g !== null && $g !== '';
                }));
                $normalizedUserGroup = $userGroup ? $normalize($userGroup) : '';
                
                if (!empty($normalizedUserGroup) && in_array($normalizedUserGroup, $normalizedAllowedGroups, true)) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::submit() - ACCESS GRANTED - User group matches");
                    }
                } else {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::submit() - ACCESS DENIED - User group '{$userGroup}' (normalized: '{$normalizedUserGroup}') not in allowed groups: " . json_encode($allowedGroups));
                    }
                }
            }
            // Если группы не указаны, но указаны факультеты - проверяем по факультетам
            if (!$hasAccess && !empty($allowedFaculties) && is_array($allowedFaculties)) {
                $normalizedAllowedFaculties = array_map($normalize, array_filter($allowedFaculties, function($f) {
                    return $f !== null && $f !== '';
                }));
                $normalizedUserFaculty = $userFaculty ? $normalize($userFaculty) : '';
                
                if (!empty($normalizedUserFaculty) && in_array($normalizedUserFaculty, $normalizedAllowedFaculties, true)) {
                    $hasAccess = true;
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::submit() - ACCESS GRANTED - User faculty matches");
                    }
                } else {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("PublicTestController::submit() - User faculty '{$userFaculty}' (normalized: '{$normalizedUserFaculty}') not in allowed faculties: " . json_encode($allowedFaculties));
                    }
                }
            }
            
            // Если доступ не предоставлен - блокируем
            if (!$hasAccess) {
                $_SESSION['hemis_error'] = 'Sizga bu test uchun ruxsat berilmagan.';
                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                    error_log("PublicTestController::submit() - ACCESS DENIED - User does not have access to test {$id}");
                }
                header('Location: /dashboard');
                    exit;
            }
        }

        $answers = $_POST['answers'] ?? [];
        if (!is_array($answers)) {
            $answers = [];
        }

        // Обрабатываем массивы ответов (для checkbox - multiple_select)
        // Преобразуем массивы в строки для хранения
        $processedAnswers = [];
        foreach ($answers as $index => $answer) {
            if (is_array($answer)) {
                // Это массив ответов (checkbox), объединяем их через запятую
                $filtered = array_filter($answer, function($item) {
                    return !empty(trim($item));
                });
                $processedAnswers[$index] = !empty($filtered) ? implode(', ', $filtered) : '';
            } else {
                // Обычный ответ (radio или text)
                $processedAnswers[$index] = $answer;
            }
        }
        
        // Обрабатываем случаи, когда для multiple_select ничего не выбрано
        // Проверяем все вопросы теста
        foreach ($test['questions'] ?? [] as $qIdx => $question) {
            if (($question['type'] ?? '') === 'multiple_select') {
                // Если для вопроса с multiple_select нет ответа, добавляем пустую строку
                if (!isset($processedAnswers[$qIdx])) {
                    $processedAnswers[$qIdx] = '';
                }
            }
        }

        // Обрабатываем дополнительные текстовые поля для "Boshqa"
        foreach ($_POST as $key => $value) {
            if (strpos($key, '_other') !== false && !empty($value)) {
                $questionIndex = str_replace('_other', '', $key);
                // Если был выбран "Boshqa", заменяем ответ на текстовый
                if (isset($processedAnswers[$questionIndex]) && $processedAnswers[$questionIndex] === 'Boshqa') {
                    $processedAnswers[$questionIndex] = trim($value);
                } elseif (isset($processedAnswers[$questionIndex]) && is_string($processedAnswers[$questionIndex])) {
                    // Если это массив ответов с "Boshqa", добавляем текстовый ответ
                    $answersArray = explode(', ', $processedAnswers[$questionIndex]);
                    if (in_array('Boshqa', $answersArray)) {
                        $answersArray = array_filter($answersArray, function($item) { return $item !== 'Boshqa'; });
                        $answersArray[] = trim($value);
                        $processedAnswers[$questionIndex] = implode(', ', $answersArray);
                    }
                }
            }
        }

        $answers = $processedAnswers;

        // ВАЛИДАЦИЯ: Проверяем, что все вопросы отвечены
        $totalQuestions = count($test['questions'] ?? []);
        $answeredQuestions = 0;
        
        foreach ($test['questions'] ?? [] as $qIdx => $question) {
            $questionType = $question['type'] ?? 'text';
            
            // Проверяем, есть ли ответ на этот вопрос
            $hasAnswer = false;
            
            if ($questionType === 'multiple_select') {
                // Для multiple_select проверяем, что есть хотя бы один выбранный вариант
                if (isset($processedAnswers[$qIdx]) && !empty(trim($processedAnswers[$qIdx]))) {
                    $hasAnswer = true;
                }
            } else {
                // Для остальных типов проверяем, что ответ не пустой
                if (isset($processedAnswers[$qIdx]) && !empty(trim($processedAnswers[$qIdx]))) {
                    $hasAnswer = true;
                }
            }
            
            if ($hasAnswer) {
                $answeredQuestions++;
            }
        }
        
        // Если не все вопросы отвечены, возвращаем ошибку
        if ($answeredQuestions < $totalQuestions) {
            $errorKey = $isTeacher ? 'teacher_error' : 'hemis_error';
            $_SESSION[$errorKey] = 'Iltimos, barcha savollarga javob bering. ' . 
                $answeredQuestions . ' / ' . $totalQuestions . ' savolga javob berildi.';
            header('Location: /tests/take?id=' . $id);
            exit;
        }

        // Данные пользователя уже получены ранее в $user

        $root = dirname(__DIR__, 2);
        $storageDir = $root . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $file = $storageDir . '/results.jsonl';

        // Рассчитываем результаты, если настроена конфигурация расчета
        $calculationService = new TestCalculationService();
        $calculationResult = $calculationService->calculateResults($test, $answers);

        $record = [
            'test_id'      => $id,
            'test_title'   => $test['title'] ?? '',
            'test_type'    => 'custom', // Обычный тест психолога
            'student_id'   => $isTeacher 
                ? ('teacher_' . ($user['id'] ?? 'unknown'))
                : ($user['login'] ?? $user['student_id'] ?? null),
            'student_name' => $isTeacher 
                ? ($user['full_name'] ?? null)
                : ($user['name'] ?? null),
            'calculated_score' => $calculationResult['calculated_score'] ?? null,
            'interpretation' => $calculationResult['interpretation'] ?? null,
            'scales' => $calculationResult['scales'] ?? null,
            'submitted_at' => date('Y-m-d H:i:s'),
            'submitted_at_iso' => date(DATE_ATOM),
            'answers'      => $answers,
            'calculated_score' => $calculationResult['calculated_score'] ?? null,
            'interpretation' => $calculationResult['interpretation'] ?? null,
            'scales' => $calculationResult['scales'] ?? null,
            'percentages' => $calculationResult['percentages'] ?? null,
            'dominant_answer' => $calculationResult['dominant_answer'] ?? null,
            'meta'         => [
                'ip'         => $_SERVER['REMOTE_ADDR'] ?? null,
                'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? null,
            ],
        ];

        file_put_contents(
            $file,
            json_encode($record, JSON_UNESCAPED_UNICODE) . PHP_EOL,
            FILE_APPEND
        );

        echo View::render('tests/thanks', [
            'title' => 'Rahmat',
        ]);
    }

    /** Показываем результаты теста для студента */
    public function showResults(): void
    {
        // Проверяем авторизацию студента
        if (empty($_SESSION['hemis_user'])) {
            $_SESSION['redirect_after_login'] = '/tests/results?id=' . urlencode($_GET['id'] ?? '');
            header('Location: /hemis/login');
            exit;
        }

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['hemis_error'] = 'Noto\'g\'ri test ID.';
            header('Location: /dashboard');
            exit;
        }

        $test = $this->storage->findById($id);

        if ($test === null) {
            $_SESSION['hemis_error'] = 'Test topilmadi.';
            header('Location: /dashboard');
            exit;
        }

        // Студенты не должны видеть результаты кастомных тестов (созданных психологом)
        // Разрешаем просмотр только встроенных тестов (Айзенк, IQ, Люшер)
        // Кастомные тесты имеют числовой ID > 0
        if ($id > 0) {
            $_SESSION['hemis_error'] = 'Bu test natijalarini ko\'rish uchun ruxsat yo\'q.';
            header('Location: /dashboard');
            exit;
        }

        // Получаем результаты студента для этого теста
        $user = $_SESSION['hemis_user'];
        $studentId = $user['login'] ?? $user['student_id'] ?? null;
        
        if (!$studentId) {
            $_SESSION['hemis_error'] = 'Talaba ID topilmadi.';
            header('Location: /dashboard');
            exit;
        }

        // Получаем все результаты студента
        $allResults = $this->resultsStorage->getResultsByStudentId($studentId);
        
        // Находим результат для этого теста (берем последний - самый свежий)
        $result = null;
        $resultsForThisTest = [];
        
        foreach ($allResults as $r) {
            if (($r['test_id'] ?? null) == $id && ($r['test_type'] ?? '') === 'custom') {
                $resultsForThisTest[] = $r;
            }
        }
        
        if (!empty($resultsForThisTest)) {
            // Сортируем по дате и берем последний (самый свежий)
            usort($resultsForThisTest, function($a, $b) {
                $dateA = $a['submitted_at'] ?? $a['completed_at'] ?? '';
                $dateB = $b['submitted_at'] ?? $b['completed_at'] ?? '';
                return strtotime($dateB) - strtotime($dateA);
            });
            $result = $resultsForThisTest[0];
        }

        if ($result === null) {
            $_SESSION['hemis_error'] = 'Bu test uchun natijalar topilmadi.';
            header('Location: /dashboard');
            exit;
        }

        echo View::render('tests/results', [
            'title' => ($test['title'] ?? 'Test') . ' – Natijalar',
            'test' => $test,
            'result' => $result,
        ]);
    }
}
