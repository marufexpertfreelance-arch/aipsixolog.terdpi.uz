<?php
declare(strict_types=1);

namespace App\Controllers;

use GuzzleHttp\Client as HttpClient;
use App\View;
use App\Services\StudentStorage;

class HemisAuthController
{
    private HttpClient $httpClient;
    private string $baseUrl;

    public function __construct()
    {
        // Базовый URL для Student API (без /rest префикса, путь добавляется в запросе)
        $this->baseUrl = $_ENV['HEMIS_API_BASE_URL'] ?? 'https://student.terdpi.uz';
        $this->baseUrl = rtrim($this->baseUrl, '/');
        
        // Настройка проверки SSL сертификата
        $verifySsl = filter_var(
            $_ENV['HEMIS_OAUTH_VERIFY_SSL'] ?? false,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
        if ($verifySsl === null) {
            $verifySsl = false; // По умолчанию отключаем SSL проверку для локальной разработки
        }
        
        // Создаем HTTP клиент для работы с Student API
        $this->httpClient = new HttpClient([
            'base_uri' => $this->baseUrl,
            'verify'   => $verifySsl,
            'timeout'  => 30.0, // Увеличиваем таймаут до 30 секунд
            'connect_timeout' => 10.0, // Таймаут подключения 10 секунд
            'headers'  => [
                'Accept'       => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);
    }

    /** Показываем форму входа */
    public function login(): void
    {
        // Если уже авторизован, редиректим на главную
        if (!empty($_SESSION['hemis_user'])) {
            header('Location: /');
            exit;
        }

        $error = $_SESSION['hemis_error'] ?? null;
        unset($_SESSION['hemis_error']);

        echo View::render('hemis/login', [
            'title' => 'HEMIS orqali kirish',
            'error' => $error,
        ]);
    }

    /** Обрабатываем вход через Student API */
    public function loginPost(): void
    {
        $login = trim($_POST['login'] ?? '');
        $password = trim($_POST['password'] ?? '');

        if (empty($login) || empty($password)) {
            $_SESSION['hemis_error'] = 'Login va parolni kiriting.';
            header('Location: /hemis/login');
            exit;
        }

        try {
            // Отправляем запрос на /v1/auth/login
            // URL будет: https://student.terdpi.uz/rest/v1/auth/login
            $requestData = [
                'login'    => $login,
                'password' => $password,
            ];
            
            // Логируем запрос для отладки
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                error_log("HEMIS API Request: POST {$this->baseUrl}/rest/v1/auth/login");
                error_log("HEMIS API Request Data: " . json_encode($requestData));
            }
            
            $response = $this->httpClient->post('/rest/v1/auth/login', [
                'json' => $requestData,
                'http_errors' => false, // Не бросать исключения на HTTP ошибки
            ]);

            $statusCode = $response->getStatusCode();
            $responseBody = (string) $response->getBody();
            $responseData = json_decode($responseBody, true);
            
            // Логируем ответ для отладки
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                error_log("HEMIS API Response: Status={$statusCode}");
                error_log("HEMIS API Response Body: " . substr($responseBody, 0, 1000));
                if ($responseData) {
                    error_log("HEMIS API Response Data: " . json_encode($responseData));
                }
            }

            // Проверяем успешный ответ (может быть 200 или другой успешный код)
            if (($statusCode >= 200 && $statusCode < 300) && isset($responseData['success']) && $responseData['success'] && isset($responseData['data']['token'])) {
                $token = $responseData['data']['token'];

                // Сохраняем токен в сессию
                $_SESSION['hemis_token'] = $token;

                // Получаем информацию о пользователе через /v1/account/me
                try {
                    $userResponse = $this->httpClient->get('/rest/v1/account/me', [
                        'headers' => [
                            'Authorization' => 'Bearer ' . $token,
                        ],
                        'http_errors' => false,
                    ]);

                    $userData = json_decode((string) $userResponse->getBody(), true);
                    
                    // Логируем ответ для отладки
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log("HEMIS API /account/me Response: Status=" . $userResponse->getStatusCode());
                        error_log("HEMIS API /account/me Response Body: " . substr((string) $userResponse->getBody(), 0, 2000));
                        if ($userData) {
                            error_log("HEMIS API /account/me Response Data: " . json_encode($userData, JSON_UNESCAPED_UNICODE));
                        }
                    }

                    $userStatusCode = $userResponse->getStatusCode();
                    
                    // Проверяем разные возможные форматы ответа
                    if ($userStatusCode === 200) {
                        // Формат 1: { "data": {...} }
                        if (isset($userData['data']) && is_array($userData['data'])) {
                            $user = $userData['data'];
                        } 
                        // Формат 2: данные напрямую в корне
                        elseif (is_array($userData) && isset($userData['id'])) {
                            $user = $userData;
                        } 
                        // Если формат не распознан - логируем и используем пустые данные
                        else {
                            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                                error_log("HemisAuthController: Unknown response format from /account/me: " . json_encode($userData, JSON_UNESCAPED_UNICODE));
                            }
                            $user = [];
                        }
                    } else {
                        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                            error_log("HemisAuthController: /account/me returned status code {$userStatusCode} instead of 200");
                            error_log("Response body: " . substr((string) $userResponse->getBody(), 0, 500));
                        }
                        $user = [];
                    }
                    
                    // Если данные пользователя получены - обрабатываем их
                    if (!empty($user)) {
                        
                        // Обрабатываем группу - может быть объектом или строкой
                        $groupName = null;
                        if (isset($user['group'])) {
                            if (is_array($user['group']) && isset($user['group']['name'])) {
                                $groupName = $user['group']['name'];
                            } elseif (is_string($user['group'])) {
                                $groupName = $user['group'];
                            }
                        }
                        
                        // Обрабатываем факультет - может быть объектом или строкой
                        $facultyName = null;
                        if (isset($user['faculty'])) {
                            if (is_array($user['faculty']) && isset($user['faculty']['name'])) {
                                $facultyName = $user['faculty']['name'];
                            } elseif (is_string($user['faculty'])) {
                                $facultyName = $user['faculty'];
                            }
                        }
                        
                        // Сохраняем данные пользователя в сессию (используем структуру из реального API)
                        $userSessionData = [
                            'id'              => $user['id'] ?? null,
                            'student_id'      => $user['student_id_number'] ?? null,
                            'name'            => $user['full_name'] ?? $user['short_name'] ?? null,
                            'first_name'      => $user['first_name'] ?? null,
                            'second_name'     => $user['second_name'] ?? null,
                            'third_name'      => $user['third_name'] ?? null,
                            'short_name'      => $user['short_name'] ?? null,
                            'login'           => $login,
                            'email'           => $user['email'] ?? null,
                            'phone'           => $user['phone'] ?? null,
                            'university'      => $user['university'] ?? null,
                            'specialty'       => is_array($user['specialty'] ?? null) ? ($user['specialty']['name'] ?? null) : ($user['specialty'] ?? null),
                            'group'           => $groupName,
                            'faculty'         => $facultyName,
                            'semester'        => is_array($user['semester'] ?? null) ? ($user['semester']['name'] ?? null) : ($user['semester'] ?? null),
                            'image'           => $user['image'] ?? null,
                            'avg_gpa'         => $user['avg_gpa'] ?? null,
                        ];
                        
                        // Логируем для отладки только в режиме разработки
                        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                            error_log("HemisAuthController: Saving user data:");
                            error_log("  - Student ID: " . var_export($userSessionData['student_id'] ?? $userSessionData['id'] ?? 'null', true));
                            error_log("  - Group: " . var_export($groupName, true));
                            error_log("  - Specialty: " . var_export($userSessionData['specialty'] ?? 'null', true));
                            error_log("  - Faculty: " . var_export($facultyName, true));
                            error_log("  - Full user data: " . json_encode($userSessionData, JSON_UNESCAPED_UNICODE));
                        }
                        
                        $_SESSION['hemis_user'] = $userSessionData;
                        
                        // Сохраняем студента в хранилище (это автоматически распределит данные по факультетам/направлениям/группам)
                        $studentStorage = new StudentStorage();
                        $studentStorage->saveStudent($userSessionData);
                        
                        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                            error_log("HemisAuthController: Student saved to storage successfully");
                        }
                    } else {
                        // Если данные пользователя не получены - логируем только в режиме разработки
                        if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                            error_log("HemisAuthController: User data is empty or not received");
                        }
                    }
                } catch (\Throwable $e) {
                    // Если не удалось получить данные пользователя, но токен есть - продолжаем
                    error_log('Failed to get user data: ' . $e->getMessage());
                    $_SESSION['hemis_user'] = [
                        'login' => $login,
                        'name'  => 'Foydalanuvchi',
                    ];
                }

                // Редиректим на главную
                header('Location: /');
                exit;
            } else {
                // Ошибка авторизации
                $errorMessage = 'Autentifikatsiya xatosi.';
                
                if ($responseData) {
                    if (isset($responseData['error'])) {
                        $errorMessage = is_string($responseData['error']) 
                            ? $responseData['error'] 
                            : 'Noto\'g\'ri login yoki parol.';
                    } elseif (isset($responseData['message'])) {
                        $errorMessage = $responseData['message'];
                    }
                }
                
                // В режиме разработки показываем больше информации
                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                    $_SESSION['hemis_error'] = $errorMessage . ' (Status: ' . $statusCode . ')';
                    $_SESSION['hemis_debug'] = [
                        'status_code' => $statusCode,
                        'response' => $responseData,
                        'url' => $this->baseUrl . '/rest/v1/auth/login',
                        'request_data' => ['login' => $login, 'password' => '***'],
                    ];
                } else {
                    $_SESSION['hemis_error'] = $errorMessage;
                }
                
                header('Location: /hemis/login');
                exit;
            }
        } catch (\GuzzleHttp\Exception\ConnectException $e) {
            // Ошибка подключения (таймаут, DNS, и т.д.)
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                error_log('HemisAuthController connection error: ' . $e->getMessage());
            }
            
            // Пробуем использовать локальные данные, если HEMIS недоступен
            $studentStorage = new StudentStorage();
            $allStudents = $studentStorage->getAll();
            
            // Ищем студента по логину в локальном хранилище
            $localStudent = null;
            foreach ($allStudents as $student) {
                $studentLogin = $student['login'] ?? $student['student_id'] ?? $student['id'] ?? null;
                if ($studentLogin && (string)$studentLogin === (string)$login) {
                    $localStudent = $student;
                    break;
                }
            }
            
            if ($localStudent) {
                // Нашли студента в локальном хранилище - используем его данные
                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log('HemisAuthController: Using local student data (HEMIS unavailable)');
                    }
                }
                
                $_SESSION['hemis_user'] = [
                    'login' => $localStudent['login'] ?? $localStudent['student_id'] ?? $localStudent['id'] ?? $login,
                    'student_id' => $localStudent['student_id'] ?? $localStudent['id'] ?? $login,
                    'name' => $localStudent['name'] ?? $localStudent['full_name'] ?? 'Foydalanuvchi',
                    'group' => $localStudent['group'] ?? null,
                    'faculty' => $localStudent['faculty'] ?? null,
                    'specialty' => $localStudent['specialty'] ?? null,
                    'semester' => $localStudent['semester'] ?? null,
                    'image' => $localStudent['image'] ?? null,
                ];
                
                // Редиректим на главную
                header('Location: /');
                exit;
            }
            
            // Если студента нет в локальном хранилище - показываем ошибку
            $errorMessage = 'HEMIS serveriga bog\'lanishda xatolik. ';
            $errorMsg = $e->getMessage();
            
            if (strpos($errorMsg, 'timed out') !== false || strpos($errorMsg, 'Connection timed out') !== false) {
                $errorMessage .= 'Server javob bermayapti. Iltimos, keyinroq urinib ko\'ring yoki tarmoq ulanishini tekshiring.';
            } elseif (strpos($errorMsg, 'Could not resolve host') !== false) {
                $errorMessage .= 'Server topilmadi. Tarmoq ulanishini tekshiring.';
            } else {
                $errorMessage .= 'Tarmoq xatosi.';
            }
            
            $errorMessage .= ' Agar siz avval tizimga kirgan bo\'lsangiz, ma\'lumotlaringiz saqlangan bo\'lishi mumkin.';
            
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                $errorMessage .= ' (Detal: ' . htmlspecialchars(substr($errorMsg, 0, 100), ENT_QUOTES, 'UTF-8') . ')';
            }
            
            $_SESSION['hemis_error'] = $errorMessage;
            header('Location: /hemis/login');
            exit;
        } catch (\GuzzleHttp\Exception\RequestException $e) {
            // Ошибка сети или запроса
            $response = $e->getResponse();
            $errorMessage = 'Server bilan bog\'lanishda xatolik.';
            
            if ($response) {
                $statusCode = $response->getStatusCode();
                $errorBody = (string) $response->getBody();
                $errorData = json_decode($errorBody, true);
                
                if ($errorData && isset($errorData['error'])) {
                    $errorMessage = $errorData['error'];
                } else {
                    $errorMessage = "HTTP {$statusCode}: " . substr($errorBody, 0, 200);
                }
                
                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                    error_log("HEMIS API Error: Status={$statusCode}, Body={$errorBody}");
                }
            } else {
                if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                    error_log('HemisAuthController network error: ' . $e->getMessage());
                }
                $errorMessage = 'Tarmoq xatosi: ' . $e->getMessage();
            }
            
            $_SESSION['hemis_error'] = $errorMessage;
            header('Location: /hemis/login');
            exit;
        } catch (\Throwable $e) {
            // Общая ошибка
            error_log('HemisAuthController login error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'Xatolik yuz berdi.';
            $errorMsg = $e->getMessage();
            
            // Проверяем, является ли это ошибкой подключения
            if (strpos($errorMsg, 'cURL error 28') !== false || strpos($errorMsg, 'Connection timed out') !== false) {
                // Пробуем использовать локальные данные, если HEMIS недоступен
                $studentStorage = new StudentStorage();
                $allStudents = $studentStorage->getAll();
                
                $localStudent = null;
                foreach ($allStudents as $student) {
                    $studentLogin = $student['login'] ?? $student['student_id'] ?? $student['id'] ?? null;
                    if ($studentLogin && (string)$studentLogin === (string)$login) {
                        $localStudent = $student;
                        break;
                    }
                }
                
                if ($localStudent) {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log('HemisAuthController: Using local student data (HEMIS unavailable)');
                    }
                    
                    $_SESSION['hemis_user'] = [
                        'login' => $localStudent['login'] ?? $localStudent['student_id'] ?? $localStudent['id'] ?? $login,
                        'student_id' => $localStudent['student_id'] ?? $localStudent['id'] ?? $login,
                        'name' => $localStudent['name'] ?? $localStudent['full_name'] ?? 'Foydalanuvchi',
                        'group' => $localStudent['group'] ?? null,
                        'faculty' => $localStudent['faculty'] ?? null,
                        'specialty' => $localStudent['specialty'] ?? null,
                        'semester' => $localStudent['semester'] ?? null,
                        'image' => $localStudent['image'] ?? null,
                    ];
                    
                    header('Location: /');
                    exit;
                }
                
                $errorMessage = 'HEMIS serveriga bog\'lanishda xatolik. Server javob bermayapti. Iltimos, keyinroq urinib ko\'ring.';
            } elseif (strpos($errorMsg, 'cURL error 6') !== false || strpos($errorMsg, 'Could not resolve host') !== false) {
                // Пробуем использовать локальные данные
                $studentStorage = new StudentStorage();
                $allStudents = $studentStorage->getAll();
                
                $localStudent = null;
                foreach ($allStudents as $student) {
                    $studentLogin = $student['login'] ?? $student['student_id'] ?? $student['id'] ?? null;
                    if ($studentLogin && (string)$studentLogin === (string)$login) {
                        $localStudent = $student;
                        break;
                    }
                }
                
                if ($localStudent) {
                    if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                        error_log('HemisAuthController: Using local student data (HEMIS unavailable)');
                    }
                    
                    $_SESSION['hemis_user'] = [
                        'login' => $localStudent['login'] ?? $localStudent['student_id'] ?? $localStudent['id'] ?? $login,
                        'student_id' => $localStudent['student_id'] ?? $localStudent['id'] ?? $login,
                        'name' => $localStudent['name'] ?? $localStudent['full_name'] ?? 'Foydalanuvchi',
                        'group' => $localStudent['group'] ?? null,
                        'faculty' => $localStudent['faculty'] ?? null,
                        'specialty' => $localStudent['specialty'] ?? null,
                        'semester' => $localStudent['semester'] ?? null,
                        'image' => $localStudent['image'] ?? null,
                    ];
                    
                    header('Location: /');
                    exit;
                }
                
                $errorMessage = 'HEMIS serveriga bog\'lanishda xatolik. Server topilmadi. Tarmoq ulanishini tekshiring.';
            }
            
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                $errorMessage .= ' ' . htmlspecialchars($errorMsg, ENT_QUOTES, 'UTF-8');
            }
            
            $_SESSION['hemis_error'] = $errorMessage;
            header('Location: /hemis/login');
            exit;
        }
    }

    /** Выход из системы */
    public function logout(): void
    {
        unset($_SESSION['hemis_user'], $_SESSION['hemis_token']);
        header('Location: /');
        exit;
    }
}
