<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\StudentStorage;
use App\HemisApi;

class StudentController
{
    public function list(): void
    {
        // Проверяем, что пользователь - психолог
        if (empty($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
        
        $title = "Talabalar ro'yxati";
        $storage = new StudentStorage();
        $allStudents = $storage->getAll();
        
        // Получаем параметры фильтрации
        $selectedFaculty = $_GET['faculty'] ?? null;
        $selectedSpecialty = $_GET['specialty'] ?? null;
        $selectedGroup = $_GET['group'] ?? null;
        
        // Нормализация для сравнения (такая же как в HemisApi)
        $normalize = function($name) {
            if ($name === null || $name === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$name));
            // Заменяем все типы апострофов и кавычек на обычный апостроф
            $apostrophes = [
                "\xCA\xBC",     // U+02BC MODIFIER LETTER APOSTROPHE
                "\xE2\x80\x99", // U+2019 RIGHT SINGLE QUOTATION MARK
                "\xE2\x80\x98", // U+2018 LEFT SINGLE QUOTATION MARK
                "\xE2\x80\x9C", // U+201C LEFT DOUBLE QUOTATION MARK
                "\xE2\x80\x9D", // U+201D RIGHT DOUBLE QUOTATION MARK
                '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };
        
        // Фильтруем студентов
        $filteredStudents = [];
        foreach ($allStudents as $student) {
            $studentGroup = $student['group'] ?? null;
            $studentSpecialty = $student['specialty'] ?? null;
            $studentFaculty = $student['faculty'] ?? null;
            
            // Если выбрана группа - проверяем только группу
            if ($selectedGroup !== null && $selectedGroup !== '') {
                $studentGroupNormalized = $normalize($studentGroup);
                $selectedGroupNormalized = $normalize($selectedGroup);
                if ($studentGroupNormalized !== $selectedGroupNormalized) {
                    continue;
                }
            }
            // Если выбрано направление (но не группа) - проверяем направление
            elseif ($selectedSpecialty !== null && $selectedSpecialty !== '') {
                $studentSpecialtyNormalized = $normalize($studentSpecialty);
                $selectedSpecialtyNormalized = $normalize($selectedSpecialty);
                if ($studentSpecialtyNormalized !== $selectedSpecialtyNormalized) {
                    continue;
                }
            }
            // Если выбран только факультет - проверяем факультет
            elseif ($selectedFaculty !== null && $selectedFaculty !== '') {
                $studentFacultyNormalized = $normalize($studentFaculty);
                $selectedFacultyNormalized = $normalize($selectedFaculty);
                if ($studentFacultyNormalized !== $selectedFacultyNormalized) {
                    continue;
                }
            }
            
            $filteredStudents[] = $student;
        }
        
        // Преобразуем формат для совместимости с view
        $students = [];
        foreach ($filteredStudents as $student) {
            $students[] = [
                'id' => $student['id'] ?? $student['student_id'] ?? null,
                'full_name' => $student['name'] ?? $student['full_name'] ?? 'Noma\'lum',
                'group' => $student['group'] ?? 'Noma\'lum',
                'faculty' => $student['faculty'] ?? 'Noma\'lum',
                'specialty' => $student['specialty'] ?? 'Noma\'lum',
                'status' => 'Активен', // Все студенты, которые входили, считаются активными
                'student_id' => $student['student_id'] ?? $student['id'] ?? null,
            ];
        }
        
        // Получаем списки для фильтров
        $hemisApi = new HemisApi();
        $faculties = $hemisApi->getFaculties();
        
        // Если выбран факультет, получаем направления
        $specialties = [];
        if ($selectedFaculty) {
            $specialties = $hemisApi->getSpecialtiesByFaculty($selectedFaculty);
        }
        
        // Если выбраны факультет и направление, получаем группы
        $groups = [];
        if ($selectedFaculty && $selectedSpecialty) {
            $groups = $hemisApi->getGroupsBySpecialty($selectedSpecialty, $selectedFaculty);
        }
        
        // Передаем переменные в view
        $selectedFaculty = $selectedFaculty ?? null;
        $selectedSpecialty = $selectedSpecialty ?? null;
        $selectedGroup = $selectedGroup ?? null;
        
        require __DIR__ . '/../../views/students/list.php';
    }

    public function show(): void
    {
        // Проверяем, что пользователь - психолог
        if (empty($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
        
        $id = isset($_GET['id']) ? (string)$_GET['id'] : '';

        if ($id === '') {
            http_response_code(400);
            echo 'Noto\'g\'ri talaba ID si.';
            return;
        }

        $storage = new StudentStorage();
        $student = $storage->findById($id);

        if ($student === null) {
            http_response_code(404);
            echo 'Talaba topilmadi.';
            return;
        }

        // Преобразуем формат для совместимости с view
        $student = [
            'id' => $student['id'] ?? $student['student_id'] ?? null,
            'full_name' => $student['name'] ?? $student['full_name'] ?? 'Noma\'lum',
            'group' => $student['group'] ?? 'Noma\'lum',
            'faculty' => $student['faculty'] ?? 'Noma\'lum',
            'status' => 'Активен',
            'email' => $student['email'] ?? null,
            'phone' => $student['phone'] ?? null,
            'specialty' => $student['specialty'] ?? null,
            'student_id' => $student['student_id'] ?? $student['id'] ?? null,
        ];

        $title = 'Talaba kartochkasi';
        require __DIR__ . '/../../views/students/show.php';
    }
}
