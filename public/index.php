<?php
declare(strict_types=1);

use App\Router;
use App\Controllers\HomeController;
use App\Controllers\StudentController;
use App\Controllers\PublicTestController;
use App\Controllers\Admin\AuthController;
use App\Controllers\Admin\TestController as AdminTestController;
use App\Controllers\Admin\ResultsController;
use App\Controllers\Admin\ApiController;
use App\Controllers\Admin\TeacherController as AdminTeacherController;
use App\Controllers\Admin\SyncController;
use App\Controllers\HemisAuthController;
use App\Controllers\OneIdAuthController;
use App\Controllers\TeacherAuthController;
use App\Controllers\TeacherHemisOauthController;
use App\Controllers\TeacherDashboardController;
use App\Controllers\EysenckTestController;
use App\Controllers\IqTestController;
use App\Controllers\LusherTestController;
use App\Controllers\RanglarTestController;
use App\Controllers\AggressionTestController;
use App\Controllers\StudentDashboardController;
use App\Controllers\LocaleController;
use App\Helpers\SessionHelper;
use App\Helpers\ErrorHandler;
use Dotenv\Dotenv;

require __DIR__ . '/../vendor/autoload.php';

// Загружаем .env
$root = dirname(__DIR__);
if (file_exists($root . '/.env')) {
    Dotenv::createImmutable($root)->safeLoad();
}

// Настраиваем error_log для записи в файл
$logFile = $root . '/storage/error.log';
if (!file_exists($logFile)) {
    @touch($logFile);
    @chmod($logFile, 0666);
}
ini_set('error_log', $logFile);

// Регистрируем обработчики ошибок
if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
    ErrorHandler::register();
}

// Безопасный запуск сессии
SessionHelper::start();

$router = new Router();

// Публичные страницы
$router->get('/', [HomeController::class, 'index']);
$router->get('/locale', [LocaleController::class, 'set']);

// Личный кабинет студента
$router->get('/dashboard', [StudentDashboardController::class, 'index']);

// Публичные страницы (доступны всем)
$router->get('/students', [StudentController::class, 'list']);
$router->get('/students/show', [StudentController::class, 'show']);

// Кабинет психолога
$router->get('/admin/login', [AuthController::class, 'loginForm']);
$router->post('/admin/login', [AuthController::class, 'login']);
$router->get('/admin/logout', [AuthController::class, 'logout']);

$router->get('/admin', [AdminTestController::class, 'dashboard']);
$router->get('/admin/tests', [AdminTestController::class, 'index']);
$router->get('/admin/tests/create', [AdminTestController::class, 'createForm']);
$router->post('/admin/tests/create', [AdminTestController::class, 'store']);
$router->get('/admin/tests/show', [AdminTestController::class, 'show']);
$router->get('/admin/tests/edit', [AdminTestController::class, 'editForm']);
$router->post('/admin/tests/update', [AdminTestController::class, 'update']);
$router->get('/admin/tests/delete', [AdminTestController::class, 'delete']);
$router->get('/admin/tests/select-groups', [AdminTestController::class, 'selectGroupsForm']);
$router->post('/admin/tests/update-groups', [AdminTestController::class, 'updateGroups']);
$router->get('/admin/tests/select-teachers', [AdminTestController::class, 'selectTeachers']);
$router->post('/admin/tests/update-teachers', [AdminTestController::class, 'updateTeachers']);

// Управление преподавателями
$router->get('/admin/teachers', [AdminTeacherController::class, 'index']);
$router->post('/admin/teachers/delete', [AdminTeacherController::class, 'delete']);

// API endpoints для админа
$router->get('/admin/api/faculties', [ApiController::class, 'getFaculties']);
$router->get('/admin/api/specialties', [ApiController::class, 'getSpecialties']);
$router->get('/admin/api/groups', [ApiController::class, 'getGroups']);

// Результаты тестов
$router->get('/admin/results', [ResultsController::class, 'index']);
$router->get('/admin/results/test', [ResultsController::class, 'testResults']);
$router->get('/admin/results/test-students', [ResultsController::class, 'testStudents']);
$router->get('/admin/results/student', [ResultsController::class, 'studentResults']);
$router->get('/admin/results/eysenck', [ResultsController::class, 'eysenckResults']);
$router->get('/admin/results/statistics', [ResultsController::class, 'statistics']);
$router->get('/admin/results/analytics', [ResultsController::class, 'analytics']);
$router->get('/admin/results/export', [ResultsController::class, 'exportExcel']);
$router->post('/admin/results/clear', [ResultsController::class, 'clearAll']);
$router->post('/admin/results/clear-students', [ResultsController::class, 'clearStudentsOnly']);
$router->post('/admin/results/clear-teachers', [ResultsController::class, 'clearTeachersOnly']);
$router->get('/admin/results/test-groups', [ResultsController::class, 'testGroups']);
$router->get('/admin/results/group', [ResultsController::class, 'groupStatistics']);
$router->get('/admin/results/teachers', [ResultsController::class, 'teacherResults']);
$router->get('/admin/results/teacher-statistics', [ResultsController::class, 'teacherStatistics']);
$router->get('/admin/results/export-teachers', [ResultsController::class, 'exportTeacherExcel']);

// Синхронизация данных из HEMIS
$router->get('/admin/settings/sync', [SyncController::class, 'index']);
$router->post('/admin/settings/sync/import', [SyncController::class, 'import']);
$router->post('/admin/settings/sync/auto', [SyncController::class, 'autoSync']);
$router->post('/admin/settings/sync/clear', [SyncController::class, 'clear']);

// Публичное прохождение теста студентами
$router->get('/tests/take', [PublicTestController::class, 'takeForm']);
$router->post('/tests/take', [PublicTestController::class, 'submit']);
$router->get('/tests/results', [PublicTestController::class, 'showResults']);

// Авторизация через HEMIS (Student API)
$router->get('/hemis/login', [HemisAuthController::class, 'login']);
$router->post('/hemis/login', [HemisAuthController::class, 'loginPost']);
$router->get('/hemis/logout', [HemisAuthController::class, 'logout']);

// Авторизация через OneID
$router->get('/oneid/login', [OneIdAuthController::class, 'login']);
$router->get('/oneid/callback', [OneIdAuthController::class, 'callback']);
$router->get('/oneid/logout', [OneIdAuthController::class, 'logout']);

// Преподаватели - регистрация и авторизация
$router->get('/teachers/register', [TeacherAuthController::class, 'registerForm']);
$router->post('/teachers/register', [TeacherAuthController::class, 'register']);
$router->get('/teachers/login', [TeacherAuthController::class, 'loginForm']);
$router->post('/teachers/login', [TeacherAuthController::class, 'login']);
$router->get('/teachers/hemis/login', [TeacherHemisOauthController::class, 'login']);
$router->get('/teachers/hemis/callback', [TeacherHemisOauthController::class, 'callback']);
// HEMIS OAuth callback для преподавателей (альтернативный маршрут)
$router->get('/hemis/callback', [TeacherHemisOauthController::class, 'callback']);
$router->get('/teachers/logout', [TeacherAuthController::class, 'logout']);

// Личный кабинет преподавателя
$router->get('/teacher/dashboard', [TeacherDashboardController::class, 'index']);

// Тест Айзенка
$router->get('/eysenck/start', [EysenckTestController::class, 'start']);
$router->get('/eysenck/question', [EysenckTestController::class, 'question']);
$router->post('/eysenck/answer', [EysenckTestController::class, 'answer']);
$router->get('/eysenck/complete', [EysenckTestController::class, 'complete']);
$router->get('/eysenck/results', [EysenckTestController::class, 'results']);

// IQ тест
$router->get('/iq/start', [IqTestController::class, 'start']);
$router->get('/iq/question', [IqTestController::class, 'question']);
$router->post('/iq/answer', [IqTestController::class, 'answer']);
$router->get('/iq/complete', [IqTestController::class, 'complete']);
$router->get('/iq/results', [IqTestController::class, 'results']);

// Lyusher тест
$router->get('/lusher/start', [LusherTestController::class, 'start']);
$router->get('/lusher/round1', [LusherTestController::class, 'selectionRound1']);
$router->get('/lusher/round2', [LusherTestController::class, 'selectionRound2']);
$router->post('/lusher/select', [LusherTestController::class, 'processSelection']);
$router->get('/lusher/complete', [LusherTestController::class, 'complete']);
$router->get('/lusher/results', [LusherTestController::class, 'results']);

// Ranglar metodikasi routes
$router->get('/ranglar/start', [RanglarTestController::class, 'start']);
$router->get('/ranglar/select', [RanglarTestController::class, 'select']);
$router->post('/ranglar/select', [RanglarTestController::class, 'select']);
$router->get('/ranglar/complete', [RanglarTestController::class, 'complete']);
$router->get('/ranglar/results', [RanglarTestController::class, 'results']);

// Tajovuz holati tashxisi (Buss-Darki)
$router->get('/aggression/start', [AggressionTestController::class, 'start']);
$router->get('/aggression/question', [AggressionTestController::class, 'question']);
$router->post('/aggression/answer', [AggressionTestController::class, 'answer']);
$router->get('/aggression/complete', [AggressionTestController::class, 'complete']);
$router->get('/aggression/results', [AggressionTestController::class, 'results']);

$router->dispatch($_SERVER['REQUEST_METHOD'] ?? 'GET', $_SERVER['REQUEST_URI'] ?? '/');
