<?php
declare(strict_types=1);

namespace App\Services;

/**
 * Сервис для управления преподавателями
 */
final class TeacherService
{
    private string $file;

    public function __construct()
    {
        $root = dirname(__DIR__, 2);
        $storageDir = $root . '/storage';
        if (!is_dir($storageDir)) {
            mkdir($storageDir, 0755, true);
        }

        $this->file = $storageDir . '/teachers.json';
        
        if (!file_exists($this->file)) {
            file_put_contents($this->file, json_encode([], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));
        }
    }

    /**
     * Регистрация нового преподавателя
     * @param array<string, mixed> $data
     * @return bool
     * @throws \Exception
     */
    public function register(array $data): bool
    {
        $teachers = $this->getAll();
        
        // Проверка уникальности логина
        foreach ($teachers as $teacher) {
            if (strtolower($teacher['login']) === strtolower($data['login'])) {
                throw new \Exception('Bunday login allaqachon mavjud');
            }
        }
        
        // Проверка уникальности email (если указан)
        if (!empty($data['email'])) {
            foreach ($teachers as $teacher) {
                if (!empty($teacher['email']) && strtolower($teacher['email']) === strtolower($data['email'])) {
                    throw new \Exception('Bunday email allaqachon mavjud');
                }
            }
        }
        
        $newTeacher = [
            'id' => $this->getNextId(),
            'login' => trim($data['login']),
            'password_hash' => password_hash($data['password'], PASSWORD_DEFAULT),
            'full_name' => trim($data['full_name']),
            'email' => !empty($data['email']) ? trim($data['email']) : null,
            'phone' => !empty($data['phone']) ? trim($data['phone']) : null,
            'department' => !empty($data['department']) ? trim($data['department']) : null,
            'registered_at' => date('Y-m-d H:i:s'),
            'last_login' => null,
        ];
        
        $teachers[] = $newTeacher;
        $this->saveAll($teachers);
        
        error_log('TeacherService::register() - New teacher registered: ' . $data['login']);
        
        return true;
    }

    /**
     * Авторизация преподавателя
     * @param string $login
     * @param string $password
     * @return array<string, mixed>|null
     */
    public function authenticate(string $login, string $password): ?array
    {
        $teacher = $this->findByLogin($login);
        
        if (!$teacher) {
            return null;
        }
        
        if (!password_verify($password, $teacher['password_hash'])) {
            return null;
        }
        
        // Обновляем последний вход
        $this->updateLastLogin($teacher['id']);
        
        // Удаляем хэш пароля перед возвратом
        unset($teacher['password_hash']);
        
        error_log('TeacherService::authenticate() - Teacher logged in: ' . $login);
        
        return $teacher;
    }

    /**
     * Обновление данных преподавателя
     * @param int $teacherId
     * @param array<string, mixed> $data
     */
    public function update(int $teacherId, array $data): bool
    {
        $teachers = $this->getAll();
        
        foreach ($teachers as &$teacher) {
            if ($teacher['id'] === $teacherId) {
                // Обновляем только разрешенные поля
                if (isset($data['full_name'])) $teacher['full_name'] = trim($data['full_name']);
                if (isset($data['email'])) $teacher['email'] = !empty($data['email']) ? trim($data['email']) : null;
                if (isset($data['phone'])) $teacher['phone'] = !empty($data['phone']) ? trim($data['phone']) : null;
                if (isset($data['department'])) $teacher['department'] = !empty($data['department']) ? trim($data['department']) : null;
                
                // Обновление пароля (если указан)
                if (!empty($data['password'])) {
                    $teacher['password_hash'] = password_hash($data['password'], PASSWORD_DEFAULT);
                }
                
                $this->saveAll($teachers);
                error_log('TeacherService::update() - Teacher updated: ID=' . $teacherId);
                return true;
            }
        }
        
        return false;
    }

    /**
     * Создать/обновить преподавателя по данным HEMIS OAuth.
     * @param array<string, mixed> $oauthUser
     * @return array<string, mixed>
     */
    public function upsertFromHemisOauth(array $oauthUser): array
    {
        $hemisId = (string)($oauthUser['id'] ?? '');
        if ($hemisId === '') {
            throw new \RuntimeException('HEMIS user id topilmadi.');
        }

        $teachers = $this->getAll();
        $existingIndex = $this->findIndexByHemisId($teachers, $hemisId);

        $fullName = trim((string)($oauthUser['name'] ?? ''));
        $login = trim((string)($oauthUser['login'] ?? ''));
        $email = trim((string)($oauthUser['email'] ?? ''));
        $phone = trim((string)($oauthUser['phone'] ?? ''));
        $picture = trim((string)($oauthUser['picture'] ?? ''));
        $department = trim((string)($oauthUser['department'] ?? ''));

        if ($existingIndex === null) {
            $newTeacher = [
                'id' => $this->getNextId(),
                'login' => $login !== '' ? $login : ('hemis_' . $hemisId),
                'password_hash' => password_hash(bin2hex(random_bytes(16)), PASSWORD_DEFAULT),
                'full_name' => $fullName !== '' ? $fullName : 'Noma\'lum o\'qituvchi',
                'email' => $email !== '' ? $email : null,
                'phone' => $phone !== '' ? $phone : null,
                'department' => $department !== '' ? $department : null,
                'registered_at' => date('Y-m-d H:i:s'),
                'last_login' => date('Y-m-d H:i:s'),
                'hemis_id' => $hemisId,
                'hemis_uuid' => (string)($oauthUser['uuid'] ?? ''),
                'hemis_type' => (string)($oauthUser['type'] ?? ''),
                'hemis_login' => $login !== '' ? $login : null,
                'picture' => $picture !== '' ? $picture : null,
            ];

            $teachers[] = $newTeacher;
            $this->saveAll($teachers);

            unset($newTeacher['password_hash']);
            return $newTeacher;
        }

        $teacher = $teachers[$existingIndex];

        if ($login !== '' && (string)($teacher['login'] ?? '') !== $login) {
            $teacher['login'] = $login;
        }

        if ($fullName !== '') {
            $teacher['full_name'] = $fullName;
        }

        if ($email !== '') {
            $teacher['email'] = $email;
        }

        if ($phone !== '') {
            $teacher['phone'] = $phone;
        }

        if ($department !== '') {
            $teacher['department'] = $department;
        }

        if ($picture !== '') {
            $teacher['picture'] = $picture;
        }

        $teacher['hemis_id'] = $hemisId;
        $teacher['hemis_uuid'] = (string)($oauthUser['uuid'] ?? ($teacher['hemis_uuid'] ?? ''));
        $teacher['hemis_type'] = (string)($oauthUser['type'] ?? ($teacher['hemis_type'] ?? ''));
        $teacher['hemis_login'] = $login !== '' ? $login : ($teacher['hemis_login'] ?? null);
        $teacher['last_login'] = date('Y-m-d H:i:s');

        $teachers[$existingIndex] = $teacher;
        $this->saveAll($teachers);

        unset($teacher['password_hash']);
        return $teacher;
    }

    /**
     * Удаление преподавателя
     */
    public function delete(int $teacherId): bool
    {
        $teachers = $this->getAll();
        $filtered = [];
        $deleted = false;
        
        foreach ($teachers as $teacher) {
            if ($teacher['id'] !== $teacherId) {
                $filtered[] = $teacher;
            } else {
                $deleted = true;
            }
        }
        
        if ($deleted) {
            $this->saveAll($filtered);
            error_log('TeacherService::delete() - Teacher deleted: ID=' . $teacherId);
        }
        
        return $deleted;
    }

    /**
     * Обновление времени последнего входа
     */
    private function updateLastLogin(int $teacherId): void
    {
        $teachers = $this->getAll();
        
        foreach ($teachers as &$teacher) {
            if ($teacher['id'] === $teacherId) {
                $teacher['last_login'] = date('Y-m-d H:i:s');
                $this->saveAll($teachers);
                break;
            }
        }
    }

    /**
     * Получить всех преподавателей
     * @return array<int, array<string, mixed>>
     */
    public function getAll(): array
    {
        $json = file_get_contents($this->file);
        if ($json === false || $json === '') {
            return [];
        }
        $data = json_decode($json, true);
        return is_array($data) ? $data : [];
    }

    /**
     * Найти преподавателя по ID
     * @return array<string, mixed>|null
     */
    public function findById(int $teacherId): ?array
    {
        foreach ($this->getAll() as $teacher) {
            if ($teacher['id'] === $teacherId) {
                return $teacher;
            }
        }
        return null;
    }

    /**
     * Найти преподавателя по логину
     * @return array<string, mixed>|null
     */
    public function findByLogin(string $login): ?array
    {
        foreach ($this->getAll() as $teacher) {
            if (strtolower($teacher['login']) === strtolower($login)) {
                return $teacher;
            }
        }
        return null;
    }

    /**
     * @param array<int, array<string, mixed>> $teachers
     */
    private function findIndexByHemisId(array $teachers, string $hemisId): ?int
    {
        foreach ($teachers as $idx => $teacher) {
            if ((string)($teacher['hemis_id'] ?? '') === $hemisId) {
                return $idx;
            }
        }
        return null;
    }

    /**
     * Получить следующий ID
     */
    private function getNextId(): int
    {
        $teachers = $this->getAll();
        if (empty($teachers)) {
            return 1;
        }
        
        $maxId = 0;
        foreach ($teachers as $teacher) {
            if ($teacher['id'] > $maxId) {
                $maxId = $teacher['id'];
            }
        }
        
        return $maxId + 1;
    }

    /**
     * Сохранить всех преподавателей
     * @param array<int, array<string, mixed>> $teachers
     */
    private function saveAll(array $teachers): void
    {
        file_put_contents(
            $this->file,
            json_encode(array_values($teachers), JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT)
        );
    }
}

