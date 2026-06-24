<?php
declare(strict_types=1);

namespace App\Controllers;

use App\View;
use App\Services\TeacherService;

class TeacherAuthController
{
    private TeacherService $teacherService;

    public function __construct()
    {
        $this->teacherService = new TeacherService();
    }

    /** Показываем форму регистрации */
    public function registerForm(): void
    {
        // Если уже авторизован, редиректим
        if (!empty($_SESSION['teacher_user'])) {
            header('Location: /teacher/dashboard');
            exit;
        }

        $error = $_SESSION['teacher_error'] ?? null;
        $success = $_SESSION['teacher_success'] ?? null;
        unset($_SESSION['teacher_error'], $_SESSION['teacher_success']);

        echo View::render('teachers/register', [
            'title' => 'O\'qituvchi sifatida ro\'yxatdan o\'tish',
            'error' => $error,
            'success' => $success,
        ]);
    }

    /** Обработка регистрации */
    public function register(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $fullName = trim($_POST['full_name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $department = trim($_POST['department'] ?? '');

        // Валидация
        if (empty($login) || empty($password) || empty($fullName)) {
            $_SESSION['teacher_error'] = 'Login, parol va to\'liq ismni kiriting.';
            header('Location: /teachers/register');
            exit;
        }

        try {
            $this->teacherService->register([
                'login' => $login,
                'password' => $password,
                'full_name' => $fullName,
                'email' => $email,
                'phone' => $phone,
                'department' => $department,
            ]);

            $_SESSION['teacher_success'] = 'Ro\'yxatdan o\'tish muvaffaqiyatli! Endi tizimga kirishingiz mumkin.';
            header('Location: /teachers/login');
            exit;
        } catch (\Exception $e) {
            $_SESSION['teacher_error'] = $e->getMessage();
            header('Location: /teachers/register');
            exit;
        }
    }

    /** Показываем форму входа */
    public function loginForm(): void
    {
        // Если уже авторизован, редиректим
        if (!empty($_SESSION['teacher_user'])) {
            header('Location: /teacher/dashboard');
            exit;
        }

        $error = $_SESSION['teacher_error'] ?? null;
        $success = $_SESSION['teacher_success'] ?? null;
        unset($_SESSION['teacher_error'], $_SESSION['teacher_success']);

        echo View::render('teachers/login', [
            'title' => 'O\'qituvchi uchun kirish',
            'error' => $error,
            'success' => $success,
        ]);
    }

    /** Обработка входа */
    public function login(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($login) || empty($password)) {
            $_SESSION['teacher_error'] = 'Login va parolni kiriting.';
            header('Location: /teachers/login');
            exit;
        }

        try {
            $teacher = $this->teacherService->authenticate($login, $password);

            if (!$teacher) {
                $_SESSION['teacher_error'] = 'Login yoki parol noto\'g\'ri.';
                header('Location: /teachers/login');
                exit;
            }

            // Сохраняем данные преподавателя в сессию
            $_SESSION['teacher_user'] = $teacher;

            // Редиректим в личный кабинет
            header('Location: /teacher/dashboard');
            exit;
        } catch (\Exception $e) {
            $_SESSION['teacher_error'] = $e->getMessage();
            header('Location: /teachers/login');
            exit;
        }
    }

    /** Выход */
    public function logout(): void
    {
        // Проверяем, был ли вход через HEMIS
        $wasHemisUser = !empty($_SESSION['teacher_user']['hemis_id']);
        
        // Очищаем локальную сессию
        unset($_SESSION['teacher_user']);
        unset($_SESSION['teacher_oauth2state']);
        
        if ($wasHemisUser) {
            // Редирект на выход из HEMIS
            $hemisBase = rtrim((string)($_ENV['HEMIS_TEACHER_OAUTH_BASE_URL'] ?? 'https://hemis.terdpi.uz'), '/');
            header('Location: ' . $hemisBase . '/dashboard/logout');
            exit;
        }
        
        header('Location: /');
        exit;
    }
}

