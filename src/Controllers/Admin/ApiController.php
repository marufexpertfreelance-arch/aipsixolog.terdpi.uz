<?php
declare(strict_types=1);

namespace App\Controllers\Admin;

use App\HemisApi;

class ApiController
{
    private HemisApi $hemisApi;

    public function __construct()
    {
        $this->hemisApi = new HemisApi();
    }

    private function ensureAuth(): void
    {
        if (!isset($_SESSION['admin_id'])) {
            http_response_code(401);
            header('Content-Type: application/json');
            echo json_encode(['error' => 'Unauthorized']);
            exit;
        }
    }

    /**
     * Получить список факультетов
     */
    public function getFaculties(): void
    {
        $this->ensureAuth();

        header('Content-Type: application/json');
        
        try {
            $faculties = $this->hemisApi->getFaculties();
            echo json_encode([
                'success' => true,
                'data' => $faculties,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }
    }

    /**
     * Получить группы по факультету с поддержкой пагинации, поиска и фильтров
     */
    public function getGroups(): void
    {
        $this->ensureAuth();

        header('Content-Type: application/json');
        
        $facultyId = $_GET['faculty_id'] ?? null;
        $facultyName = $_GET['faculty_name'] ?? null;
        $specialty = $_GET['specialty'] ?? null;
        
        // Новые параметры для пагинации и фильтрации
        $page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
        $perPage = isset($_GET['per_page']) ? max(1, min(200, (int)$_GET['per_page'])) : 50;
        $search = trim($_GET['search'] ?? '');
        $facultyFilter = trim($_GET['faculty_filter'] ?? '');
        $specialtyFilter = trim($_GET['specialty_filter'] ?? '');

        try {
            // Логируем параметры для отладки
            error_log('ApiController::getGroups() - specialty: ' . ($specialty ?? 'null'));
            error_log('ApiController::getGroups() - faculty_id: ' . ($facultyId ?? 'null'));
            error_log('ApiController::getGroups() - faculty_name: ' . ($facultyName ?? 'null'));
            error_log('ApiController::getGroups() - page: ' . $page . ', per_page: ' . $perPage);
            error_log('ApiController::getGroups() - search: ' . ($search ?: 'empty'));
            error_log('ApiController::getGroups() - faculty_filter: ' . ($facultyFilter ?: 'empty'));
            error_log('ApiController::getGroups() - specialty_filter: ' . ($specialtyFilter ?: 'empty'));
            
            // Получаем все группы согласно старым параметрам (для обратной совместимости)
            if ($specialty) {
                $faculty = $facultyName ?? $facultyId;
                error_log('ApiController::getGroups() - Getting groups by specialty: ' . $specialty . ', faculty: ' . ($faculty ?? 'null'));
                $groups = $this->hemisApi->getGroupsBySpecialty($specialty, $faculty);
            } elseif ($facultyId || $facultyName) {
                $faculty = $facultyName ?? $facultyId;
                error_log('ApiController::getGroups() - Getting groups by faculty: ' . $faculty);
                $groups = $this->hemisApi->getGroupsByFaculty($faculty);
            } else {
                error_log('ApiController::getGroups() - Getting all groups');
                $groups = $this->hemisApi->getAllGroups();
            }
            
            error_log('ApiController::getGroups() - Total groups before filtering: ' . count($groups));
            
            // Применяем фильтры
            $filteredGroups = $this->applyFilters($groups, [
                'search' => $search,
                'faculty_filter' => $facultyFilter,
                'specialty_filter' => $specialtyFilter,
            ]);
            
            error_log('ApiController::getGroups() - Total groups after filtering: ' . count($filteredGroups));
            
            // Применяем пагинацию
            $total = count($filteredGroups);
            $lastPage = (int)ceil($total / $perPage);
            $offset = ($page - 1) * $perPage;
            $paginatedGroups = array_slice($filteredGroups, $offset, $perPage);
            
            echo json_encode([
                'success' => true,
                'data' => $paginatedGroups,
                'meta' => [
                    'total' => $total,
                    'per_page' => $perPage,
                    'current_page' => $page,
                    'last_page' => $lastPage,
                    'from' => $total > 0 ? $offset + 1 : 0,
                    'to' => min($offset + $perPage, $total),
                ],
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            error_log('ApiController::getGroups() error: ' . $e->getMessage());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }
    }
    
    /**
     * Применить фильтры к списку групп
     * @param array<int, array<string, mixed>> $groups
     * @param array<string, string> $filters
     * @return array<int, array<string, mixed>>
     */
    private function applyFilters(array $groups, array $filters): array
    {
        $search = mb_strtolower(trim($filters['search'] ?? ''));
        $facultyFilter = mb_strtolower(trim($filters['faculty_filter'] ?? ''));
        $specialtyFilter = mb_strtolower(trim($filters['specialty_filter'] ?? ''));
        
        $filtered = [];
        
        foreach ($groups as $group) {
            $groupName = mb_strtolower($group['name'] ?? '');
            $groupFaculty = mb_strtolower($group['faculty'] ?? '');
            $groupSpecialty = mb_strtolower($group['specialty'] ?? '');
            
            // Поиск по названию группы, факультету, направлению
            if (!empty($search)) {
                $matchesSearch = 
                    strpos($groupName, $search) !== false ||
                    strpos($groupFaculty, $search) !== false ||
                    strpos($groupSpecialty, $search) !== false;
                if (!$matchesSearch) {
                    continue;
                }
            }
            
            // Фильтр по факультету
            if (!empty($facultyFilter)) {
                if (strpos($groupFaculty, $facultyFilter) === false) {
                    continue;
                }
            }
            
            // Фильтр по направлению
            if (!empty($specialtyFilter)) {
                if (strpos($groupSpecialty, $specialtyFilter) === false) {
                    continue;
                }
            }
            
            
            $filtered[] = $group;
        }
        
        return $filtered;
    }
    

    /**
     * Получить направления по факультету
     */
    public function getSpecialties(): void
    {
        $this->ensureAuth();

        header('Content-Type: application/json');
        
        $facultyId = $_GET['faculty_id'] ?? null;
        $facultyName = $_GET['faculty_name'] ?? null;

        try {
            if ($facultyId || $facultyName) {
                // Используем название факультета, если оно передано (более надежно)
                $faculty = $facultyName ?? $facultyId;
                
                // Логируем для отладки
                error_log('ApiController::getSpecialties() - faculty: ' . ($facultyName ?? $facultyId ?? 'unknown'));
                
                $specialties = $this->hemisApi->getSpecialtiesByFaculty($faculty);
                
                // Логируем результат
                error_log('ApiController::getSpecialties() - specialties count: ' . count($specialties));
                
                if (empty($specialties)) {
                    error_log('ApiController::getSpecialties() - No specialties found for faculty: ' . $faculty);
                }
            } else {
                error_log('ApiController::getSpecialties() - Missing faculty_id and faculty_name parameters');
                http_response_code(400);
                echo json_encode([
                    'success' => false,
                    'error' => 'Faculty ID or name is required',
                ], JSON_UNESCAPED_UNICODE);
                return;
            }
            
            echo json_encode([
                'success' => true,
                'data' => $specialties,
            ], JSON_UNESCAPED_UNICODE);
        } catch (\Throwable $e) {
            error_log('ApiController::getSpecialties() error: ' . $e->getMessage());
            error_log('ApiController::getSpecialties() stack trace: ' . $e->getTraceAsString());
            http_response_code(500);
            echo json_encode([
                'success' => false,
                'error' => $e->getMessage(),
            ], JSON_UNESCAPED_UNICODE);
        }
    }
}

