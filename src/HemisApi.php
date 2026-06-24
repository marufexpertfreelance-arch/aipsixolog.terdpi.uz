<?php
declare(strict_types=1);

namespace App;

use GuzzleHttp\Client;
use Dotenv\Dotenv;

/**
 * Класс-обёртка над API HEMIS (пока mock).
 */
class HemisApi
{
    private Client $client;
    private string $baseUrl;
    private ?string $apiKey;
    private bool $useMock;
    private Client $httpClient;
    private string $studentApiBaseUrl;

    public function __construct()
    {
        $root = dirname(__DIR__);
        if (file_exists($root . '/.env')) {
            $dotenv = Dotenv::createImmutable($root);
            $dotenv->safeLoad();
        }

        $this->baseUrl = $_ENV['HEMIS_BASE_URL'] ?? 'https://example-hemis.local/api';
        $this->apiKey  = $_ENV['HEMIS_API_KEY'] ?? null;
        $this->useMock = ($_ENV['HEMIS_USE_MOCK'] ?? 'true') === 'true';

        $this->client = new Client([
            'base_uri' => $this->baseUrl,
            'timeout'  => 5.0,
        ]);

        // Настройка HTTP клиента для Student API (как в HemisAuthController)
        $this->studentApiBaseUrl = $_ENV['HEMIS_API_BASE_URL'] ?? 'https://student.terdpi.uz';
        $this->studentApiBaseUrl = rtrim($this->studentApiBaseUrl, '/');
        
        $verifySsl = filter_var(
            $_ENV['HEMIS_OAUTH_VERIFY_SSL'] ?? false,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
        if ($verifySsl === null) {
            $verifySsl = false;
        }
        
        $this->httpClient = new Client([
            'base_uri' => $this->studentApiBaseUrl,
            'verify'   => $verifySsl,
            'timeout'  => 10.0,
            'headers'  => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /** @return array<int, array<string, mixed>> */
    private function getMockStudents(): array
    {
        return [
            [
                'id'        => 1,
                'full_name' => 'Ali Aliyev',
                'group'     => '202-20',
                'faculty'   => 'Информационные технологии',
                'status'    => 'Активен',
            ],
            [
                'id'        => 2,
                'full_name' => 'Laylo Karimova',
                'group'     => '103-21',
                'faculty'   => 'Психология',
                'status'    => 'Активен',
            ],
            [
                'id'        => 3,
                'full_name' => 'Javlon Bekov',
                'group'     => '401-19',
                'faculty'   => 'Юриспруденция',
                'status'    => 'Академический отпуск',
            ],
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function getStudents(): array
    {
        if ($this->useMock) {
            return $this->getMockStudents();
        }

        try {
            $response = $this->client->get('/students', [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept'        => 'application/json',
                ],
            ]);

            $data = json_decode((string) $response->getBody(), true);
            return $data['students'] ?? [];
        } catch (\Throwable $e) {
            // Если ошибка при обращении к API, логируем и возвращаем пустой массив
            // В продакшене лучше использовать logger
            error_log('HemisApi::getStudents() error: ' . $e->getMessage());
            
            // Для отладки: если API недоступен, можно вернуть mock-данные
            // Раскомментируйте следующую строку для локальной разработки:
            // return $this->getMockStudents();
            
            return [];
        }
    }

    /** @return array<string, mixed>|null */
    public function getStudentById(int $id): ?array
    {
        if ($this->useMock) {
            foreach ($this->getStudents() as $student) {
                if ((int) $student['id'] === $id) {
                    $student['risk_level'] = 'Средний';
                    $student['notes'] = 'Рекомендуется беседа с психологом в ближайший месяц.';
                    $student['last_consultation'] = '2025-11-01';
                    $student['tests'] = [
                        [
                            'name'   => 'Шкала тревожности',
                            'result' => 'Умеренная тревожность',
                            'date'   => '2025-10-10',
                        ],
                    ];
                    return $student;
                }
            }
            return null;
        }

        try {
            $response = $this->client->get('/students/' . $id, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $this->apiKey,
                    'Accept'        => 'application/json',
                ],
            ]);

            if ($response->getStatusCode() === 404) {
                return null;
            }

            $data = json_decode((string) $response->getBody(), true);
            return $data;
        } catch (\Throwable $e) {
            // Если ошибка при обращении к API, логируем и возвращаем null
            error_log('HemisApi::getStudentById() error: ' . $e->getMessage());
            return null;
        }
    }

    /**
     * Получить список факультетов
     * Приоритет: загруженные данные из HEMIS API, затем данные студентов
     * @return array<int, array<string, mixed>>
     */
    public function getFaculties(): array
    {
        // Сначала пробуем загруженные данные из HEMIS API
        $structureService = new \App\Services\UniversityStructureService();
        $faculties = $structureService->getFaculties();
        
        if (!empty($faculties)) {
            return $faculties;
        }
        
        // Fallback: данные из StudentStorage
        return $this->getFacultiesFromStudents();
    }

    /**
     * Получить группы по факультету
     * Приоритет: загруженные данные из HEMIS API, затем данные студентов
     * @param string|int $facultyId ID или название факультета
     * @return array<int, array<string, mixed>>
     */
    public function getGroupsByFaculty($facultyId): array
    {
        // Сначала пробуем загруженные данные из HEMIS API
        $structureService = new \App\Services\UniversityStructureService();
        if ($structureService->hasData()) {
            return $structureService->getGroups($facultyId);
        }
        
        // Fallback: данные из StudentStorage
        return $this->getGroupsFromStudents($facultyId);
    }

    /**
     * Получить все группы
     * Приоритет: загруженные данные из HEMIS API, затем данные студентов
     * @return array<int, array<string, mixed>>
     */
    public function getAllGroups(): array
    {
        // Сначала пробуем загруженные данные из HEMIS API
        $structureService = new \App\Services\UniversityStructureService();
        if ($structureService->hasData()) {
            return $structureService->getGroups();
        }
        
        // Fallback: данные из StudentStorage
        return $this->getGroupsFromStudents();
    }

    /**
     * Получить факультеты из данных студентов (fallback)
     * @return array<int, array<string, mixed>>
     */
    private function getFacultiesFromStudents(): array
    {
        $studentStorage = new \App\Services\StudentStorage();
        $students = $studentStorage->getAll();
        
        if (empty($students)) {
            return [];
        }
        
        $faculties = [];
        
        foreach ($students as $student) {
            $facultyName = $student['faculty'] ?? null;
            if ($facultyName && !isset($faculties[$facultyName])) {
                $faculties[$facultyName] = [
                    'id' => count($faculties) + 1,
                    'name' => $facultyName,
                ];
            }
        }
        
        return array_values($faculties);
    }

    /**
     * Получить группы из данных студентов (fallback)
     * @param string|int|null $facultyId
     * @return array<int, array<string, mixed>>
     */
    private function getGroupsFromStudents($facultyId = null): array
    {
        $studentStorage = new \App\Services\StudentStorage();
        $students = $studentStorage->getAll();
        
        if (empty($students)) {
            return [];
        }
        
        $groups = [];
        
        // Нормализация для сравнения: приводим к нижнему регистру, удаляем все типы апострофов
        $normalize = function($name) {
            if ($name === null || $name === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$name));
            // Заменяем все типы апострофов и кавычек на обычный апостроф для единообразия
            $apostrophes = [
                "\xCA\xBC",     // U+02BC MODIFIER LETTER APOSTROPHE (узбекский)
                "\xE2\x80\x99", // U+2019 RIGHT SINGLE QUOTATION MARK
                "\xE2\x80\x98", // U+2018 LEFT SINGLE QUOTATION MARK
                "\xE2\x80\x9C", // U+201C LEFT DOUBLE QUOTATION MARK
                "\xE2\x80\x9D", // U+201D RIGHT DOUBLE QUOTATION MARK
                '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            // Нормализуем пробелы
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };
        
        $facultyIdNormalized = $facultyId !== null ? $normalize($facultyId) : null;
        
        foreach ($students as $student) {
            $groupName = $student['group'] ?? null;
            $facultyName = $student['faculty'] ?? null;
            
            if (empty($groupName)) {
                continue;
            }
            
            // Если указан факультет, фильтруем по нему
            if ($facultyIdNormalized !== null) {
                $studentFacultyNormalized = $normalize($facultyName);
                if ($studentFacultyNormalized !== $facultyIdNormalized) {
                    continue;
                }
            }
            
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = [
                    'id' => $groupName,
                    'name' => $groupName,
                    'faculty' => $facultyName,
                    'specialty' => $student['specialty'] ?? null,
                ];
            }
        }
        
        return array_values($groups);
    }

    /**
     * Форматировать факультеты
     * @param array<int, array<string, mixed>> $data
     * @return array<int, array<string, mixed>>
     */
    private function formatFaculties(array $data): array
    {
        $faculties = [];
        foreach ($data as $item) {
            $faculties[] = [
                'id' => $item['id'] ?? $item['faculty_id'] ?? null,
                'name' => $item['name'] ?? $item['faculty_name'] ?? '',
            ];
        }
        return $faculties;
    }

    /**
     * Форматировать группы
     * @param array<int, array<string, mixed>> $data
     * @return array<int, array<string, mixed>>
     */
    private function formatGroups(array $data): array
    {
        $groups = [];
        foreach ($data as $item) {
            $groups[] = [
                'id' => $item['id'] ?? $item['group_id'] ?? $item['name'] ?? '',
                'name' => $item['name'] ?? $item['group_name'] ?? '',
                'faculty' => $item['faculty'] ?? $item['faculty_name'] ?? null,
            ];
        }
        return $groups;
    }

    /**
     * Получить направления по факультету
     * Приоритет: загруженные данные из HEMIS API, затем данные студентов
     * @param string|int $facultyId ID или название факультета
     * @return array<int, array<string, mixed>>
     */
    public function getSpecialtiesByFaculty($facultyId): array
    {
        // Сначала пробуем загруженные данные из HEMIS API
        $structureService = new \App\Services\UniversityStructureService();
        if ($structureService->hasData()) {
            return $structureService->getSpecialties($facultyId);
        }
        
        // Fallback: данные из StudentStorage
        return $this->getSpecialtiesFromStudents($facultyId);
    }

    /**
     * Получить группы по направлению и факультету
     * Приоритет: загруженные данные из HEMIS API, затем данные студентов
     * @param string $specialty Название направления
     * @param string|int|null $facultyId ID или название факультета (опционально)
     * @return array<int, array<string, mixed>>
     */
    public function getGroupsBySpecialty(string $specialty, $facultyId = null): array
    {
        // Сначала пробуем загруженные данные из HEMIS API
        $structureService = new \App\Services\UniversityStructureService();
        if ($structureService->hasData()) {
            return $structureService->getGroupsBySpecialtyName($specialty, $facultyId);
        }
        
        // Fallback: данные из StudentStorage
        return $this->getGroupsBySpecialtyFromStudents($specialty, $facultyId);
    }

    /**
     * Получить направления из данных студентов (fallback)
     * @param string|int|null $facultyId
     * @return array<int, array<string, mixed>>
     */
    private function getSpecialtiesFromStudents($facultyId = null): array
    {
        $studentStorage = new \App\Services\StudentStorage();
        $students = $studentStorage->getAll();
        
        if (empty($students)) {
            return [];
        }
        
        $specialties = [];
        
        // Нормализация для сравнения: приводим к нижнему регистру, удаляем все типы апострофов
        $normalize = function($name) {
            if ($name === null || $name === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$name));
            // Заменяем все типы апострофов и кавычек на обычный апостроф для единообразия
            $apostrophes = [
                "\xCA\xBC",     // U+02BC MODIFIER LETTER APOSTROPHE (узбекский)
                "\xE2\x80\x99", // U+2019 RIGHT SINGLE QUOTATION MARK
                "\xE2\x80\x98", // U+2018 LEFT SINGLE QUOTATION MARK
                "\xE2\x80\x9C", // U+201C LEFT DOUBLE QUOTATION MARK
                "\xE2\x80\x9D", // U+201D RIGHT DOUBLE QUOTATION MARK
                '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            // Нормализуем пробелы
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };
        
        $facultyIdNormalized = $facultyId !== null ? $normalize($facultyId) : null;
        
        foreach ($students as $student) {
            $specialtyName = $student['specialty'] ?? null;
            $facultyName = $student['faculty'] ?? null;
            
            if (empty($specialtyName)) {
                continue;
            }
            
            // Если указан факультет, фильтруем по нему
            if ($facultyIdNormalized !== null) {
                $studentFacultyNormalized = $normalize($facultyName);
                if ($studentFacultyNormalized !== $facultyIdNormalized) {
                    continue;
                }
            }
            
            // Используем название направления как ключ для уникальности
            if (!isset($specialties[$specialtyName])) {
                $specialties[$specialtyName] = [
                    'id' => $specialtyName,
                    'name' => $specialtyName,
                    'faculty' => $facultyName,
                ];
            }
        }
        
        return array_values($specialties);
    }

    /**
     * Получить группы по направлению из данных студентов (fallback)
     * @param string $specialty Название направления
     * @param string|int|null $facultyId ID или название факультета (опционально)
     * @return array<int, array<string, mixed>>
     */
    private function getGroupsBySpecialtyFromStudents(string $specialty, $facultyId = null): array
    {
        $studentStorage = new \App\Services\StudentStorage();
        $students = $studentStorage->getAll();
        
        if (empty($students)) {
            return [];
        }
        
        $groups = [];
        
        // Нормализация для сравнения: приводим к нижнему регистру, удаляем все типы апострофов
        $normalize = function($name) {
            if ($name === null || $name === '') {
                return '';
            }
            $normalized = mb_strtolower(trim((string)$name));
            // Заменяем все типы апострофов и кавычек на обычный апостроф для единообразия
            $apostrophes = [
                "\xCA\xBC",     // U+02BC MODIFIER LETTER APOSTROPHE (узбекский)
                "\xE2\x80\x99", // U+2019 RIGHT SINGLE QUOTATION MARK
                "\xE2\x80\x98", // U+2018 LEFT SINGLE QUOTATION MARK
                "\xE2\x80\x9C", // U+201C LEFT DOUBLE QUOTATION MARK
                "\xE2\x80\x9D", // U+201D RIGHT DOUBLE QUOTATION MARK
                '"', '`'
            ];
            $normalized = str_replace($apostrophes, "'", $normalized);
            // Нормализуем пробелы
            $normalized = preg_replace('/\s+/', ' ', $normalized);
            return trim($normalized);
        };
        
        $specialtyNormalized = $normalize($specialty);
        $facultyIdNormalized = $facultyId !== null ? $normalize($facultyId) : null;
        
        // Логируем для отладки
        error_log("HemisApi::getGroupsBySpecialtyFromStudents() - Input:");
        error_log("  specialty (raw): " . var_export($specialty, true));
        error_log("  specialty (normalized): " . var_export($specialtyNormalized, true));
        error_log("  facultyId (raw): " . var_export($facultyId, true));
        error_log("  facultyId (normalized): " . var_export($facultyIdNormalized, true));
        error_log("  students count: " . count($students));
        
        foreach ($students as $student) {
            $groupName = $student['group'] ?? null;
            $studentSpecialty = $student['specialty'] ?? null;
            $facultyName = $student['faculty'] ?? null;
            
            if (empty($groupName) || empty($studentSpecialty)) {
                continue;
            }
            
            // Простое точное сравнение направления
            $studentSpecialtyNormalized = $normalize($studentSpecialty);
            
            // Логируем сравнение для первых 3 студентов
            if (count($groups) < 3) {
                error_log("Comparing:");
                error_log("  Search: '{$specialty}' -> '{$specialtyNormalized}'");
                error_log("  Student: '{$studentSpecialty}' -> '{$studentSpecialtyNormalized}'");
                error_log("  Match: " . ($studentSpecialtyNormalized === $specialtyNormalized ? 'YES ✓' : 'NO ✗'));
            }
            
            if ($studentSpecialtyNormalized !== $specialtyNormalized) {
                continue;
            }
            
            // Если указан факультет, проверяем его тоже
            if ($facultyIdNormalized !== null) {
                $studentFacultyNormalized = $normalize($facultyName);
                if ($studentFacultyNormalized !== $facultyIdNormalized) {
                    continue;
                }
            }
            
            // Нашли совпадение
            if (!isset($groups[$groupName])) {
                $groups[$groupName] = [
                    'id' => $groupName,
                    'name' => $groupName,
                    'specialty' => $studentSpecialty,
                    'faculty' => $facultyName,
                ];
            }
        }
        
        return array_values($groups);
    }

    /**
     * Форматировать направления
     * @param array<int, array<string, mixed>> $data
     * @return array<int, array<string, mixed>>
     */
    private function formatSpecialties(array $data): array
    {
        $specialties = [];
        foreach ($data as $item) {
            $specialties[] = [
                'id' => $item['id'] ?? $item['specialty_id'] ?? $item['name'] ?? '',
                'name' => $item['name'] ?? $item['specialty_name'] ?? '',
                'faculty' => $item['faculty'] ?? $item['faculty_name'] ?? null,
            ];
        }
        return $specialties;
    }


}
