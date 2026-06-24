<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\View;
use App\Services\TeacherService;
use App\Services\ResultsStorage;
use App\Services\TestStorage;

class TeacherController
{
    private TeacherService $teacherService;
    private ResultsStorage $resultsStorage;
    private TestStorage $testStorage;

    public function __construct()
    {
        $this->teacherService = new TeacherService();
        $this->resultsStorage = new ResultsStorage();
        $this->testStorage = new TestStorage();
    }

    /** Список всех преподавателей */
    public function index(): void
    {
        $this->ensureAuth();

        $teachers = $this->teacherService->getAll();
        
        // Сортируем по дате регистрации (новые сверху)
        usort($teachers, function ($a, $b) {
            return strtotime($b['registered_at'] ?? '1970-01-01') - strtotime($a['registered_at'] ?? '1970-01-01');
        });

        $success = $_SESSION['admin_success'] ?? null;
        $error = $_SESSION['admin_error'] ?? null;
        unset($_SESSION['admin_success'], $_SESSION['admin_error']);

        echo View::render('admin/teachers/list', [
            'title' => 'O\'qituvchilar ro\'yxati',
            'teachers' => $teachers,
            'success' => $success,
            'error' => $error,
        ]);
    }

    private function ensureAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    /** Удалить преподавателя */
    public function delete(): void
    {
        $this->ensureAuth();

        $teacherId = (int)($_POST['teacher_id'] ?? 0);

        if ($teacherId <= 0) {
            $_SESSION['admin_error'] = 'Noto\'g\'ri o\'qituvchi ID.';
            header('Location: /admin');
            exit;
        }

        if ($this->teacherService->delete($teacherId)) {
            $studentId = 'teacher_' . $teacherId;
            $deletedResults = $this->resultsStorage->deleteAllResultsByStudentId($studentId);

            // Удаляем преподавателя из назначений тестов (allowed_teachers)
            $tests = $this->testStorage->getAll();
            $changed = false;
            foreach ($tests as &$test) {
                $allowed = $test['allowed_teachers'] ?? null;
                if (!is_array($allowed) || $allowed === []) {
                    continue;
                }

                $filtered = array_values(array_filter($allowed, function ($id) use ($teacherId) {
                    return (int)$id !== $teacherId;
                }));

                if (count($filtered) !== count($allowed)) {
                    $test['allowed_teachers'] = $filtered;
                    $changed = true;
                }
            }
            unset($test);

            if ($changed) {
                $this->testStorage->saveAll($tests);
            }

            $_SESSION['admin_flash'] = "O'qituvchi muvaffaqiyatli o'chirildi. O'chirilgan natijalar: {$deletedResults}.";
        } else {
            $_SESSION['admin_error'] = 'O\'qituvchini o\'chirishda xatolik yuz berdi.';
        }

        header('Location: /admin#teachers');
        exit;
    }
}

