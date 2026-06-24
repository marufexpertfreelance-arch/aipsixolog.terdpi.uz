<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\Services\ExcelExportService;
use App\Services\ResultsStorage;
use App\Services\StudentStorage;
use App\Services\TestStorage;
use App\Services\TestCalculationService;
use App\View;

class ResultsController
{
    private ResultsStorage $resultsStorage;
    private TestStorage $testStorage;

    public function __construct()
    {
        $this->resultsStorage = new ResultsStorage();
        $this->testStorage = new TestStorage();
    }

    private function ensureAuth(): void
    {
        if (!isset($_SESSION['admin_logged_in']) && !isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    /** Главная страница результатов - общая таблица */
    public function index(): void
    {
        $this->ensureAuth();

        // Фильтры
        $filterType = $_GET['type'] ?? 'all'; // all, custom, eysenck, iq
        $filterTestId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
        $filterStudentId = $_GET['student_id'] ?? '';
        $filterGroup = $_GET['group'] ?? '';
        $filterFaculty = $_GET['faculty'] ?? '';
        $filterSpecialty = $_GET['specialty'] ?? '';
        $filterDateFrom = $_GET['date_from'] ?? '';
        $filterDateTo = $_GET['date_to'] ?? '';
        $filterCategory = $_GET['category'] ?? '';

        // Получаем все результаты
        $allResults = $this->resultsStorage->getAllResults();

        // Применяем фильтры
        $filteredResults = $allResults;

        // ВАЖНО: Если выбран конкретный тест (test_id > 0), фильтруем ТОЛЬКО по test_id
        // Это должно быть приоритетнее, чем фильтр по типу
        if ($filterTestId > 0) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterTestId) {
                $resultTestId = $result['test_id'] ?? 0;
                // Преобразуем оба значения в строку для строгого сравнения
                // Для обычных тестов test_id - число, для Айзенка может быть строка 'eysenck'
                $resultTestIdStr = (string)$resultTestId;
                $filterTestIdStr = (string)$filterTestId;
                // Строгое сравнение строк
                return $resultTestIdStr === $filterTestIdStr;
            });
            // Пересобираем массив после фильтрации по test_id
            $filteredResults = array_values($filteredResults);
        } elseif ($filterType !== 'all') {
            // Если конкретный тест не выбран, применяем фильтр по типу
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterType) {
                return ($result['test_type'] ?? '') === $filterType;
            });
        }

        if (!empty($filterStudentId)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterStudentId) {
                return ($result['student_id'] ?? '') === $filterStudentId;
            });
        }

        if (!empty($filterDateFrom) || !empty($filterDateTo)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterDateFrom, $filterDateTo) {
                $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                if (empty($date)) {
                    return false;
                }
                if (!empty($filterDateFrom) && $date < $filterDateFrom) {
                    return false;
                }
                if (!empty($filterDateTo) && $date > $filterDateTo) {
                    return false;
                }
                return true;
            });
        }

        // Пересобираем массив после фильтрации
        $filteredResults = array_values($filteredResults);

        // Фильтруем результаты удаленных тестов - показываем только результаты существующих тестов
        // Делаем это ПЕРЕД добавлением информации о группах для оптимизации
        $filteredResults = array_filter($filteredResults, function ($result) {
            $resultTestId = $result['test_id'] ?? 0;
            $testType = $result['test_type'] ?? '';
            
            // Тест Люшера всегда показываем (test_type = 'lusher') - проверяем первым
            if ($testType === 'lusher' || $resultTestId === 'lusher') {
                return true;
            }
            
            // Тест Айзенка всегда показываем (test_id = 'eysenck' или 0/-1)
            if ($testType === 'eysenck' || $resultTestId === 'eysenck' || $resultTestId === 0 || $resultTestId === -1) {
                return true;
            }
            
            // IQ тест всегда показываем (test_id = 'iq')
            if ($testType === 'iq' || $resultTestId === 'iq') {
                return true;
            }
            
            // Для обычных тестов проверяем через findById для надежности
            $testIdInt = (int)$resultTestId;
            if ($testIdInt > 0) {
                $test = $this->testStorage->findById($testIdInt);
                return $test !== null;
            }
            
            // Если test_id не число и не 'eysenck' и не 'iq' и не 'lusher' - скрываем
            return false;
        });
        $filteredResults = array_values($filteredResults);

        // Получаем информацию о студентах из StudentStorage для получения групп
        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();
        
        // Получаем данные преподавателей
        $teacherService = new \App\Services\TeacherService();
        $allTeachers = $teacherService->getAll();
        
        // Создаем массивы соответствия student_id => данные для быстрого поиска
        $studentGroups = [];
        $studentFaculties = [];
        $studentSpecialties = [];
        foreach ($allStudents as $student) {
            $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
            if (!empty($studentId)) {
                $studentGroups[$studentId] = $student['group'] ?? '';
                $studentFaculties[$studentId] = $student['faculty'] ?? '';
                $studentSpecialties[$studentId] = $student['specialty'] ?? '';
            }
        }
        
        // Создаем массивы соответствия teacher_id => данные для быстрого поиска
        $teacherData = [];
        foreach ($allTeachers as $teacher) {
            $teacherId = 'teacher_' . (string)($teacher['id'] ?? '');
            $teacherData[$teacherId] = [
                'id' => $teacher['id'],
                'full_name' => $teacher['full_name'] ?? '',
                'department' => $teacher['department'] ?? '',
            ];
        }
        
        // Добавляем информацию о группе, факультете и направлении к каждому результату
        foreach ($filteredResults as &$result) {
            $studentId = (string)($result['student_id'] ?? '');
            
            // Проверяем, является ли это преподавателем
            if (strpos($studentId, 'teacher_') === 0) {
                $teacherInfo = $teacherData[$studentId] ?? null;
                if ($teacherInfo) {
                    $result['user_type'] = 'teacher';
                    $result['user_name'] = $teacherInfo['full_name'];
                    $result['user_department'] = $teacherInfo['department'];
                    $result['user_id'] = $teacherInfo['id'];
                    $result['student_group'] = '';
                    $result['student_faculty'] = $teacherInfo['department'];
                    $result['student_specialty'] = '';
                } else {
                    // Преподаватель не найден
                    $result['user_type'] = 'teacher';
                    $result['user_name'] = 'Noma\'lum o\'qituvchi';
                    $result['user_department'] = '';
                    $result['student_group'] = '';
                    $result['student_faculty'] = '';
                    $result['student_specialty'] = '';
                }
            } else {
                // Это студент
                $result['user_type'] = 'student';
                $result['user_name'] = $result['student_name'] ?? '';
                $result['user_id'] = $studentId;
                $result['student_group'] = $studentGroups[$studentId] ?? '';
                $result['student_faculty'] = $studentFaculties[$studentId] ?? '';
                $result['student_specialty'] = $studentSpecialties[$studentId] ?? '';
            }
        }
        unset($result); // Снимаем ссылку

        // На /admin/results показываем только студентов. Преподаватели должны быть в /admin/results/teachers
        $filteredResults = array_values(array_filter($filteredResults, function ($result) {
            return ($result['user_type'] ?? 'student') === 'student';
        }));
        
        // Применяем фильтр по группе, если указан (только для студентов)
        if (!empty($filterGroup)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterGroup) {
                $userType = $result['user_type'] ?? 'student';
                // Преподаватели всегда проходят фильтр по группе (у них нет группы)
                if ($userType === 'teacher') {
                    return false; // Скрываем преподавателей при фильтрации по группе
                }
                return ($result['student_group'] ?? '') === $filterGroup;
            });
            $filteredResults = array_values($filteredResults);
        }
        
        // Применяем фильтр по факультету, если указан
        if (!empty($filterFaculty)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterFaculty) {
                return ($result['student_faculty'] ?? '') === $filterFaculty;
            });
            $filteredResults = array_values($filteredResults);
        }
        
        // Применяем фильтр по направлению, если указан
        if (!empty($filterSpecialty)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterSpecialty) {
                return ($result['student_specialty'] ?? '') === $filterSpecialty;
            });
            $filteredResults = array_values($filteredResults);
        }
        
        // Получаем список уникальных групп, факультетов и направлений для фильтра
        $uniqueGroups = [];
        $uniqueFaculties = [];
        $uniqueSpecialties = [];
        foreach ($allStudents as $student) {
            $group = $student['group'] ?? '';
            if (!empty($group) && !in_array($group, $uniqueGroups)) {
                $uniqueGroups[] = $group;
            }
            $faculty = $student['faculty'] ?? '';
            if (!empty($faculty) && !in_array($faculty, $uniqueFaculties)) {
                $uniqueFaculties[] = $faculty;
            }
            $specialty = $student['specialty'] ?? '';
            if (!empty($specialty) && !in_array($specialty, $uniqueSpecialties)) {
                $uniqueSpecialties[] = $specialty;
            }
        }
        sort($uniqueGroups);
        sort($uniqueFaculties);
        sort($uniqueSpecialties);

        // Получаем список тестов для фильтра
        $tests = $this->testStorage->getAll();
        
        // Собираем все уникальные категории тестов из поля category
        $uniqueCategories = [];
        foreach ($tests as $test) {
            $category = $test['category'] ?? '';
            if (!empty($category) && !in_array($category, $uniqueCategories)) {
                $uniqueCategories[] = $category;
            }
        }
        sort($uniqueCategories);
        
        // Применяем фильтр по категории теста, если указан
        if (!empty($filterCategory)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterCategory, $tests) {
                $testType = $result['test_type'] ?? '';
                
                // Для специальных тестов (IQ, Айзенк, Люшер) не фильтруем по категории теста
                // так как у них нет категории теста в обычном смысле
                if ($testType === 'iq' || $testType === 'eysenck' || $testType === 'lusher') {
                    return false; // Скрываем специальные тесты при фильтрации по категории
                }
                
                // Для обычных тестов находим тест и проверяем его категорию
                $testId = $result['test_id'] ?? 0;
                if ($testId > 0) {
                    foreach ($tests as $test) {
                        if ((int)($test['id'] ?? 0) === $testId) {
                            $testCategory = $test['category'] ?? '';
                            return $testCategory === $filterCategory;
                        }
                    }
                }
                
                return false;
            });
            $filteredResults = array_values($filteredResults);
        }
        
        $students = $this->resultsStorage->getAllStudents();
        
        // Получаем всех преподавателей для отображения
        $teacherService = new \App\Services\TeacherService();
        $teachers = $teacherService->getAll();

        echo View::render('admin/results/index', [
            'title' => 'Test natijalari',
            'results' => $filteredResults,
            'tests' => $tests,
            'students' => $students,
            'teachers' => $teachers,
            'groups' => $uniqueGroups,
            'faculties' => $uniqueFaculties,
            'specialties' => $uniqueSpecialties,
            'filters' => [
                'type' => $filterType,
                'test_id' => $filterTestId,
                'student_id' => $filterStudentId,
                'group' => $filterGroup,
                'faculty' => $filterFaculty,
                'specialty' => $filterSpecialty,
                'date_from' => $filterDateFrom,
                'date_to' => $filterDateTo,
                'category' => $filterCategory,
            ],
            'categories' => $uniqueCategories,
        ]);
    }

    /** Результаты конкретного теста */
    public function testResults(): void
    {
        $this->ensureAuth();

        $testIdParam = $_GET['id'] ?? '';
        
        // Проверяем, это строка 'iq' или число
        if ($testIdParam === 'iq') {
            // IQ тест
            $test = [
                'id'                => 'iq',
                'title'             => 'IQ Test',
                'description'       => 'Intellektual qobiliyatlarni baholash uchun test',
                'type'              => 'iq',
                'allowed_groups'    => [],
                'allowed_faculties' => [],
            ];
            $results = $this->resultsStorage->getIqResults();
            $testId  = -2; // Специальный ID для IQ теста
        } elseif ($testIdParam === 'lusher') {
            // Тест Люшера
            $test = [
                'id'                => 'lusher',
                'title'             => 'Lyusher Testi',
                'description'       => 'Ranglar orqali psixologik holatni aniqlash uchun test',
                'type'              => 'lusher',
                'allowed_groups'    => [],
                'allowed_faculties' => [],
            ];
            $results = $this->resultsStorage->getLusherResults();
            $testId  = -3; // Специальный ID для теста Люшера
        } elseif ($testIdParam === 'aggression') {
            // Тест Тажовуз (Buss-Darki)
            $test = [
                'id'                => 'aggression',
                'title'             => 'Tajovuz holati tashxisi',
                'description'       => 'Bas-Darki metodikasi bo\'yicha tajovuzkorlik va dushmanlik indekslarini aniqlash',
                'type'              => 'aggression',
                'allowed_groups'    => [],
                'allowed_faculties' => [],
            ];
            $results = $this->resultsStorage->getAggressionResults();
            $testId  = -4; // Специальный ID для теста Тажовуз
        } else {
            $testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

            if ($testId === 0 || $testId === -1) {
                // Тест Айзенка
                $test = [
                    'id'                => 'eysenck',
                    'title'             => 'Temperament',
                    'description'       => 'Temperamentingizni aniqlash uchun psixologik test',
                    'type'              => 'eysenck',
                    'allowed_groups'    => [],
                    'allowed_faculties' => [],
                ];
                $results = $this->resultsStorage->getEysenckResults();
            } else {
                $test = $this->testStorage->findById($testId);
                if ($test === null) {
                    $_SESSION['admin_flash'] = 'Test topilmadi.';
                    header('Location: /admin/results');
                    exit;
                }
                $results = $this->resultsStorage->getResultsByTestId($testId);
            }
        }

        // Получаем разрешенные группы и факультеты
        $allowedGroups    = $test['allowed_groups'] ?? [];
        $allowedFaculties = $test['allowed_faculties'] ?? [];

        // Фильтруем результаты по разрешенным группам/факультетам
        if (!empty($allowedGroups) || !empty($allowedFaculties)) {
            $results = $this->resultsStorage->filterResultsByAllowedGroups($results, $allowedGroups, $allowedFaculties);
        }

        // Получаем статистику с учетом фильтрации
        $isIqTest         = ($testId === -2 || $testIdParam === 'iq');
        $isLusherTest     = ($testId === -3 || $testIdParam === 'lusher');
        $isAggressionTest = ($testId === -4 || $testIdParam === 'aggression');
        
        if ($isIqTest) {
            // Для IQ теста используем специальную статистику
            $statistics = $this->resultsStorage->getIqStatistics();
            // Добавляем диапазон дат
            $iqDateRange = ['first' => null, 'last' => null];
            if (!empty($results)) {
                $dates = [];
                foreach ($results as $result) {
                    $date = $result['completed_at'] ?? null;
                    if ($date) {
                        $dates[] = $date;
                    }
                }
                if (!empty($dates)) {
                    sort($dates);
                    $iqDateRange['first'] = $dates[0];
                    $iqDateRange['last']  = end($dates);
                }
            }
            $statistics['date_range'] = $iqDateRange;
            $categoryStatistics       = null;
        } elseif ($isLusherTest) {
            // Для теста Люшера используем специальную статистику
            $statistics = $this->resultsStorage->getLusherStatistics();
            // Добавляем диапазон дат
            $lusherDateRange = ['first' => null, 'last' => null];
            if (!empty($results)) {
                $dates = [];
                foreach ($results as $result) {
                    $date = $result['completed_at'] ?? null;
                    if ($date) {
                        $dates[] = $date;
                    }
                }
                if (!empty($dates)) {
                    sort($dates);
                    $lusherDateRange['first'] = $dates[0];
                    $lusherDateRange['last']  = end($dates);
                }
            }
            $statistics['date_range'] = $lusherDateRange;
            $categoryStatistics       = null;
        } elseif ($isAggressionTest) {
            // Для теста Тажовуз (Buss-Darki) используем специальную статистику
            $statistics = $this->resultsStorage->getAggressionStatistics();
            // Добавляем диапазон дат и общее количество
            $aggrDateRange = ['first' => null, 'last' => null];
            if (!empty($results)) {
                $dates = [];
                foreach ($results as $result) {
                    $date = $result['completed_at'] ?? null;
                    if ($date) {
                        $dates[] = $date;
                    }
                }
                if (!empty($dates)) {
                    sort($dates);
                    $aggrDateRange['first'] = $dates[0];
                    $aggrDateRange['last']  = end($dates);
                }
            }
            $statistics['date_range']         = $aggrDateRange;
            $statistics['total_completions'] = count($results);
            $categoryStatistics              = null;
        } else {
            $statistics = $this->resultsStorage->getTestStatistics(
                $testId === 0 ? -1 : $testId,
                $allowedGroups,
                $allowedFaculties,
                $test // Передаем данные теста для фильтрации полных результатов
            );
            
            // Также фильтруем результаты для отображения - только полные результаты
            $results = $this->resultsStorage->filterCompleteResults($results, $test);

            // Добавляем информацию о количестве студентов в разрешенных группах
            $studentStorage = new StudentStorage();
            $allStudents    = $studentStorage->getAll();
            
            $totalStudentsInGroups = 0;
            if (!empty($allowedGroups) || !empty($allowedFaculties)) {
                // Функция нормализации для сравнения
                $normalize = function($str) {
                    if ($str === null || $str === '') {
                        return '';
                    }
                    $normalized  = mb_strtolower(trim((string)$str));
                    $apostrophes = [
                        "\xCA\xBC", "\xE2\x80\x99", "\xE2\x80\x98",
                        "\xE2\x80\x9C", "\xE2\x80\x9D", '"', '`'
                    ];
                    $normalized = str_replace($apostrophes, "'", $normalized);
                    $normalized = preg_replace('/\s+/', ' ', $normalized);
                    return trim($normalized);
                };

                if (!empty($allowedGroups)) {
                    $normalizedAllowedGroups = array_map($normalize, $allowedGroups);
                    foreach ($allStudents as $student) {
                        $studentGroup = $student['group'] ?? null;
                        if ($studentGroup && in_array($normalize($studentGroup), $normalizedAllowedGroups, true)) {
                            $totalStudentsInGroups++;
                        }
                    }
                } elseif (!empty($allowedFaculties)) {
                    $normalizedAllowedFaculties = array_map($normalize, $allowedFaculties);
                    foreach ($allStudents as $student) {
                        $studentFaculty = $student['faculty'] ?? null;
                        if ($studentFaculty && in_array($normalize($studentFaculty), $normalizedAllowedFaculties, true)) {
                            $totalStudentsInGroups++;
                        }
                    }
                }
            }

            $statistics['total_students_in_groups'] = $totalStudentsInGroups;

            // Подготовка данных для графиков (распределение по категориям)
            $categoryStatistics = null;
            $calculationService = new TestCalculationService();
            if (!empty($results) && isset($test['calculation_config']) && !empty($test['calculation_config'])) {
                $categoryStatistics = $calculationService->getCategoryStatistics($results, $test);
            }
        }

        echo View::render('admin/results/test', [
            'title'              => 'Test natijalari: ' . ($test['title'] ?? ''),
            'test'               => $test,
            'results'            => $results,
            'statistics'         => $statistics,
            'category_statistics'=> $categoryStatistics,
            'is_iq_test'         => $isIqTest,
            'is_lusher_test'     => $isLusherTest,
            'is_aggression_test' => $isAggressionTest,
        ]);
    }




    /** Список студентов теста (кто сдал, кто нет) */
    public function testStudents(): void
    {
        $this->ensureAuth();

        $testId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;

        if ($testId === 0 || $testId === -1) {
            $_SESSION['admin_flash'] = 'Test topilmadi yoki noto\'g\'ri.';
            header('Location: /admin/results');
            exit;
        }

        $test = $this->testStorage->findById($testId);
        if ($test === null) {
            $_SESSION['admin_flash'] = 'Test topilmadi.';
            header('Location: /admin/results');
            exit;
        }

        $allowedGroups = $test['allowed_groups'] ?? [];
        $allowedFaculties = $test['allowed_faculties'] ?? [];

        // Если группы не выбраны, показываем сообщение
        if (empty($allowedGroups) && empty($allowedFaculties)) {
            $_SESSION['admin_flash'] = 'Bu test uchun guruhlar tanlanmagan.';
            header('Location: /admin/results/test?id=' . $testId);
            exit;
        }

        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();
        
        // Получаем результаты теста
        $testResults = $this->resultsStorage->getResultsByTestId($testId);
        
        // Создаем массив student_id => результат для быстрого поиска
        $resultsByStudentId = [];
        foreach ($testResults as $result) {
            $studentId = (string)($result['student_id'] ?? '');
            if (!empty($studentId)) {
                // Сохраняем последний результат студента
                $resultsByStudentId[$studentId] = $result;
            }
        }

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

        // Фильтруем студентов по выбранным группам/факультетам
        $studentsList = [];
        $passedCount = 0;
        $notPassedCount = 0;

        if (!empty($allowedGroups)) {
            $normalizedAllowedGroups = array_map($normalize, $allowedGroups);
            foreach ($allStudents as $student) {
                $studentGroup = $student['group'] ?? null;
                if ($studentGroup) {
                    $normalizedStudentGroup = $normalize($studentGroup);
                    if (in_array($normalizedStudentGroup, $normalizedAllowedGroups, true)) {
                        $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
                        $result = $resultsByStudentId[$studentId] ?? null;
                        
                        $studentsList[] = [
                            'student' => $student,
                            'result' => $result,
                            'has_passed' => $result !== null,
                        ];
                        
                        if ($result !== null) {
                            $passedCount++;
                        } else {
                            $notPassedCount++;
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
                        $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
                        $result = $resultsByStudentId[$studentId] ?? null;
                        
                        $studentsList[] = [
                            'student' => $student,
                            'result' => $result,
                            'has_passed' => $result !== null,
                        ];
                        
                        if ($result !== null) {
                            $passedCount++;
                        } else {
                            $notPassedCount++;
                        }
                    }
                }
            }
        }

        // Сортируем: сначала сдавшие, потом не сдавшие
        usort($studentsList, function($a, $b) {
            if ($a['has_passed'] === $b['has_passed']) {
                $nameA = $a['student']['name'] ?? $a['student']['full_name'] ?? '';
                $nameB = $b['student']['name'] ?? $b['student']['full_name'] ?? '';
                return strcmp($nameA, $nameB);
            }
            return $b['has_passed'] ? 1 : -1;
        });

        echo View::render('admin/results/test-students', [
            'title' => 'Test talabalari: ' . ($test['title'] ?? ''),
            'test' => $test,
            'students' => $studentsList,
            'total_students' => count($studentsList),
            'passed_count' => $passedCount,
            'not_passed_count' => $notPassedCount,
        ]);
    }

    /** Результаты конкретного студента */
    public function studentResults(): void
    {
        $this->ensureAuth();

        $studentId = $_GET['id'] ?? '';

        if (empty($studentId)) {
            $_SESSION['admin_flash'] = 'Talaba ID ko\'rsatilmagan.';
            header('Location: /admin/results');
            exit;
        }

        $results = $this->resultsStorage->getResultsByStudentId($studentId);

        if (empty($results)) {
            $_SESSION['admin_flash'] = 'Ushbu talaba uchun natijalar topilmadi.';
            header('Location: /admin/results');
            exit;
        }

        $studentName = $results[0]['student_name'] ?? 'Noma\'lum';
        $studentInfo = [
            'id' => $studentId,
            'name' => $studentName,
        ];

        echo View::render('admin/results/student', [
            'title' => 'Talaba natijalari: ' . $studentName,
            'student' => $studentInfo,
            'results' => $results,
        ]);
    }

    /** Все результаты теста Айзенка */
    public function eysenckResults(): void
    {
        $this->ensureAuth();

        // Фильтр по темпераменту
        $filterTemperament = $_GET['temperament'] ?? '';

        $results = $this->resultsStorage->getEysenckResults();

        // Применяем фильтр по темпераменту
        if (!empty($filterTemperament)) {
            $results = array_filter($results, function ($result) use ($filterTemperament) {
                return ($result['temperament']['type'] ?? '') === $filterTemperament;
            });
            $results = array_values($results);
        }

        $statistics = $this->resultsStorage->getTestStatistics(-1);
        $temperamentDistribution = $statistics['temperament_distribution'] ?? [];

        echo View::render('admin/results/eysenck', [
            'title' => 'Temperament natijalari',
            'results' => $results,
            'statistics' => $statistics,
            'temperament_distribution' => $temperamentDistribution,
            'filter_temperament' => $filterTemperament,
        ]);
    }

    /** Страница со статистикой */
    public function statistics(): void
    {
        $this->ensureAuth();

        $audience = $_GET['audience'] ?? 'students';
        $audience = in_array($audience, ['students', 'teachers', 'all'], true) ? $audience : 'students';

        $overallStats = $this->resultsStorage->getOverallStatistics($audience);
        $tests = $this->testStorage->getAll();

        // Статистика по каждому тесту
        $testStatistics = [];
        foreach ($tests as $test) {
            $testId = $test['id'] ?? 0;
            if ($testId) {
                $testStatistics[$testId] = $this->resultsStorage->getTestStatistics($testId, [], [], $test, $audience);
                $testStatistics[$testId]['test'] = $test;
            }
        }

        // Статистика по тесту Айзенка
        $eysenckStats = $this->resultsStorage->getTestStatistics(-1, [], [], null, $audience);
        $testStatistics['eysenck'] = $eysenckStats;
        $testStatistics['eysenck']['test'] = [
            'id' => 'eysenck',
            'title' => 'Temperament',
        ];

        // Статистика по IQ тесту
        $iqStats = $this->resultsStorage->getIqStatistics();
        $iqResults = $this->resultsStorage->getIqResults();
        
        // Добавляем диапазон дат для IQ теста
        $iqDateRange = ['first' => null, 'last' => null];
        if (!empty($iqResults)) {
            $dates = [];
            foreach ($iqResults as $result) {
                $date = $result['completed_at'] ?? null;
                if ($date) {
                    $dates[] = $date;
                }
            }
            if (!empty($dates)) {
                sort($dates);
                $iqDateRange['first'] = $dates[0];
                $iqDateRange['last'] = end($dates);
            }
        }
        
        $testStatistics['iq'] = array_merge($iqStats, [
            'date_range' => $iqDateRange,
        ]);
        $testStatistics['iq']['test'] = [
            'id' => 'iq',
            'title' => 'IQ Test',
        ];

        // Статистика по тесту Люшера
        $lusherStats = $this->resultsStorage->getLusherStatistics();
        $lusherResults = $this->resultsStorage->getLusherResults();
        
        // Добавляем диапазон дат для теста Люшера
        $lusherDateRange = ['first' => null, 'last' => null];
        if (!empty($lusherResults)) {
            $dates = [];
            foreach ($lusherResults as $result) {
                $date = $result['completed_at'] ?? null;
                if ($date) {
                    $dates[] = $date;
                }
            }
            if (!empty($dates)) {
                sort($dates);
                $lusherDateRange['first'] = $dates[0];
                $lusherDateRange['last'] = end($dates);
            }
        }
        
        $testStatistics['lusher'] = array_merge($lusherStats, [
            'date_range' => $lusherDateRange,
        ]);
        $testStatistics['lusher']['test'] = [
            'id' => 'lusher',
            'title' => 'Lyusher Testi',
        ];

        echo View::render('admin/results/statistics', [
            'title' => 'Statistika',
            'overall' => $overallStats,
            'test_statistics' => $testStatistics,
            'audience' => $audience,
        ]);
    }

    /** Страница детальной аналитики */
    public function analytics(): void
    {
        $this->ensureAuth();

        $overallStats = $this->resultsStorage->getOverallStatistics();
        $allResults = $this->resultsStorage->getAllResults();

        // Аналитика по дням (последние 30 дней)
        $dailyStats = [];
        $today = date('Y-m-d');
        for ($i = 29; $i >= 0; $i--) {
            $date = date('Y-m-d', strtotime("-$i days", strtotime($today)));
            $dailyStats[$date] = 0;
        }

        foreach ($allResults as $result) {
            $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
            if ($date) {
                $dateOnly = substr($date, 0, 10);
                if (isset($dailyStats[$dateOnly])) {
                    $dailyStats[$dateOnly]++;
                }
            }
        }

        // Топ студентов по активности
        $students = $this->resultsStorage->getAllStudents();
        usort($students, function ($a, $b) {
            return ($b['tests_count'] ?? 0) - ($a['tests_count'] ?? 0);
        });
        $topStudents = array_slice($students, 0, 10);

        echo View::render('admin/results/analytics', [
            'title' => 'Batafsil tahlil',
            'overall' => $overallStats,
            'daily_stats' => $dailyStats,
            'top_students' => $topStudents,
        ]);
    }

    /** Экспорт результатов в Excel */
    public function exportExcel(): void
    {
        $this->ensureAuth();

        // Получаем параметры фильтрации
        $filterType = $_GET['type'] ?? 'all'; // all, custom, eysenck, iq
        // Поддерживаем оба варианта: test_id и id (для совместимости)
        $filterTestId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
        $filterStudentId = $_GET['student_id'] ?? '';
        $filterGroup = $_GET['group'] ?? '';
        $filterFaculty = $_GET['faculty'] ?? '';
        $filterSpecialty = $_GET['specialty'] ?? '';
        $filterDateFrom = $_GET['date_from'] ?? '';
        $filterDateTo = $_GET['date_to'] ?? '';
        $filterCategory = $_GET['category'] ?? '';

        // Получаем информацию о студентах для групп, факультетов и направлений
        $studentStorage = new StudentStorage();
        $allStudents = $studentStorage->getAll();
        
        // Создаем массивы соответствия student_id => данные
        $studentGroups = [];
        $studentFaculties = [];
        $studentSpecialties = [];
        foreach ($allStudents as $student) {
            $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
            if (!empty($studentId)) {
                $studentGroups[$studentId] = $student['group'] ?? '';
                $studentFaculties[$studentId] = $student['faculty'] ?? '';
                $studentSpecialties[$studentId] = $student['specialty'] ?? '';
            }
        }

        // Получаем результаты
        $allResults = $this->resultsStorage->getAllResults();
        
        // Применяем фильтры
        $filteredResults = $allResults;

        // ВАЖНО: Если выбран конкретный тест (test_id > 0), фильтруем ТОЛЬКО по test_id
        // Это должно быть приоритетнее, чем фильтр по типу
        if ($filterTestId > 0) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterTestId) {
                $resultTestId = $result['test_id'] ?? 0;
                // Преобразуем оба значения в строку для строгого сравнения
                // Для обычных тестов test_id - число, для Айзенка может быть строка 'eysenck'
                $resultTestIdStr = (string)$resultTestId;
                $filterTestIdStr = (string)$filterTestId;
                // Строгое сравнение строк
                return $resultTestIdStr === $filterTestIdStr;
            });
            // Пересобираем массив после фильтрации по test_id
            $filteredResults = array_values($filteredResults);
        } elseif ($filterType !== 'all') {
            // Если конкретный тест не выбран, применяем фильтр по типу
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterType) {
                return ($result['test_type'] ?? '') === $filterType;
            });
        }

        if (!empty($filterStudentId)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterStudentId) {
                return ($result['student_id'] ?? '') === $filterStudentId;
            });
        }

        if (!empty($filterDateFrom) || !empty($filterDateTo)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterDateFrom, $filterDateTo) {
                $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                if (empty($date)) {
                    return false;
                }
                if (!empty($filterDateFrom) && $date < $filterDateFrom) {
                    return false;
                }
                if (!empty($filterDateTo) && $date > $filterDateTo) {
                    return false;
                }
                return true;
            });
        }

        // Добавляем информацию о группе, факультете и направлении к каждому результату
        foreach ($filteredResults as &$result) {
            $studentId = (string)($result['student_id'] ?? '');
            $result['student_group'] = $studentGroups[$studentId] ?? '';
            $result['student_faculty'] = $studentFaculties[$studentId] ?? '';
            $result['student_specialty'] = $studentSpecialties[$studentId] ?? '';
        }
        unset($result);

        // Фильтруем по группе, если указана
        if (!empty($filterGroup)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterGroup) {
                return ($result['student_group'] ?? '') === $filterGroup;
            });
        }

        // Фильтруем по факультету, если указан
        if (!empty($filterFaculty)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterFaculty) {
                return ($result['student_faculty'] ?? '') === $filterFaculty;
            });
        }

        // Фильтруем по направлению, если указано
        if (!empty($filterSpecialty)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterSpecialty) {
                return ($result['student_specialty'] ?? '') === $filterSpecialty;
            });
        }

        // Пересобираем массив после фильтрации
        $filteredResults = array_values($filteredResults);
        
        // Получаем список всех тестов для фильтрации по категории теста
        $allTests = $this->testStorage->getAll();
        
        // Применяем фильтр по категории теста, если указан
        if (!empty($filterCategory)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterCategory, $allTests) {
                $testType = $result['test_type'] ?? '';
                
                // Для специальных тестов (IQ, Айзенк, Люшер) не фильтруем по категории теста
                // так как у них нет категории теста в обычном смысле
                if ($testType === 'iq' || $testType === 'eysenck' || $testType === 'lusher') {
                    return false; // Скрываем специальные тесты при фильтрации по категории
                }
                
                // Для обычных тестов находим тест и проверяем его категорию
                $testId = $result['test_id'] ?? 0;
                if ($testId > 0) {
                    foreach ($allTests as $test) {
                        if ((int)($test['id'] ?? 0) === $testId) {
                            $testCategory = $test['category'] ?? '';
                            return $testCategory === $filterCategory;
                        }
                    }
                }
                
                return false;
            });
            $filteredResults = array_values($filteredResults);
        }

        // Фильтруем результаты удаленных тестов - показываем только результаты существующих тестов
        // Используем findById для надежности
        $filteredResults = array_filter($filteredResults, function ($result) {
            $resultTestId = $result['test_id'] ?? 0;
            $testType = $result['test_type'] ?? '';
            
            // Тест Люшера всегда показываем (test_type = 'lusher') - проверяем первым
            if ($testType === 'lusher' || $resultTestId === 'lusher') {
                return true;
            }
            
            // Тест Айзенка всегда показываем (test_id = 'eysenck' или 0/-1)
            if ($testType === 'eysenck' || $resultTestId === 'eysenck' || $resultTestId === 0 || $resultTestId === -1) {
                return true;
            }
            
            // IQ тест всегда показываем (test_id = 'iq')
            if ($testType === 'iq' || $resultTestId === 'iq') {
                return true;
            }
            
            // Для обычных тестов проверяем через findById для надежности
            $testIdInt = (int)$resultTestId;
            if ($testIdInt > 0) {
                $test = $this->testStorage->findById($testIdInt);
                return $test !== null;
            }
            
            // Если test_id не число и не 'eysenck' и не 'iq' и не 'lusher' - скрываем
            return false;
        });
        $filteredResults = array_values($filteredResults);

        if (empty($filteredResults)) {
            $_SESSION['admin_flash'] = 'Eksport qilish uchun natijalar topilmadi.';
            header('Location: /admin/results');
            exit;
        }

        // Создаем сервис экспорта
        $excelService = new ExcelExportService();

        // Фильтруем результаты учителей - исключаем записи, где student_id начинается с "teacher_"
        $filteredResults = array_filter($filteredResults, function ($result) {
            $studentId = (string)($result['student_id'] ?? '');
            // Исключаем учителей (их student_id начинается с "teacher_")
            return substr($studentId, 0, 8) !== 'teacher_';
        });
        $filteredResults = array_values($filteredResults);

        // Разделяем результаты по типам
        $eysenckResults = [];
        $customResults = [];
        $iqResults = [];
        $lusherResults = [];
        
        foreach ($filteredResults as $result) {
            $testType = $result['test_type'] ?? '';
            if ($testType === 'eysenck') {
                $eysenckResults[] = $result;
            } elseif ($testType === 'custom') {
                $customResults[] = $result;
            } elseif ($testType === 'iq') {
                $iqResults[] = $result;
            } elseif ($testType === 'lusher') {
                $lusherResults[] = $result;
            }
        }

        // Получаем всех студентов из allowed_groups, если указан test_id
        $allStudentsInGroups = [];
        if ($filterTestId > 0) {
            $test = $this->testStorage->findById($filterTestId);
            if ($test) {
                $allowedGroups = $test['allowed_groups'] ?? [];
                $allowedFaculties = $test['allowed_faculties'] ?? [];
                
                if (!empty($allowedGroups) || !empty($allowedFaculties)) {
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

                    if (!empty($allowedGroups)) {
                        $normalizedAllowedGroups = array_map($normalize, $allowedGroups);
                        foreach ($allStudents as $student) {
                            $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
                            // Исключаем учителей (их student_id начинается с "teacher_")
                            if (substr($studentId, 0, 8) === 'teacher_') {
                                continue;
                            }
                            $studentGroup = $student['group'] ?? null;
                            if ($studentGroup && in_array($normalize($studentGroup), $normalizedAllowedGroups, true)) {
                                $allStudentsInGroups[] = $student;
                            }
                        }
                    } elseif (!empty($allowedFaculties)) {
                        $normalizedAllowedFaculties = array_map($normalize, $allowedFaculties);
                        foreach ($allStudents as $student) {
                            $studentId = (string)($student['student_id'] ?? $student['id'] ?? '');
                            // Исключаем учителей (их student_id начинается с "teacher_")
                            if (substr($studentId, 0, 8) === 'teacher_') {
                                continue;
                            }
                            $studentFaculty = $student['faculty'] ?? null;
                            if ($studentFaculty && in_array($normalize($studentFaculty), $normalizedAllowedFaculties, true)) {
                                $allStudentsInGroups[] = $student;
                            }
                        }
                    }
                }
            }
        }

        // Определяем тип экспорта на основе фильтра и наличия результатов
        if ($filterType === 'eysenck' || (!empty($eysenckResults) && empty($customResults) && empty($iqResults) && empty($lusherResults))) {
            // Экспорт результатов теста Айзенка
            if (!empty($eysenckResults)) {
                $excelService->exportEysenckResults($eysenckResults, $studentGroups);
            } else {
                $_SESSION['admin_flash'] = 'Temperament natijalari topilmadi.';
                header('Location: /admin/results');
                exit;
            }
        } elseif ($filterType === 'iq' || (!empty($iqResults) && empty($customResults) && empty($eysenckResults) && empty($lusherResults))) {
            // Экспорт результатов IQ теста
            if (!empty($iqResults)) {
                $excelService->exportIqResults($iqResults, $studentGroups);
            } else {
                $_SESSION['admin_flash'] = 'IQ test natijalari topilmadi.';
                header('Location: /admin/results');
                exit;
            }
        } elseif ($filterType === 'lusher' || (!empty($lusherResults) && empty($customResults) && empty($eysenckResults) && empty($iqResults))) {
            // Экспорт результатов теста Люшера
            if (!empty($lusherResults)) {
                $excelService->exportLusherResults($lusherResults, $studentGroups);
            } else {
                $_SESSION['admin_flash'] = 'Lyusher testi natijalari topilmadi.';
                header('Location: /admin/results');
                exit;
            }
        } elseif ($filterType === 'custom' || (!empty($customResults) && empty($eysenckResults) && empty($iqResults) && empty($lusherResults))) {
            // Экспорт результатов обычных тестов
            if (!empty($customResults)) {
                // Получаем данные теста, если указан конкретный тест
                $test = null;
                if ($filterTestId > 0) {
                    $test = $this->testStorage->findById($filterTestId);
                } elseif (!empty($customResults)) {
                    $firstTestId = $customResults[0]['test_id'] ?? 0;
                    if ($firstTestId > 0 && is_numeric($firstTestId)) {
                        $test = $this->testStorage->findById((int)$firstTestId);
                    }
                }
                
                $excelService->exportCustomTestResults($customResults, $test, $studentGroups, $allStudentsInGroups);
            } else {
                $_SESSION['admin_flash'] = 'Oddiy testlar natijalari topilmadi.';
                header('Location: /admin/results');
                exit;
            }
        } else {
            // Если есть оба типа, экспортируем все (можно улучшить, создав отдельные листы)
            // Пока экспортируем только обычные тесты, если они есть
            if (!empty($customResults)) {
                $test = null;
                if ($filterTestId > 0) {
                    $test = $this->testStorage->findById($filterTestId);
                }
                $excelService->exportCustomTestResults($customResults, $test, $studentGroups, $allStudentsInGroups);
            } elseif (!empty($eysenckResults)) {
                $excelService->exportEysenckResults($eysenckResults, $studentGroups);
            } elseif (!empty($iqResults)) {
                $excelService->exportIqResults($iqResults, $studentGroups);
            } elseif (!empty($lusherResults)) {
                $excelService->exportLusherResults($lusherResults, $studentGroups);
            }
        }
    }

    /** Очистить все результаты тестов */
    public function clearAll(): void
    {
        $this->ensureAuth();

        // Проверяем метод запроса (только POST для безопасности)
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['admin_flash'] = 'Noto\'g\'ri so\'rov metodi.';
            header('Location: /admin/results');
            exit;
        }

        // Очищаем все результаты
        $success = $this->resultsStorage->clearAllResults();

        if ($success) {
            $_SESSION['admin_flash'] = 'Barcha test natijalari muvaffaqiyatli o\'chirildi.';
        } else {
            $_SESSION['admin_flash'] = 'Xatolik yuz berdi. Natijalar o\'chirilmadi.';
        }

        header('Location: /admin/results');
        exit;
    }

    /** Очистить результаты только студентов (teacher_* сохранить) */
    public function clearStudentsOnly(): void
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['admin_flash'] = 'Noto\'g\'ri so\'rov metodi.';
            header('Location: /admin/results');
            exit;
        }

        $success = $this->resultsStorage->clearStudentResults();
        $_SESSION['admin_flash'] = $success
            ? 'Talabalar natijalari muvaffaqiyatli o\'chirildi.'
            : 'Xatolik yuz berdi. Natijalar o\'chirilmadi.';

        header('Location: /admin/results');
        exit;
    }

    /** Очистить результаты только преподавателей (students сохранить) */
    public function clearTeachersOnly(): void
    {
        $this->ensureAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $_SESSION['admin_flash'] = 'Noto\'g\'ri so\'rov metodi.';
            header('Location: /admin/results/teachers');
            exit;
        }

        $success = $this->resultsStorage->clearTeacherResults();
        $_SESSION['admin_flash'] = $success
            ? 'O\'qituvchilar natijalari muvaffaqiyatli o\'chirildi.'
            : 'Xatolik yuz berdi. Natijalar o\'chirilmadi.';

        header('Location: /admin/results/teachers');
        exit;
    }

    /** Страница выбора факультета и отображения групп */
    public function testGroups(): void
    {
        $this->ensureAuth();

        $testId = isset($_GET['test_id']) ? $_GET['test_id'] : 'eysenck';
        $selectedFaculty = $_GET['faculty'] ?? null;

        // Преобразуем test_id в int для ResultsStorage
        $testIdInt = ($testId === 'eysenck' || $testId === '0' || $testId === '-1') ? 0 : (int)$testId;

        // Получаем список факультетов
        $hemisApi = new \App\HemisApi();
        $faculties = $hemisApi->getFaculties();

        // Если выбран факультет, получаем статистику и группы
        $facultyStats = null;
        $groups = [];

        if ($selectedFaculty) {
            $facultyStats = $this->resultsStorage->getFacultyStatistics($selectedFaculty, $testIdInt);
            $groups = $facultyStats['groups'] ?? [];
        }

        // Получаем информацию о тесте
        if ($testId === 'eysenck' || $testIdInt === 0) {
            $test = [
                'id' => 'eysenck',
                'title' => 'Temperament',
            ];
        } else {
            $test = $this->testStorage->findById($testIdInt);
            if ($test === null) {
                $_SESSION['admin_flash'] = 'Test topilmadi.';
                header('Location: /admin/results');
                exit;
            }
        }

        echo View::render('admin/results/test-groups', [
            'title' => 'Guruhlar kesimida statistika',
            'test' => $test,
            'test_id' => $testId,
            'faculties' => $faculties,
            'selected_faculty' => $selectedFaculty,
            'faculty_stats' => $facultyStats,
            'groups' => $groups,
        ]);
    }

    /** Страница детальной статистики группы */
    public function groupStatistics(): void
    {
        $this->ensureAuth();

        $testId = isset($_GET['test_id']) ? $_GET['test_id'] : 'eysenck';
        $group = $_GET['group'] ?? '';

        if (empty($group)) {
            $_SESSION['admin_flash'] = 'Guruh ko\'rsatilmagan.';
            header('Location: /admin/results/test-groups?test_id=' . urlencode($testId));
            exit;
        }

        // Преобразуем test_id в int для ResultsStorage
        $testIdInt = ($testId === 'eysenck' || $testId === '0' || $testId === '-1') ? 0 : (int)$testId;

        // Получаем статистику группы
        $groupStats = $this->resultsStorage->getGroupStatistics($group, $testIdInt);

        // Получаем информацию о тесте
        if ($testId === 'eysenck' || $testIdInt === 0) {
            $test = [
                'id' => 'eysenck',
                'title' => 'Temperament',
            ];
        } else {
            $test = $this->testStorage->findById($testIdInt);
            if ($test === null) {
                $_SESSION['admin_flash'] = 'Test topilmadi.';
                header('Location: /admin/results');
                exit;
            }
        }

        echo View::render('admin/results/group-statistics', [
            'title' => $group . ' guruhi kesimida statistika',
            'test' => $test,
            'test_id' => $testId,
            'group' => $group,
            'stats' => $groupStats,
        ]);
    }

    /** Результаты преподавателей */
    public function teacherResults(): void
    {
        $this->ensureAuth();

        // Фильтры
        $filterType = $_GET['type'] ?? 'all';
        $filterTestId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
        $filterTeacherId = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : 0;
        $filterDateFrom = $_GET['date_from'] ?? '';
        $filterDateTo = $_GET['date_to'] ?? '';

        // Получаем ВСЕ результаты
        $allResults = $this->resultsStorage->getAllResults();

        // Фильтруем только результаты преподавателей (student_id начинается с 'teacher_')
        $teacherResults = array_filter($allResults, function ($result) {
            $studentId = (string)($result['student_id'] ?? '');
            return strpos($studentId, 'teacher_') === 0;
        });
        $teacherResults = array_values($teacherResults);

        // Применяем доп. фильтры
        $filteredResults = $teacherResults;

        if ($filterTeacherId > 0) {
            $teacherStudentId = 'teacher_' . $filterTeacherId;
            $filteredResults = array_filter($filteredResults, function ($result) use ($teacherStudentId) {
                return ($result['student_id'] ?? '') === $teacherStudentId;
            });
        }

        if ($filterTestId > 0) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterTestId) {
                $resultTestId = $result['test_id'] ?? 0;
                // Преобразуем оба значения в строку для сравнения
                return (string)$resultTestId === (string)$filterTestId;
            });
        } elseif ($filterType !== 'all') {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterType) {
                return ($result['test_type'] ?? '') === $filterType;
            });
        }

        if (!empty($filterDateFrom) || !empty($filterDateTo)) {
            $filteredResults = array_filter($filteredResults, function ($result) use ($filterDateFrom, $filterDateTo) {
                $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                if (empty($date)) {
                    return false;
                }
                if (!empty($filterDateFrom) && $date < $filterDateFrom) {
                    return false;
                }
                if (!empty($filterDateTo) && $date > $filterDateTo) {
                    return false;
                }
                return true;
            });
        }

        $filteredResults = array_values($filteredResults);

        // Получаем данные преподавателей
        $teacherService = new \App\Services\TeacherService();
        $teachers = $teacherService->getAll();
        $teachersById = [];
        foreach ($teachers as $teacher) {
            $teachersById[$teacher['id']] = $teacher;
        }

        // Получаем список тестов для фильтра
        $tests = $this->testStorage->getAll();
        $testsById = [];
        foreach ($tests as $test) {
            $testsById[$test['id']] = $test;
        }

        // Обогащаем результаты данными преподавателей и тестов
        foreach ($filteredResults as &$result) {
            $studentId = $result['student_id'] ?? '';
            if (strpos($studentId, 'teacher_') === 0) {
                $teacherIdStr = str_replace('teacher_', '', $studentId);
                $teacherId = (int)$teacherIdStr;
                
                // Пытаемся найти преподавателя по ID
                if (isset($teachersById[$teacherId])) {
                    $result['teacher_data'] = $teachersById[$teacherId];
                } else {
                    // Если не нашли, используем student_name как fallback
                    $result['teacher_data'] = [
                        'full_name' => $result['student_name'] ?? 'Noma\'lum o\'qituvchi',
                        'department' => '-',
                        'id' => $teacherId,
                    ];
                }
            }
            
            // Добавляем test_title, если его нет
            if (empty($result['test_title'])) {
                $testType = $result['test_type'] ?? '';
                $testId = $result['test_id'] ?? 0;
                
                if ($testType === 'eysenck' || $testId === 'eysenck' || $testId === 0 || $testId === -1) {
                    $result['test_title'] = 'Temperament testi';
                } elseif ($testType === 'iq' || $testId === 'iq') {
                    $result['test_title'] = 'IQ testi';
                } elseif ($testType === 'lusher' || $testId === 'lusher') {
                    $result['test_title'] = 'Lyusher testi';
                } elseif ($testId > 0 && isset($testsById[$testId])) {
                    $result['test_title'] = $testsById[$testId]['title'] ?? 'Noma\'lum test';
                } else {
                    $result['test_title'] = $result['test_title'] ?? 'Noma\'lum test';
                }
            }
        }
        unset($result);

        // Сортируем результаты по дате (новые сверху)
        usort($filteredResults, function ($a, $b) {
            $dateA = $a['submitted_at'] ?? $a['completed_at'] ?? '1970-01-01';
            $dateB = $b['submitted_at'] ?? $b['completed_at'] ?? '1970-01-01';
            return strtotime($dateB) - strtotime($dateA);
        });

        echo View::render('admin/results/teachers', [
            'title' => 'O\'qituvchilar natijalari',
            'results' => $filteredResults,
            'tests' => $tests,
            'filters' => [
                'type' => $filterType,
                'test_id' => $filterTestId,
                'teacher_id' => $filterTeacherId,
                'date_from' => $filterDateFrom,
                'date_to' => $filterDateTo,
            ],
        ]);
    }

    /** Статистика по преподавателям */
    public function teacherStatistics(): void
    {
        $this->ensureAuth();

        // Получаем все результаты преподавателей
        $allResults = $this->resultsStorage->getAllResults();
        $teacherResults = array_filter($allResults, function ($result) {
            $studentId = $result['student_id'] ?? '';
            return strpos($studentId, 'teacher_') === 0;
        });

        // Получаем данные преподавателей
        $teacherService = new \App\Services\TeacherService();
        $teachers = $teacherService->getAll();

        // Подсчитываем статистику для каждого преподавателя
        $stats = [];
        foreach ($teachers as $teacher) {
            $teacherId = $teacher['id'];
            $teacherStudentId = 'teacher_' . $teacherId;

            // Считаем результаты этого преподавателя
            $teacherTestResults = array_filter($teacherResults, function ($result) use ($teacherStudentId) {
                return ($result['student_id'] ?? '') === $teacherStudentId;
            });

            $totalTests = count($teacherTestResults);
            $customTests = 0;
            $builtInTests = 0;

            foreach ($teacherTestResults as $result) {
                $testType = $result['test_type'] ?? '';
                if ($testType === 'custom') {
                    $customTests++;
                } else {
                    $builtInTests++;
                }
            }

            $stats[] = [
                'teacher' => $teacher,
                'total_tests' => $totalTests,
                'custom_tests' => $customTests,
                'built_in_tests' => $builtInTests,
            ];
        }

        // Сортируем по количеству пройденных тестов
        usort($stats, function ($a, $b) {
            return $b['total_tests'] - $a['total_tests'];
        });

        echo View::render('admin/results/teacher-statistics', [
            'title' => 'O\'qituvchilar statistikasi',
            'stats' => $stats,
        ]);
    }

    /** Экспорт результатов преподавателей в Excel */
    public function exportTeacherExcel(): void
    {
        $this->ensureAuth();

        // Фильтры (как на странице /admin/results/teachers)
        $filterType = $_GET['type'] ?? 'all';
        $filterTestId = isset($_GET['test_id']) ? (int)$_GET['test_id'] : 0;
        $filterTeacherId = isset($_GET['teacher_id']) ? (int)$_GET['teacher_id'] : 0;
        $filterDateFrom = $_GET['date_from'] ?? '';
        $filterDateTo = $_GET['date_to'] ?? '';

        // Получаем все результаты преподавателей
        $allResults = $this->resultsStorage->getAllResults();
        $teacherResults = array_filter($allResults, function ($result) {
            $studentId = $result['student_id'] ?? '';
            return strpos($studentId, 'teacher_') === 0;
        });
        $teacherResults = array_values($teacherResults);

        // Применяем фильтры
        if ($filterTeacherId > 0) {
            $teacherStudentId = 'teacher_' . $filterTeacherId;
            $teacherResults = array_values(array_filter($teacherResults, function ($result) use ($teacherStudentId) {
                return ($result['student_id'] ?? '') === $teacherStudentId;
            }));
        }

        if ($filterTestId > 0) {
            $teacherResults = array_values(array_filter($teacherResults, function ($result) use ($filterTestId) {
                return ($result['test_id'] ?? 0) == $filterTestId;
            }));
        } elseif ($filterType !== 'all') {
            $teacherResults = array_values(array_filter($teacherResults, function ($result) use ($filterType) {
                return ($result['test_type'] ?? '') === $filterType;
            }));
        }

        if (!empty($filterDateFrom) || !empty($filterDateTo)) {
            $teacherResults = array_values(array_filter($teacherResults, function ($result) use ($filterDateFrom, $filterDateTo) {
                $date = $result['submitted_at'] ?? $result['completed_at'] ?? '';
                if (empty($date)) {
                    return false;
                }
                if (!empty($filterDateFrom) && $date < $filterDateFrom) {
                    return false;
                }
                if (!empty($filterDateTo) && $date > $filterDateTo) {
                    return false;
                }
                return true;
            }));
        }

        // Получаем данные преподавателей
        $teacherService = new \App\Services\TeacherService();
        $teachers = $teacherService->getAll();
        $teachersById = [];
        foreach ($teachers as $teacher) {
            $teachersById[$teacher['id']] = $teacher;
        }

        // Обогащаем результаты данными преподавателей
        foreach ($teacherResults as &$result) {
            $studentId = $result['student_id'] ?? '';
            if (strpos($studentId, 'teacher_') === 0) {
                $teacherId = (int)str_replace('teacher_', '', $studentId);
                $result['teacher_data'] = $teachersById[$teacherId] ?? null;
            }
        }

        if (empty($teacherResults)) {
            $_SESSION['admin_flash'] = 'Eksport qilish uchun natijalar topilmadi.';
            header('Location: /admin/results/teachers');
            exit;
        }

        // Получаем тесты
        $tests = $this->testStorage->getAll();
        $testsById = [];
        foreach ($tests as $test) {
            $testsById[$test['id']] = $test;
        }

        $excelService = new ExcelExportService();

        // Если выбран конкретный custom test_id — делаем детальный экспорт с ответами (как у студентов)
        if ($filterTestId > 0) {
            $test = $this->testStorage->findById($filterTestId);
            if ($test !== null) {
                $excelService->exportCustomTestResults($teacherResults, $test, [], []);
                return;
            }
        }

        // Иначе — сводный экспорт по преподавателям
        $excelService->exportTeacherResults($teacherResults, $testsById);
    }
}

