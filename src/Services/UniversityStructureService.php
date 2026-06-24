<?php
declare(strict_types=1);

namespace App\Services;

use GuzzleHttp\Client;

/**
 * Сервис для работы со структурой университета (факультеты, направления, группы)
 * Данные загружаются из HEMIS API и сохраняются локально
 */
final class UniversityStructureService
{
    private string $storageFile;
    private ?array $cachedData = null;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $storageDir = $root . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $this->storageFile = $storageDir . '/university_structure.json';
    }

    /**
     * Импортировать данные из JSON файла HEMIS API
     * @param string $jsonFilePath Путь к JSON файлу с ответом API
     * @return array Статистика импорта
     */
    public function importFromJsonFile(string $jsonFilePath): array
    {
        if (!file_exists($jsonFilePath)) {
            throw new \RuntimeException("Файл не найден: {$jsonFilePath}");
        }

        $jsonContent = file_get_contents($jsonFilePath);
        if ($jsonContent === false) {
            throw new \RuntimeException("Не удалось прочитать файл: {$jsonFilePath}");
        }

        $data = json_decode($jsonContent, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \RuntimeException("Ошибка парсинга JSON: " . json_last_error_msg());
        }

        return $this->importFromApiResponse($data);
    }

    /**
     * Автоматически загрузить и импортировать все страницы group-list из HEMIS Student API.
     *
     * @param int $limit Размер страницы (рекомендуется 200)
     * @param int $startPage С какой страницы начинать
     * @param int|null $maxPages Ограничение количества страниц (null = все)
     * @param string|null $bearerToken Токен (если endpoint требует авторизацию)
     * @return array Итоговая статистика
     */
    public function fetchAndImportAllGroups(int $limit = 200, int $startPage = 1, ?int $maxPages = null, ?string $bearerToken = null): array
    {
        if ($limit < 1) {
            $limit = 200;
        }
        if ($startPage < 1) {
            $startPage = 1;
        }

        $baseUrl = (getenv('HEMIS_API_BASE_URL') ?: ($_ENV['HEMIS_API_BASE_URL'] ?? 'https://student.terdpi.uz'));
        $baseUrl = rtrim((string) $baseUrl, '/');

        $verifySslRaw = getenv('HEMIS_OAUTH_VERIFY_SSL') ?: ($_ENV['HEMIS_OAUTH_VERIFY_SSL'] ?? '');
        $verifySsl = filter_var(
            $verifySslRaw !== '' ? $verifySslRaw : false,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
        if ($verifySsl === null) {
            $verifySsl = false;
        }

        $client = new Client([
            'base_uri' => $baseUrl,
            'verify' => $verifySsl,
            'timeout' => 60.0,
            'connect_timeout' => 15.0,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $page = $startPage;
        $processedPages = 0;
        $totalPages = null;
        $totalCount = null;

        $overall = [
            'pages_processed' => 0,
            'faculties_imported' => 0,
            'specialties_imported' => 0,
            'groups_imported' => 0,
            'faculties_total' => 0,
            'specialties_total' => 0,
            'groups_total' => 0,
            'pagination' => null,
        ];

        while (true) {
            if ($maxPages !== null && $processedPages >= $maxPages) {
                break;
            }

            $headers = [];
            if (!empty($bearerToken)) {
                $headers['Authorization'] = 'Bearer ' . $bearerToken;
            }

            $response = $client->get('/rest/v1/data/group-list', [
                'query' => [
                    'page' => $page,
                    'limit' => $limit,
                ],
                'headers' => $headers,
                'http_errors' => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body = (string)$response->getBody();
            $data = json_decode($body, true);

            if ($statusCode < 200 || $statusCode >= 300) {
                $message = $data['message'] ?? $data['error'] ?? ('HTTP ' . $statusCode);
                throw new \RuntimeException('HEMIS group-list request failed: ' . $message);
            }
            if (!is_array($data) || !($data['success'] ?? false)) {
                $message = $data['message'] ?? $data['error'] ?? 'Unknown API error';
                throw new \RuntimeException('HEMIS API error: ' . $message);
            }

            $pagination = $data['data']['pagination'] ?? null;
            if (is_array($pagination)) {
                $totalPages = $pagination['pageCount'] ?? $totalPages;
                $totalCount = $pagination['totalCount'] ?? $totalCount;
            }

            $stats = $this->importFromApiResponse($data);

            $processedPages++;
            $overall['pages_processed'] = $processedPages;
            $overall['faculties_imported'] += (int)($stats['faculties_imported'] ?? 0);
            $overall['specialties_imported'] += (int)($stats['specialties_imported'] ?? 0);
            $overall['groups_imported'] += (int)($stats['groups_imported'] ?? 0);
            $overall['faculties_total'] = (int)($stats['faculties_total'] ?? $overall['faculties_total']);
            $overall['specialties_total'] = (int)($stats['specialties_total'] ?? $overall['specialties_total']);
            $overall['groups_total'] = (int)($stats['groups_total'] ?? $overall['groups_total']);

            $overall['pagination'] = $stats['pagination'] ?? $pagination;
            if (is_array($overall['pagination'])) {
                if ($totalPages !== null) {
                    $overall['pagination']['pageCount'] = $totalPages;
                }
                if ($totalCount !== null) {
                    $overall['pagination']['totalCount'] = $totalCount;
                }
            }

            if ($totalPages !== null && $page >= $totalPages) {
                break;
            }

            $page++;
        }

        return $overall;
    }

    /**
     * Импортировать данные из ответа HEMIS API
     * @param array $apiResponse Ответ API (массив с success, data, etc.)
     * @return array Статистика импорта
     */
    public function importFromApiResponse(array $apiResponse): array
    {
        if (!isset($apiResponse['success']) || !$apiResponse['success']) {
            throw new \RuntimeException("API вернул ошибку: " . ($apiResponse['error'] ?? 'Неизвестная ошибка'));
        }

        $items = $apiResponse['data']['items'] ?? [];
        if (empty($items)) {
            throw new \RuntimeException(
                "Import uchun ma'lumot yo'q. JSON faylda data.items (guruhlar ro'yxati) bo'lishi kerak. HEMIS group-list API javobini yuklang."
            );
        }

        // Парсим данные
        $faculties = [];
        $specialties = [];
        $groups = [];

        foreach ($items as $item) {
            // Группа
            $groupId = $item['id'] ?? null;
            $groupName = $item['name'] ?? null;
            
            if (!$groupId || !$groupName) {
                continue;
            }

            // Факультет (department)
            $department = $item['department'] ?? null;
            if ($department) {
                $facultyId = $department['id'] ?? null;
                $facultyName = $department['name'] ?? null;
                $facultyCode = $department['code'] ?? null;

                if ($facultyId && $facultyName && !isset($faculties[$facultyId])) {
                    $faculties[$facultyId] = [
                        'id' => $facultyId,
                        'name' => $facultyName,
                        'code' => $facultyCode,
                    ];
                }
            }

            // Направление (specialty)
            $specialty = $item['specialty'] ?? null;
            if ($specialty) {
                $specialtyId = $specialty['id'] ?? null;
                $specialtyName = $specialty['name'] ?? null;
                $specialtyCode = $specialty['code'] ?? null;

                if ($specialtyId && $specialtyName && !isset($specialties[$specialtyId])) {
                    $specialties[$specialtyId] = [
                        'id' => $specialtyId,
                        'name' => $specialtyName,
                        'code' => $specialtyCode,
                        'faculty_id' => $department['id'] ?? null,
                        'faculty_name' => $department['name'] ?? null,
                    ];
                }
            }

            // Группа
            $groups[$groupId] = [
                'id' => $groupId,
                'name' => $groupName,
                'faculty_id' => $department['id'] ?? null,
                'faculty_name' => $department['name'] ?? null,
                'specialty_id' => $specialty['id'] ?? null,
                'specialty_name' => $specialty['name'] ?? null,
                'education_lang' => $item['educationLang']['name'] ?? null,
                'active' => $item['active'] ?? true,
            ];
        }

        // Загружаем существующие данные для объединения
        $existingData = $this->loadData();
        
        // Объединяем с существующими данными
        $existingFaculties = $existingData['faculties'] ?? [];
        $existingSpecialties = $existingData['specialties'] ?? [];
        $existingGroups = $existingData['groups'] ?? [];

        // Преобразуем в ассоциативные массивы для объединения
        $existingFacultiesById = [];
        foreach ($existingFaculties as $f) {
            $existingFacultiesById[$f['id']] = $f;
        }
        $existingSpecialtiesById = [];
        foreach ($existingSpecialties as $s) {
            $existingSpecialtiesById[$s['id']] = $s;
        }
        $existingGroupsById = [];
        foreach ($existingGroups as $g) {
            $existingGroupsById[$g['id']] = $g;
        }

        // Объединяем (новые данные перезаписывают старые)
        $mergedFaculties = array_merge($existingFacultiesById, $faculties);
        $mergedSpecialties = array_merge($existingSpecialtiesById, $specialties);
        $mergedGroups = array_merge($existingGroupsById, $groups);

        // Сохраняем
        $dataToSave = [
            'faculties' => array_values($mergedFaculties),
            'specialties' => array_values($mergedSpecialties),
            'groups' => array_values($mergedGroups),
            'last_updated' => date('Y-m-d H:i:s'),
            'pagination' => $apiResponse['data']['pagination'] ?? null,
        ];

        $this->saveData($dataToSave);
        $this->cachedData = $dataToSave;

        return [
            'faculties_imported' => count($faculties),
            'specialties_imported' => count($specialties),
            'groups_imported' => count($groups),
            'faculties_total' => count($mergedFaculties),
            'specialties_total' => count($mergedSpecialties),
            'groups_total' => count($mergedGroups),
            'pagination' => $apiResponse['data']['pagination'] ?? null,
        ];
    }

    /**
     * Получить все факультеты
     * @return array<int, array<string, mixed>>
     */
    public function getFaculties(): array
    {
        $data = $this->loadData();
        return $data['faculties'] ?? [];
    }

    /**
     * Получить все направления
     * @param int|string|null $facultyId Фильтр по факультету (опционально)
     * @return array<int, array<string, mixed>>
     */
    public function getSpecialties($facultyId = null): array
    {
        $data = $this->loadData();
        $specialties = $data['specialties'] ?? [];

        if ($facultyId !== null) {
            $specialties = array_filter($specialties, function ($s) use ($facultyId) {
                return (string)($s['faculty_id'] ?? '') === (string)$facultyId 
                    || (string)($s['faculty_name'] ?? '') === (string)$facultyId;
            });
        }

        return array_values($specialties);
    }

    /**
     * Получить все группы
     * @param int|string|null $facultyId Фильтр по факультету (опционально)
     * @param int|string|null $specialtyId Фильтр по направлению (опционально)
     * @return array<int, array<string, mixed>>
     */
    public function getGroups($facultyId = null, $specialtyId = null): array
    {
        $data = $this->loadData();
        $groups = $data['groups'] ?? [];

        if ($facultyId !== null) {
            $groups = array_filter($groups, function ($g) use ($facultyId) {
                return (string)($g['faculty_id'] ?? '') === (string)$facultyId 
                    || (string)($g['faculty_name'] ?? '') === (string)$facultyId;
            });
        }

        if ($specialtyId !== null) {
            $groups = array_filter($groups, function ($g) use ($specialtyId) {
                return (string)($g['specialty_id'] ?? '') === (string)$specialtyId 
                    || (string)($g['specialty_name'] ?? '') === (string)$specialtyId;
            });
        }

        return array_values($groups);
    }

    /**
     * Получить группы по направлению (с нормализацией названий)
     * @param string $specialtyName Название направления
     * @param string|null $facultyName Название факультета (опционально)
     * @return array<int, array<string, mixed>>
     */
    public function getGroupsBySpecialtyName(string $specialtyName, ?string $facultyName = null): array
    {
        $data = $this->loadData();
        $groups = $data['groups'] ?? [];

        $normalize = function($name) {
            if ($name === null || $name === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$name));
            // Заменяем все типы апострофов
            $apostrophes = [
                "\xCA\xBC", "\xE2\x80\x99", "\xE2\x80\x98",
                "\xE2\x80\x9C", "\xE2\x80\x9D", '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };

        $specialtyNormalized = $normalize($specialtyName);
        $facultyNormalized = $facultyName !== null ? $normalize($facultyName) : null;

        $result = [];
        foreach ($groups as $group) {
            $groupSpecialtyNormalized = $normalize($group['specialty_name'] ?? '');
            
            if ($groupSpecialtyNormalized !== $specialtyNormalized) {
                continue;
            }

            if ($facultyNormalized !== null) {
                $groupFacultyNormalized = $normalize($group['faculty_name'] ?? '');
                if ($groupFacultyNormalized !== $facultyNormalized) {
                    continue;
                }
            }

            $result[] = $group;
        }

        return $result;
    }

    /**
     * Получить дату последнего обновления
     * @return string|null
     */
    public function getLastUpdated(): ?string
    {
        $data = $this->loadData();
        return $data['last_updated'] ?? null;
    }

    /**
     * Получить статистику
     * @return array
     */
    public function getStatistics(): array
    {
        $data = $this->loadData();
        return [
            'faculties_count' => count($data['faculties'] ?? []),
            'specialties_count' => count($data['specialties'] ?? []),
            'groups_count' => count($data['groups'] ?? []),
            'last_updated' => $data['last_updated'] ?? null,
            'pagination' => $data['pagination'] ?? null,
        ];
    }

    /**
     * Проверить, есть ли загруженные данные
     * @return bool
     */
    public function hasData(): bool
    {
        $data = $this->loadData();
        return !empty($data['groups']);
    }

    /**
     * Очистить все данные
     */
    public function clearData(): void
    {
        if (file_exists($this->storageFile)) {
            unlink($this->storageFile);
        }
        $this->cachedData = null;
    }

    /**
     * Загрузить данные из файла
     * @return array
     */
    private function loadData(): array
    {
        if ($this->cachedData !== null) {
            return $this->cachedData;
        }

        if (!file_exists($this->storageFile)) {
            return [];
        }

        $json = file_get_contents($this->storageFile);
        if ($json === false || $json === '') {
            return [];
        }

        $data = json_decode($json, true);
        $this->cachedData = is_array($data) ? $data : [];
        
        return $this->cachedData;
    }

    /**
     * Сохранить данные в файл
     * @param array $data
     */
    private function saveData(array $data): void
    {
        file_put_contents(
            $this->storageFile,
            json_encode($data, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}
