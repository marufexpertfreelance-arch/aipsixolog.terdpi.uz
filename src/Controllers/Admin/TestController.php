<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\TestStorage;
use App\HemisApi;

class TestController
{
    private TestStorage $storage;

    public function __construct()
    {
        $this->storage = new TestStorage();
    }

    private function ensureAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    public function dashboard(): void
    {
        $this->ensureAuth();

        $tests = $this->storage->getAll();
        $flash = $_SESSION['admin_flash'] ?? null;
        unset($_SESSION['admin_flash']);
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_error']);

        // Получаем все данные для дашборда
        $resultsStorage = new \App\Services\ResultsStorage();
        $studentStorage = new \App\Services\StudentStorage();
        $teacherService = new \App\Services\TeacherService();
        
        $allStudents = $studentStorage->getAll();
        $allTeachers = $teacherService->getAll();
        
        // Сортируем преподавателей по дате регистрации (новые сверху)
        usort($allTeachers, function ($a, $b) {
            return strtotime($b['registered_at'] ?? '1970-01-01') - strtotime($a['registered_at'] ?? '1970-01-01');
        });
        
        // Обрабатываем тесты (та же логика что и в index)
        foreach ($tests as &$test) {
            $testId = (int)($test['id'] ?? 0);
            
            $allowedGroups = $test['allowed_groups'] ?? [];
            if (!is_array($allowedGroups)) {
                $allowedGroups = [];
            }
            $allowedGroups = array_filter($allowedGroups, function($g) {
                return !empty($g) && trim((string)$g) !== '';
            });
            $allowedGroups = array_values($allowedGroups);
            $test['allowed_groups'] = $allowedGroups;
            
            $allowedTeachers = $test['allowed_teachers'] ?? [];
            if (!is_array($allowedTeachers)) {
                $allowedTeachers = [];
            }
            $allowedTeachers = array_filter($allowedTeachers, function($t) {
                return !empty($t) && is_numeric($t);
            });
            $allowedTeachers = array_values(array_map('intval', $allowedTeachers));
            
            $teacherData = [];
            foreach ($allowedTeachers as $teacherId) {
                foreach ($allTeachers as $teacher) {
                    if ((int)($teacher['id'] ?? 0) === $teacherId) {
                        $teacherData[] = [
                            'id' => $teacher['id'],
                            'full_name' => $teacher['full_name'] ?? '',
                        ];
                        break;
                    }
                }
            }
            $test['teacher_data'] = $teacherData;
            
            $normalize = function($str) {
                return mb_strtolower(trim((string)$str));
            };
            
            $studentsInGroups = [];
            if (!empty($allowedGroups)) {
                foreach ($allStudents as $student) {
                    $studentGroup = $student['group'] ?? null;
                    if ($studentGroup) {
                        $normalizedStudentGroup = $normalize($studentGroup);
                        foreach ($allowedGroups as $allowedGroup) {
                            $normalizedAllowedGroup = $normalize($allowedGroup);
                            if ($normalizedStudentGroup === $normalizedAllowedGroup) {
                                $studentsInGroups[] = $student;
                                break;
                            }
                        }
                    }
                }
            } else {
                $studentsInGroups = $allStudents;
            }
            
            $testResults = $resultsStorage->getResultsByTestId($testId);
            $passedStudentIds = [];
            foreach ($testResults as $result) {
                $studentId = $result['student_id'] ?? null;
                if ($studentId) {
                    $passedStudentIds[$studentId] = true;
                }
            }
            
            $passedCount = 0;
            foreach ($studentsInGroups as $student) {
                $studentId = $student['student_id'] ?? $student['id'] ?? null;
                if ($studentId && isset($passedStudentIds[$studentId])) {
                    $passedCount++;
                }
            }
            
            $test['stats'] = [
                'total_students' => count($studentsInGroups),
                'passed_count' => $passedCount,
                'groups_count' => count($allowedGroups),
            ];
        }
        unset($test);

        // Краткая статистика результатов для вкладки "Natijalar"
        $allResults = $resultsStorage->getAllResults();
        $resultsCount = count($allResults);
        $totalResults = $resultsCount;
        
        // Статистика по типам
        $resultsByType = [
            'custom' => 0,
            'eysenck' => 0,
            'iq' => 0,
            'lusher' => 0,
        ];
        foreach ($allResults as $result) {
            $type = $result['test_type'] ?? 'custom';
            if (isset($resultsByType[$type])) {
                $resultsByType[$type]++;
            }
        }

        require __DIR__ . '/../../../views/admin/dashboard.php';
    }

    public function index(): void
    {
        $this->ensureAuth();

        $tests = $this->storage->getAll();
        $title = 'Мои психологические тесты';
        $flash = $_SESSION['admin_flash'] ?? null;
        unset($_SESSION['admin_flash']);

        // Получаем статистику для каждого теста
        $resultsStorage = new \App\Services\ResultsStorage();
        $studentStorage = new \App\Services\StudentStorage();
        $allStudents = $studentStorage->getAll();
        
        // Получаем всех преподавателей для отображения
        $teacherService = new \App\Services\TeacherService();
        $allTeachers = $teacherService->getAll();
        
        // Предварительно загружаем все результаты всех тестов один раз
        // Это быстрее, чем загружать для каждого теста отдельно
        $allTestResults = [];
        foreach ($tests as $test) {
            $testId = (int)($test['id'] ?? 0);
            if ($testId > 0) {
                $allTestResults[$testId] = $resultsStorage->getResultsByTestId($testId);
            }
        }
        
        foreach ($tests as &$test) {
            $testId = (int)($test['id'] ?? 0);
            
            // Убеждаемся, что allowed_groups - это массив и не пустой
            $allowedGroups = $test['allowed_groups'] ?? [];
            if (!is_array($allowedGroups)) {
                $allowedGroups = [];
            }
            // Фильтруем пустые значения
            $allowedGroups = array_filter($allowedGroups, function($g) {
                return !empty($g) && trim((string)$g) !== '';
            });
            $allowedGroups = array_values($allowedGroups); // Перенумеровываем индексы
            
            // Сохраняем очищенные группы обратно в тест для отображения
            $test['allowed_groups'] = $allowedGroups;
            
            // Обработка allowed_teachers
            $allowedTeachers = $test['allowed_teachers'] ?? [];
            if (!is_array($allowedTeachers)) {
                $allowedTeachers = [];
            }
            // Фильтруем и нормализуем ID преподавателей
            $allowedTeachers = array_filter($allowedTeachers, function($t) {
                return !empty($t) && is_numeric($t);
            });
            $allowedTeachers = array_values(array_map('intval', $allowedTeachers));
            
            // Получаем данные преподавателей по их ID
            $teacherData = [];
            foreach ($allowedTeachers as $teacherId) {
                foreach ($allTeachers as $teacher) {
                    if ((int)($teacher['id'] ?? 0) === $teacherId) {
                        $teacherData[] = [
                            'id' => $teacher['id'],
                            'full_name' => $teacher['full_name'] ?? '',
                        ];
                        break;
                    }
                }
            }
            
            // Сохраняем данные преподавателей для отображения
            $test['teacher_data'] = $teacherData;
            
            // Подсчитываем студентов в группах
            $studentsInGroups = [];
            if (!empty($allowedGroups)) {
                // Функция нормализации для сравнения
                $normalize = function($str) {
                    return mb_strtolower(trim((string)$str));
                };
                
                foreach ($allStudents as $student) {
                    $studentGroup = $student['group'] ?? null;
                    if ($studentGroup) {
                        $normalizedStudentGroup = $normalize($studentGroup);
                        foreach ($allowedGroups as $allowedGroup) {
                            $normalizedAllowedGroup = $normalize($allowedGroup);
                            if ($normalizedStudentGroup === $normalizedAllowedGroup) {
                        $studentsInGroups[] = $student;
                                break;
                            }
                        }
                    }
                }
            } else {
                $studentsInGroups = $allStudents;
            }
            
            // Используем предзагруженные результаты вместо загрузки каждый раз
            $testResults = $allTestResults[$testId] ?? [];
            $passedStudentIds = [];
            foreach ($testResults as $result) {
                $studentId = $result['student_id'] ?? null;
                if ($studentId) {
                    $passedStudentIds[$studentId] = true;
                }
            }
            
            $passedCount = 0;
            foreach ($studentsInGroups as $student) {
                $studentId = $student['student_id'] ?? $student['id'] ?? null;
                if ($studentId && isset($passedStudentIds[$studentId])) {
                    $passedCount++;
                }
            }
            
            $test['stats'] = [
                'total_students' => count($studentsInGroups),
                'passed_count' => $passedCount,
                'groups_count' => count($allowedGroups),
            ];
        }
        unset($test);

        require __DIR__ . '/../../../views/admin/tests/index.php';
    }

    public function createForm(): void
    {
        $this->ensureAuth();

        $title = 'Создать новый тест';
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_error']);

        require __DIR__ . '/../../../views/admin/tests/create.php';
    }

    public function store(): void
    {
        $this->ensureAuth();

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $questionsIn = $_POST['questions'] ?? [];

        $questions = [];

        if (is_array($questionsIn)) {
            foreach ($questionsIn as $q) {
                $text = trim($q['text'] ?? '');
                if ($text === '') {
                    continue;
                }
                
                // Получаем явно указанный тип вопроса
                $questionType = trim($q['type'] ?? '');
                
                // Валидация типа вопроса
                $allowedTypes = ['multiple_choice', 'multiple_select', 'text', 'scale'];
                if (!in_array($questionType, $allowedTypes)) {
                    // Если тип не указан или невалиден, определяем автоматически
                    $questionType = '';
                }
                
                $options = [];
                if (isset($q['options']) && is_array($q['options'])) {
                    foreach ($q['options'] as $opt) {
                        $optText = trim($opt['text'] ?? '');
                        if ($optText !== '') {
                            $optionData = [
                                'text' => $optText,
                                'is_other' => !empty($opt['is_other'] ?? false),
                            ];
                            // Добавляем балл, если указан
                            if (isset($opt['score']) && $opt['score'] !== '') {
                                $optionData['score'] = (int)$opt['score'];
                            }
                            // Добавляем цвет, если указан
                            if (isset($opt['color']) && !empty(trim($opt['color']))) {
                                $optionData['color'] = trim($opt['color']);
                            }
                            $options[] = $optionData;
                        }
                    }
                }
                
                // Если тип не указан, определяем автоматически
                if ($questionType === '') {
                if (empty($options)) {
                        $questionType = 'text';
                    } else {
                        $questionType = 'multiple_choice';
                    }
                }
                
                // Для типов text и scale варианты не нужны
                if ($questionType === 'text' || $questionType === 'scale') {
                    $questions[] = [
                        'text' => $text,
                        'type' => $questionType,
                    ];
                } else {
                    // Для типов с вариантами проверяем наличие вариантов
                    if (empty($options)) {
                        // Если нет вариантов, но тип требует их, используем text
                        $questions[] = [
                            'text' => $text,
                        'type' => 'text',
                    ];
                } else {
                $questions[] = [
                    'text' => $text,
                            'type' => $questionType,
                        'options' => $options,
                ];
                    }
                }
            }
        }

        if ($title === '' || count($questions) === 0) {
            $_SESSION['admin_error'] = 'Нужно указать название теста и минимум один вопрос.';
            header('Location: /admin/tests/create');
            exit;
        }

        // Обработка конфигурации расчета результатов
        $calculationConfig = $this->parseCalculationConfig($_POST);

        $test = [
            'title'       => $title,
            'description' => $description,
            'category'    => $category,
            'questions'   => $questions,
            'created_at'  => date('Y-m-d H:i'),
            'allowed_groups' => [], // По умолчанию тест доступен всем
            'allowed_faculties' => [],
            'open_for_all' => true, // По умолчанию новый тест открыт для всех
            'calculation_config' => $calculationConfig,
        ];

        $created = $this->storage->create($test);

        $_SESSION['admin_flash'] = 'Тест «' . $created['title'] . '» создан.';
        header('Location: /admin/tests/show?id=' . $created['id']);
        exit;
    }

    public function show(): void
    {
        $this->ensureAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            http_response_code(400);
            echo 'Некорректный ID теста.';
            return;
        }

        $test = $this->storage->findById($id);

        if ($test === null) {
            http_response_code(404);
            echo 'Тест не найден.';
            return;
        }

        $title = 'Тест: ' . ($test['title'] ?? '');
        $flash = $_SESSION['admin_flash'] ?? null;
        unset($_SESSION['admin_flash']);

        // Получаем статистику прохождения теста (только если группы выбраны)
        $resultsStorage = new \App\Services\ResultsStorage();
        $studentStorage = new \App\Services\StudentStorage();
        
        // Получаем данные о преподавателях
        $teacherService = new \App\Services\TeacherService();
        $allTeachers = $teacherService->getAll();
        
        $testResults = $resultsStorage->getResultsByTestId($id);
        $allowedGroups = $test['allowed_groups'] ?? [];
        $allowedFaculties = $test['allowed_faculties'] ?? [];
        $allowedTeachers = $test['allowed_teachers'] ?? [];
        if (!is_array($allowedTeachers)) {
            $allowedTeachers = [];
        }
        // Фильтруем ID преподавателей
        $allowedTeachers = array_filter($allowedTeachers, function($t) {
            return !empty($t) && is_numeric($t);
        });
        $allowedTeachers = array_values(array_map('intval', $allowedTeachers));
        
        // Статистику показываем ТОЛЬКО если группы или факультеты выбраны
        // Если ничего не выбрано - тест еще не отправлялся, статистики нет
        $totalStudents = 0;
        $passedCount = 0;
        $notPassedStudents = [];
        
        if (!empty($allowedGroups) || !empty($allowedFaculties)) {
            // Получаем список студентов из выбранных групп/факультетов
            $allStudents = $studentStorage->getAll();
            $studentsInGroups = [];
            $studentsWhoPassed = [];
            
            // Собираем ID студентов, которые прошли тест
            foreach ($testResults as $result) {
                $studentId = $result['student_id'] ?? null;
                if ($studentId) {
                    $studentsWhoPassed[$studentId] = true;
                }
            }
            
            // Функция нормализации для сравнения
            $normalize = function($str) {
                return mb_strtolower(trim((string)$str));
            };
            
            // Фильтруем студентов по выбранным группам/факультетам
            foreach ($allStudents as $student) {
                $studentGroup = $student['group'] ?? null;
                $studentFaculty = $student['faculty'] ?? null;
                $includeStudent = false;
                
                // Если указаны группы - проверяем по группам
                if (!empty($allowedGroups)) {
                    $normalizedAllowedGroups = array_map($normalize, $allowedGroups);
                    $normalizedStudentGroup = $studentGroup ? $normalize($studentGroup) : '';
                    if (in_array($normalizedStudentGroup, $normalizedAllowedGroups, true)) {
                        $includeStudent = true;
                    }
                }
                // Если группы не указаны, но указаны факультеты - проверяем по факультетам
                elseif (!empty($allowedFaculties)) {
                    $normalizedAllowedFaculties = array_map($normalize, $allowedFaculties);
                    $normalizedStudentFaculty = $studentFaculty ? $normalize($studentFaculty) : '';
                    if (in_array($normalizedStudentFaculty, $normalizedAllowedFaculties, true)) {
                        $includeStudent = true;
                    }
                }
                
                if ($includeStudent) {
                    $studentsInGroups[] = $student;
                }
            }
            
            $totalStudents = count($studentsInGroups);
            
            // Подсчитываем прошедших и не прошедших
            foreach ($studentsInGroups as $student) {
                $studentId = $student['student_id'] ?? $student['id'] ?? null;
                if ($studentId && isset($studentsWhoPassed[$studentId])) {
                    $passedCount++;
                } else {
                    $notPassedStudents[] = $student;
                }
            }
        }
        
        // Обработка преподавателей
        $teacherResults = [];
        $totalTeachers = 0;
        $passedTeachersCount = 0;
        $notPassedTeachers = [];

        if (!empty($allowedTeachers)) {
            // Получаем результаты тестов от преподавателей (student_id будет 'teacher_X')
            foreach ($testResults as $result) {
                $studentId = (string)($result['student_id'] ?? '');
                if (strpos($studentId, 'teacher_') === 0) {
                    $teacherId = (int)str_replace('teacher_', '', $studentId);
                    if (in_array($teacherId, $allowedTeachers)) {
                        $teacherResults[$teacherId] = $result;
                    }
                }
            }
            
            // Получаем данные преподавателей
            foreach ($allowedTeachers as $teacherId) {
                $teacher = null;
                foreach ($allTeachers as $t) {
                    if ((int)($t['id'] ?? 0) === $teacherId) {
                        $teacher = $t;
                        break;
                    }
                }
                
                if ($teacher) {
                    $totalTeachers++;
                    $hasResult = isset($teacherResults[$teacherId]);
                    
                    if ($hasResult) {
                        $passedTeachersCount++;
                    } else {
                        $notPassedTeachers[] = [
                            'id' => $teacher['id'],
                            'full_name' => $teacher['full_name'] ?? '',
                            'department' => $teacher['department'] ?? '',
                        ];
                    }
                }
            }
        }

        require __DIR__ . '/../../../views/admin/tests/show.php';
    }

    public function editForm(): void
    {
        $this->ensureAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['admin_error'] = 'Noto\'g\'ri test ID.';
            header('Location: /admin/tests');
            exit;
        }

        $test = $this->storage->findById($id);

        if ($test === null) {
            $_SESSION['admin_error'] = 'Test topilmadi.';
            header('Location: /admin/tests');
            exit;
        }

        $title = 'Testni tahrirlash';
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_error']);

        require __DIR__ . '/../../../views/admin/tests/edit.php';
    }

    public function update(): void
    {
        $this->ensureAuth();

        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;

        if ($id <= 0) {
            $_SESSION['admin_error'] = 'Noto\'g\'ri test ID.';
            header('Location: /admin/tests');
            exit;
        }

        $title       = trim($_POST['title'] ?? '');
        $description = trim($_POST['description'] ?? '');
        $category    = trim($_POST['category'] ?? '');
        $questionsIn = $_POST['questions'] ?? [];

        $questions = [];

        if (is_array($questionsIn)) {
            foreach ($questionsIn as $q) {
                $text = trim($q['text'] ?? '');
                if ($text === '') {
                    continue;
                }
                
                // Получаем явно указанный тип вопроса
                $questionType = trim($q['type'] ?? '');
                
                // Валидация типа вопроса
                $allowedTypes = ['multiple_choice', 'multiple_select', 'text', 'scale'];
                if (!in_array($questionType, $allowedTypes)) {
                    // Если тип не указан или невалиден, определяем автоматически
                    $questionType = '';
                }
                
                $options = [];
                if (isset($q['options']) && is_array($q['options'])) {
                    foreach ($q['options'] as $opt) {
                        $optText = trim($opt['text'] ?? '');
                        if ($optText !== '') {
                            $optionData = [
                                'text' => $optText,
                                'is_other' => !empty($opt['is_other'] ?? false),
                            ];
                            // Добавляем балл, если указан
                            if (isset($opt['score']) && $opt['score'] !== '') {
                                $optionData['score'] = (int)$opt['score'];
                            }
                            // Добавляем цвет, если указан
                            if (isset($opt['color']) && !empty(trim($opt['color']))) {
                                $optionData['color'] = trim($opt['color']);
                            }
                            $options[] = $optionData;
                        }
                    }
                }
                
                // Если тип не указан, определяем автоматически
                if ($questionType === '') {
                if (empty($options)) {
                        $questionType = 'text';
                    } else {
                        $questionType = 'multiple_choice';
                    }
                }
                
                // Для типов text и scale варианты не нужны
                if ($questionType === 'text' || $questionType === 'scale') {
                    $questions[] = [
                        'text' => $text,
                        'type' => $questionType,
                    ];
                } else {
                    // Для типов с вариантами проверяем наличие вариантов
                    if (empty($options)) {
                        // Если нет вариантов, но тип требует их, используем text
                        $questions[] = [
                            'text' => $text,
                        'type' => 'text',
                    ];
                } else {
                    $questions[] = [
                        'text' => $text,
                            'type' => $questionType,
                        'options' => $options,
                    ];
                    }
                }
            }
        }

        if ($title === '' || count($questions) === 0) {
            $_SESSION['admin_error'] = 'Test nomi va kamida bitta savol kerak.';
            header('Location: /admin/tests/edit?id=' . $id);
            exit;
        }

        // Сохраняем существующие группы (не обновляем их при редактировании теста)
        $existingTest = $this->storage->findById($id);
        $allowedGroups = $existingTest['allowed_groups'] ?? [];
        $allowedFaculties = $existingTest['allowed_faculties'] ?? [];

        // Обработка конфигурации расчета результатов
        $calculationConfig = $this->parseCalculationConfig($_POST);

        $test = [
            'title'            => $title,
            'description'      => $description,
            'category'         => $category,
            'questions'        => $questions,
            'allowed_groups'   => $allowedGroups,
            'allowed_faculties' => $allowedFaculties,
            'calculation_config' => $calculationConfig,
        ];

        $updated = $this->storage->update($id, $test);

        if (!$updated) {
            $_SESSION['admin_error'] = 'Test topilmadi.';
            header('Location: /admin/tests');
            exit;
        }

        $_SESSION['admin_flash'] = 'Test «' . $title . '» yangilandi.';
        header('Location: /admin/tests/show?id=' . $id);
        exit;
    }

    public function delete(): void
    {
        $this->ensureAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['admin_error'] = 'Noto\'g\'ri test ID.';
            header('Location: /admin/tests');
            exit;
        }

        $deleted = $this->storage->delete($id);

        if ($deleted) {
            $_SESSION['admin_flash'] = 'Test o\'chirildi.';
        } else {
            $_SESSION['admin_error'] = 'Test topilmadi.';
        }

        header('Location: /admin/tests');
        exit;
    }

    /**
     * Форма выбора групп для теста
     */
    public function selectGroupsForm(): void
    {
        $this->ensureAuth();

        $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

        if ($id <= 0) {
            $_SESSION['admin_error'] = 'Noto\'g\'ri test ID.';
            header('Location: /admin/tests');
            exit;
        }

        $test = $this->storage->findById($id);

        if ($test === null) {
            $_SESSION['admin_error'] = 'Test topilmadi.';
            header('Location: /admin/tests');
            exit;
        }

        // Получаем список факультетов для выбора групп
        $hemisApi = new HemisApi();
        $faculties = $hemisApi->getFaculties();

        $title = 'Test uchun guruhlarni tanlash';
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_error']);

        require __DIR__ . '/../../../views/admin/tests/select-groups.php';
    }

    /**
     * Сохранение выбранных групп для теста
     */
    public function updateGroups(): void
    {
        $this->ensureAuth();

        $id = isset($_POST['test_id']) ? (int)$_POST['test_id'] : 0;

        if ($id <= 0) {
            $_SESSION['admin_error'] = 'Noto\'g\'ri test ID.';
            header('Location: /admin/tests');
            exit;
        }

        // Проверяем наличие дубликатов ID перед поиском
        $allTestsWithId = $this->storage->findAllById($id);

        if (empty($allTestsWithId)) {
            $_SESSION['admin_error'] = 'Test topilmadi.';
            header('Location: /admin/tests');
            exit;
        }

        // Если найдено несколько тестов с одинаковым ID, используем первый
        // (дубликаты будут автоматически исправлены при обновлении через TestStorage)
        $test = $allTestsWithId[0];
        
        if (count($allTestsWithId) > 1 && ($_ENV['APP_ENV'] ?? 'production') === 'development') {
            error_log('TestController::updateGroups() - WARNING: Found ' . count($allTestsWithId) . ' tests with ID ' . $id . '. Using the first one: "' . ($test['title'] ?? 'Unknown') . '"');
        }

        // Получаем выбранные группы (всегда полностью заменяем, даже если пусто)
        $allowedGroups = [];
        $allowedFaculties = [];
        
        // Если выбрана опция "Открыть для всех", очищаем группы и факультеты
        $openForAll = isset($_POST['open_for_all']) && $_POST['open_for_all'] === '1';
        
        if (!$openForAll) {
        if (isset($_POST['allowed_groups']) && is_array($_POST['allowed_groups'])) {
            $allowedGroups = array_values(array_filter(array_map('trim', $_POST['allowed_groups']), function($g) {
                return $g !== '' && trim($g) !== '';
            }));
        }
        
        if (isset($_POST['allowed_faculties']) && is_array($_POST['allowed_faculties'])) {
            $allowedFaculties = array_values(array_filter(array_map('trim', $_POST['allowed_faculties']), function($f) {
                return $f !== '' && trim($f) !== '';
            }));
            }
        }

        // Логируем для отладки
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            error_log('TestController::updateGroups() - Test ID: ' . $id);
            error_log('TestController::updateGroups() - Test Title: ' . ($test['title'] ?? 'Unknown'));
            error_log('TestController::updateGroups() - POST allowed_groups: ' . json_encode($_POST['allowed_groups'] ?? []));
            error_log('TestController::updateGroups() - POST allowed_faculties: ' . json_encode($_POST['allowed_faculties'] ?? []));
            error_log('TestController::updateGroups() - Final Allowed Groups: ' . json_encode($allowedGroups));
            error_log('TestController::updateGroups() - Final Allowed Faculties: ' . json_encode($allowedFaculties));
        }

        // ВСЕГДА полностью заменяем группы и факультеты (даже если пусто)
        // Это гарантирует, что старые группы будут удалены
        $test['allowed_groups'] = $allowedGroups;
        $test['allowed_faculties'] = $allowedFaculties;
        // Сохраняем флаг "открыт для всех" для правильного отображения
        $test['open_for_all'] = $openForAll;
        
        // Определяем, есть ли ограничения
        $hasRestrictions = !empty($allowedGroups) || !empty($allowedFaculties);

        $updated = $this->storage->update($id, $test);

        if (!$updated) {
            $_SESSION['admin_error'] = 'Xatolik yuz berdi.';
            header('Location: /admin/tests/select-groups?id=' . $id);
            exit;
        }

        // Логируем успешное сохранение только в режиме разработки
        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
            error_log('TestController::updateGroups() - Successfully updated test ID ' . $id . ' with ' . count($allowedGroups) . ' groups');
        }

        if ($openForAll) {
            $_SESSION['admin_flash'] = 'Test barcha talabalar uchun ochiq qilindi.';
        } elseif ($hasRestrictions) {
            $_SESSION['admin_flash'] = 'Guruhlar saqlandi. Test endi tanlangan guruhlarga ochiq.';
        } else {
            $_SESSION['admin_flash'] = 'Test saqlandi. Guruhlar tanlanmagan.';
        }
        header('Location: /admin/tests/show?id=' . $id);
        exit;
    }

    /**
     * Парсинг конфигурации расчета результатов из POST данных
     * 
     * @param array<string, mixed> $post POST данные
     * @return array<string, mixed>|null Конфигурация расчета или null
     */
    private function parseCalculationConfig(array $post): ?array
    {
        $calculationType = trim($post['calculation_type'] ?? '');
        
        // Если тип расчета не указан, возвращаем null
        if (empty($calculationType) || $calculationType === 'none') {
            return null;
        }
        
        $config = [
            'type' => $calculationType,
        ];
        
        // Для типов sum, average, categories - обрабатываем категории и вопросы со шкалой
        if (in_array($calculationType, ['sum', 'average', 'categories'], true)) {
            // Обработка категорий
            $categories = [];
            $categoryNames = $post['category_names'] ?? [];
            $categoryMins = $post['category_mins'] ?? [];
            $categoryMaxs = $post['category_maxs'] ?? [];
            $categoryDescriptions = $post['category_descriptions'] ?? [];
            
            if (is_array($categoryNames)) {
                foreach ($categoryNames as $index => $name) {
                    $name = trim($name);
                    if (!empty($name)) {
                        $min = isset($categoryMins[$index]) ? (float)$categoryMins[$index] : 0;
                        $max = isset($categoryMaxs[$index]) ? (float)$categoryMaxs[$index] : PHP_FLOAT_MAX;
                        $description = isset($categoryDescriptions[$index]) ? trim($categoryDescriptions[$index]) : '';
                        
                        $categories[] = [
                            'name' => $name,
                            'min' => $min,
                            'max' => $max,
                            'description' => $description,
                        ];
                    }
                }
            }
            
            if (!empty($categories)) {
                $config['categories'] = $categories;
            }
            
            // Обработка вопросов со шкалой
            $scaleQuestions = $post['scale_questions'] ?? [];
            if (is_array($scaleQuestions) && !empty($scaleQuestions)) {
                $scaleQuestionIndices = [];
                foreach ($scaleQuestions as $qIndex) {
                    $qIndex = (int)$qIndex;
                    if ($qIndex >= 0) {
                        $scaleQuestionIndices[] = $qIndex;
                    }
                }
                if (!empty($scaleQuestionIndices)) {
                    $config['scale_questions'] = $scaleQuestionIndices;
                }
            }
        }
        
        // Для типа multi_scale - обрабатываем несколько шкал
        if ($calculationType === 'multi_scale') {
            $scales = [];
            $scalesData = $post['scales'] ?? [];
            
            if (is_array($scalesData)) {
                foreach ($scalesData as $scaleId => $scaleData) {
                    $scaleName = trim($scaleData['name'] ?? '');
                    if (empty($scaleName)) {
                        continue;
                    }
                    
                    // Обработка индексов вопросов
                    $questionIndices = [];
                    if (isset($scaleData['question_indices']) && is_array($scaleData['question_indices'])) {
                        foreach ($scaleData['question_indices'] as $qIndex) {
                            $qIndex = (int)$qIndex;
                            if ($qIndex >= 0) {
                                $questionIndices[] = $qIndex;
                            }
                        }
                    }
                    
                    // Обработка категорий для шкалы
                    $categories = [];
                    if (isset($scaleData['categories']) && is_array($scaleData['categories'])) {
                        foreach ($scaleData['categories'] as $categoryData) {
                            $categoryName = trim($categoryData['name'] ?? '');
                            if (!empty($categoryName)) {
                                $categories[] = [
                                    'name' => $categoryName,
                                    'min' => isset($categoryData['min']) ? (float)$categoryData['min'] : 0,
                                    'max' => isset($categoryData['max']) ? (float)$categoryData['max'] : PHP_FLOAT_MAX,
                                    'description' => isset($categoryData['description']) ? trim($categoryData['description']) : '',
                                ];
                            }
                        }
                    }
                    
                    if (!empty($questionIndices) && !empty($categories)) {
                        $scales[] = [
                            'name' => $scaleName,
                            'question_indices' => $questionIndices,
                            'categories' => $categories,
                        ];
                    }
                }
            }
            
            if (!empty($scales)) {
                $config['scales'] = $scales;
            } else {
                // Если нет валидных шкал, возвращаем null
                return null;
            }
        }
        
        // Для типа percentage - обрабатываем интерпретации ответов
        if ($calculationType === 'percentage') {
            $answerInterpretations = [];
            $answerKeys = $post['answer_keys'] ?? [];
            $answerCategories = $post['answer_categories'] ?? [];
            $answerDescriptions = $post['answer_descriptions'] ?? [];
            
            if (is_array($answerKeys)) {
                foreach ($answerKeys as $index => $key) {
                    $key = trim($key);
                    if (!empty($key)) {
                        $category = isset($answerCategories[$index]) ? trim($answerCategories[$index]) : $key;
                        $description = isset($answerDescriptions[$index]) ? trim($answerDescriptions[$index]) : '';
                        
                        $answerInterpretations[$key] = [
                            'category' => $category,
                            'description' => $description,
                        ];
                    }
                }
            }
            
            if (!empty($answerInterpretations)) {
                $config['answer_interpretations'] = $answerInterpretations;
            }
        }
        
        return $config;
    }

    /** Показать форму выбора преподавателей для теста */
    public function selectTeachers(): void
    {
        $this->ensureAuth();

        $testId = (int)($_GET['id'] ?? 0);
        if ($testId <= 0) {
            http_response_code(404);
            echo '404 - Test topilmadi';
            exit;
        }

        $test = $this->storage->findById($testId);
        if (!$test) {
            http_response_code(404);
            echo '404 - Test topilmadi';
            exit;
        }

        // Получаем всех преподавателей
        $teacherService = new \App\Services\TeacherService();
        $allTeachers = $teacherService->getAll();

        // Сортируем по имени
        usort($allTeachers, function ($a, $b) {
            return strcmp($a['full_name'] ?? '', $b['full_name'] ?? '');
        });

        // Получаем текущие назначенные преподаватели
        $allowedTeachers = $test['allowed_teachers'] ?? [];

        echo \App\View::render('admin/tests/select-teachers', [
            'title' => 'O\'qituvchilarni tanlash',
            'test' => $test,
            'allTeachers' => $allTeachers,
            'allowedTeachers' => $allowedTeachers,
        ]);
    }

    /** Обновить список преподавателей для теста */
    public function updateTeachers(): void
    {
        $this->ensureAuth();

        $testId = (int)($_POST['test_id'] ?? 0);
        if ($testId <= 0) {
            $_SESSION['admin_flash'] = 'Noto\'g\'ri test ID.';
            header('Location: /admin/tests');
            exit;
        }

        $test = $this->storage->findById($testId);
        if (!$test) {
            $_SESSION['admin_flash'] = 'Test topilmadi.';
            header('Location: /admin/tests');
            exit;
        }

        // Получаем выбранных преподавателей
        $selectedTeachers = $_POST['teachers'] ?? [];
        if (!is_array($selectedTeachers)) {
            $selectedTeachers = [];
        }

        // Конвертируем в integers
        $selectedTeachers = array_map('intval', $selectedTeachers);
        $selectedTeachers = array_filter($selectedTeachers, function ($id) {
            return $id > 0;
        });
        $selectedTeachers = array_values($selectedTeachers);

        // Обновляем тест
        $test['allowed_teachers'] = $selectedTeachers;
        $this->storage->update($testId, $test);

        $_SESSION['admin_flash'] = count($selectedTeachers) > 0
            ? 'O\'qituvchilar muvaffaqiyatli tayinlandi!'
            : 'Barcha o\'qituvchilar olib tashlandi.';

        header('Location: /admin/tests/show?id=' . $testId);
        exit;
    }
}
