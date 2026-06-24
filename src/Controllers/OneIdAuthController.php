<?php
declare(strict_types=1);

namespace App\Controllers;

use App\OneIdProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;

/**
 * Контроллер для авторизации через OneID
 */
class OneIdAuthController
{
    private OneIdProvider $provider;

    public function __construct()
    {
        // Получаем настройки из .env
        $clientId = $_ENV['ONEID_CLIENT_ID'] ?? '';
        $clientSecret = $_ENV['ONEID_CLIENT_SECRET'] ?? '';
        $redirectUri = $_ENV['ONEID_REDIRECT_URI'] ?? $this->getDefaultRedirectUri();
        
        // Настройка проверки SSL
        $verifySsl = filter_var(
            $_ENV['ONEID_OAUTH_VERIFY_SSL'] ?? true,
            FILTER_VALIDATE_BOOLEAN,
            FILTER_NULL_ON_FAILURE
        );
        if ($verifySsl === null) {
            $verifySsl = true;
        }

        // Создаем провайдер OneID
        $this->provider = new OneIdProvider([
            'clientId'     => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri'  => $redirectUri,
            'verify'       => $verifySsl,
        ]);
    }

    /**
     * Получить дефолтный redirect URI
     */
    private function getDefaultRedirectUri(): string
    {
        $protocol = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        return $protocol . '://' . $host . '/oneid/callback';
    }

    /**
     * Редирект на страницу авторизации OneID
     */
    public function login(): void
    {
        // Если уже авторизован, редиректим на главную
        if (!empty($_SESSION['hemis_user'])) {
            header('Location: /');
            exit;
        }

        // Проверяем наличие настроек
        if (empty($_ENV['ONEID_CLIENT_ID']) || empty($_ENV['ONEID_CLIENT_SECRET'])) {
            $_SESSION['hemis_error'] = 'OneID sozlamalari to\'liq emas. Iltimos, .env faylini tekshiring.';
            header('Location: /hemis/login');
            exit;
        }

        try {
            // Генерируем state для защиты от CSRF
            $state = bin2hex(random_bytes(16));
            $_SESSION['oneid_state'] = $state;

            // Получаем URL авторизации
            $authorizationUrl = $this->provider->getAuthorizationUrl([
                'state' => $state,
            ]);

            // Сохраняем state в сессии
            $_SESSION['oneid_oauth2state'] = $this->provider->getState();

            // Редиректим на OneID
            header('Location: ' . $authorizationUrl);
            exit;
        } catch (\Throwable $e) {
            error_log('OneIdAuthController login error: ' . $e->getMessage());
            $_SESSION['hemis_error'] = 'OneID bilan bog\'lanishda xatolik yuz berdi.';
            header('Location: /hemis/login');
            exit;
        }
    }

    /**
     * Обработка callback от OneID
     */
    public function callback(): void
    {
        // Проверяем наличие кода авторизации
        if (empty($_GET['code'])) {
            $error = $_GET['error'] ?? 'Noma\'lum xatolik';
            $errorDescription = $_GET['error_description'] ?? '';
            
            $_SESSION['hemis_error'] = 'OneID autentifikatsiya xatosi: ' . $error;
            if (!empty($errorDescription)) {
                $_SESSION['hemis_error'] .= ' (' . $errorDescription . ')';
            }
            
            header('Location: /hemis/login');
            exit;
        }

        // Проверяем state для защиты от CSRF
        $state = $_GET['state'] ?? '';
        $savedState = $_SESSION['oneid_oauth2state'] ?? null;
        
        if (empty($state) || $state !== $savedState) {
            $_SESSION['hemis_error'] = 'Xavfsizlik xatosi: state mos kelmadi.';
            header('Location: /hemis/login');
            exit;
        }

        // Очищаем state из сессии
        unset($_SESSION['oneid_oauth2state'], $_SESSION['oneid_state']);

        try {
            // Получаем access token
            $accessToken = $this->provider->getAccessToken('authorization_code', [
                'code' => $_GET['code'],
            ]);

            // Получаем информацию о пользователе
            $resourceOwner = $this->provider->getResourceOwner($accessToken);
            $userData = $resourceOwner->toArray();

            // Сохраняем данные пользователя в сессию (в формате, совместимом с HEMIS)
            $_SESSION['hemis_user'] = [
                'id'              => $resourceOwner->getId(),
                'student_id'      => $resourceOwner->getPin(),
                'name'            => $resourceOwner->getFullName(),
                'login'           => $resourceOwner->getPin() ?? $resourceOwner->getId(),
                'email'           => $resourceOwner->getEmail(),
                'phone'           => $resourceOwner->getPhone(),
                'pin'             => $resourceOwner->getPin(),
                'oneid_data'      => $userData, // Сохраняем полные данные от OneID
            ];

            // Сохраняем токен (если нужен для дальнейших запросов)
            $_SESSION['oneid_token'] = $accessToken->getToken();

            // Редиректим на главную
            header('Location: /');
            exit;
        } catch (IdentityProviderException $e) {
            error_log('OneID IdentityProviderException: ' . $e->getMessage());
            $_SESSION['hemis_error'] = 'OneID autentifikatsiya xatosi: ' . $e->getMessage();
            header('Location: /hemis/login');
            exit;
        } catch (\Throwable $e) {
            error_log('OneIdAuthController callback error: ' . $e->getMessage());
            error_log('Stack trace: ' . $e->getTraceAsString());
            
            $errorMessage = 'OneID bilan bog\'lanishda xatolik yuz berdi.';
            if (($_ENV['APP_ENV'] ?? 'production') === 'development') {
                $errorMessage .= ' ' . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8');
            }
            
            $_SESSION['hemis_error'] = $errorMessage;
            header('Location: /hemis/login');
            exit;
        }
    }

    /**
     * Выход из системы
     */
    public function logout(): void
    {
        unset($_SESSION['hemis_user'], $_SESSION['oneid_token']);
        header('Location: /');
        exit;
    }
}

