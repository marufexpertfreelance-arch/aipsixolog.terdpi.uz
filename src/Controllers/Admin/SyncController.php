<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\View;
use App\Services\UniversityStructureService;

class SyncController
{
    public function __construct()
    {
        $this->ensureAuth();
    }

    private function ensureAuth(): void
    {
        if (empty($_SESSION['admin_logged_in']) && empty($_SESSION['admin_id'])) {
            header('Location: /admin/login');
            exit;
        }
    }

    /**
     * Страница синхронизации данных
     */
    public function index(): void
    {
        $structureService = new UniversityStructureService();
        $stats = $structureService->getStatistics();

        echo View::render('admin/settings/sync', [
            'title' => 'Ma\'lumotlarni sinxronlash',
            'stats' => $stats,
        ]);
    }

    /**
     * Импорт данных из загруженного JSON файла
     */
    public function import(): void
    {
        $result = [
            'success' => false,
            'message' => '',
            'stats' => null,
        ];

        try {
            // Проверяем, загружен ли файл
            if (!isset($_FILES['json_file']) || $_FILES['json_file']['error'] !== UPLOAD_ERR_OK) {
                throw new \RuntimeException('Fayl yuklanmadi yoki xatolik yuz berdi.');
            }

            $uploadedFile = $_FILES['json_file'];
            
            // Проверяем тип файла
            $finfo = finfo_open(FILEINFO_MIME_TYPE);
            $mimeType = finfo_file($finfo, $uploadedFile['tmp_name']);
            finfo_close($finfo);

            if ($mimeType !== 'application/json' && $mimeType !== 'text/plain') {
                // Пробуем прочитать как JSON
                $content = file_get_contents($uploadedFile['tmp_name']);
                $data = json_decode($content, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('Fayl JSON formatida emas.');
                }
            } else {
                $content = file_get_contents($uploadedFile['tmp_name']);
                $data = json_decode($content, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    throw new \RuntimeException('JSON parsing xatosi: ' . json_last_error_msg());
                }
            }

            // Импортируем данные
            $structureService = new UniversityStructureService();
            $stats = $structureService->importFromApiResponse($data);

            $result['success'] = true;
            $result['message'] = sprintf(
                'Ma\'lumotlar muvaffaqiyatli yuklandi! Fakultetlar: %d, Yo\'nalishlar: %d, Guruhlar: %d',
                $stats['faculties_total'],
                $stats['specialties_total'],
                $stats['groups_total']
            );
            $result['stats'] = $stats;

            // Сохраняем сообщение в сессию
            $_SESSION['sync_success'] = $result['message'];

        } catch (\Throwable $e) {
            $result['message'] = 'Xatolik: ' . $e->getMessage();
            $_SESSION['sync_error'] = $result['message'];
        }

        // Редирект обратно на страницу синхронизации
        header('Location: /admin/settings/sync');
        exit;
    }

    /**
     * Очистить данные структуры
     */
    public function clear(): void
    {
        try {
            $structureService = new UniversityStructureService();
            $structureService->clearData();
            $_SESSION['sync_success'] = 'Barcha ma\'lumotlar o\'chirildi.';
        } catch (\Throwable $e) {
            $_SESSION['sync_error'] = 'Xatolik: ' . $e->getMessage();
        }

        header('Location: /admin/settings/sync');
        exit;
    }

    /**
     * Автосинхронизация: скачать group-list напрямую из HEMIS и импортировать
     */
    public function autoSync(): void
    {
        try {
            $limit = (int)($_POST['limit'] ?? 200);
            $startPage = (int)($_POST['start_page'] ?? 1);
            $maxPagesRaw = trim((string)($_POST['max_pages'] ?? ''));
            $maxPages = $maxPagesRaw === '' ? null : (int)$maxPagesRaw;
            $token = trim((string)($_POST['bearer_token'] ?? ''));
            $token = $token === '' ? null : $token;

            $structureService = new UniversityStructureService();
            $stats = $structureService->fetchAndImportAllGroups($limit, $startPage, $maxPages, $token);

            $groupsTotal = (int)($stats['groups_total'] ?? 0);
            if ($groupsTotal === 0) {
                $_SESSION['sync_error'] = 'Guruhlar topilmadi. API bo\'sh javob qaytardi. Token va HEMIS API manzilini tekshiring (.env: HEMIS_API_BASE_URL).';
            } else {
                $_SESSION['sync_success'] = sprintf(
                    'Avto-sinxronlash yakunlandi! Sahifalar: %d, Fakultetlar: %d, Yo\'nalishlar: %d, Guruhlar: %d',
                    $stats['pages_processed'] ?? 0,
                    $stats['faculties_total'] ?? 0,
                    $stats['specialties_total'] ?? 0,
                    $stats['groups_total'] ?? 0
                );
            }
        } catch (\Throwable $e) {
            $_SESSION['sync_error'] = 'Xatolik: ' . $e->getMessage();
        }

        header('Location: /admin/settings/sync');
        exit;
    }
}
