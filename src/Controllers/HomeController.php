<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\StudentStorage;
use App\Services\TestStorage;
use App\Services\ResultsStorage;
use App\View;

class HomeController
{
    /**
     * Главная страница - определяет тип пользователя и перенаправляет
     */
    public function index(): void
    {
        // Если студент авторизован - перенаправляем в личный кабинет
        if (!empty($_SESSION['hemis_user'])) {
            header('Location: /dashboard');
            exit;
        }

        // Если психолог авторизован - перенаправляем в кабинет психолога
        if (!empty($_SESSION['admin_logged_in'])) {
            header('Location: /admin');
            exit;
        }

        // Получаем статистику для лендинга
        $statistics = $this->getStatistics();

        // Если никто не авторизован - показываем публичную главную страницу
        echo View::render('home', [
            'title' => 'Termiz davlat pedagogika instituti',
            'statistics' => $statistics,
        ]);
    }

    /**
     * Получить статистику для отображения на лендинге
     * @return array<string, int>
     */
    private function getStatistics(): array
    {
        try {
            $studentStorage = new StudentStorage();
            $testStorage = new TestStorage();
            $resultsStorage = new ResultsStorage();

            $students = $studentStorage->getAll();
            $tests = $testStorage->getAll();
            $allResults = $resultsStorage->getAllResults();

            return [
                'students_count' => count($students),
                'tests_count' => count($tests),
                'results_count' => count($allResults),
            ];
        } catch (\Exception $e) {
            // В случае ошибки возвращаем нули
            return [
                'students_count' => 0,
                'tests_count' => 0,
                'results_count' => 0,
            ];
        }
    }
}
