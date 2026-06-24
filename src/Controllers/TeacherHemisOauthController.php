<?php
declare(strict_types=1);

namespace App\Controllers;

use App\Services\TeacherService;
use GuzzleHttp\Client as GuzzleClient;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Provider\GenericProvider;

class TeacherHemisOauthController
{
    private TeacherService $teacherService;

    public function __construct()
    {
        $this->teacherService = new TeacherService();
    }

    private function buildRedirectUri(): string
    {
        if (!empty($_ENV['HEMIS_TEACHER_OAUTH_REDIRECT_URI'])) {
            return (string)$_ENV['HEMIS_TEACHER_OAUTH_REDIRECT_URI'];
        }

        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost:8000';
        return $scheme . '://' . $host . '/hemis/callback/';
    }

    private function provider(): GenericProvider
    {
        $clientId = (string)($_ENV['HEMIS_TEACHER_OAUTH_CLIENT_ID'] ?? ($_ENV['HEMIS_OAUTH_CLIENT_ID'] ?? ''));
        $clientSecret = (string)($_ENV['HEMIS_TEACHER_OAUTH_CLIENT_SECRET'] ?? ($_ENV['HEMIS_OAUTH_CLIENT_SECRET'] ?? ''));

        if ($clientId === '' || $clientSecret === '') {
            throw new \RuntimeException('HEMIS OAuth sozlamalari topilmadi (HEMIS_TEACHER_OAUTH_CLIENT_ID/SECRET).');
        }

        $base = (string)($_ENV['HEMIS_TEACHER_OAUTH_BASE_URL'] ?? '');
        if ($base === '') {
            $authorizeUrl = (string)($_ENV['HEMIS_OAUTH_AUTHORIZE_URL'] ?? '');
            if ($authorizeUrl !== '') {
                $parts = parse_url($authorizeUrl);
                $scheme = $parts['scheme'] ?? 'https';
                $host = $parts['host'] ?? 'univer.hemis.uz';
                $port = isset($parts['port']) ? ':' . $parts['port'] : '';
                $base = $scheme . '://' . $host . $port;
            }
        }
        $base = rtrim($base !== '' ? $base : 'https://univer.hemis.uz', '/');

        // HTTP client with disabled SSL verification (for self-signed certificates)
        $httpClient = new GuzzleClient([
            'verify' => false,
        ]);

        return new GenericProvider([
            'clientId' => $clientId,
            'clientSecret' => $clientSecret,
            'redirectUri' => $this->buildRedirectUri(),
            'urlAuthorize' => $base . '/oauth/authorize',
            'urlAccessToken' => $base . '/oauth/access-token',
            'urlResourceOwnerDetails' => $base . '/oauth/api/user?fields=id,uuid,type,name,login,picture,email,university_id,phone',
        ], [
            'httpClient' => $httpClient,
        ]);
    }

    public function login(): void
    {
        if (!empty($_SESSION['teacher_user'])) {
            header('Location: /teacher/dashboard');
            exit;
        }

        try {
            $provider = $this->provider();
            $authorizationUrl = $provider->getAuthorizationUrl();
            $_SESSION['teacher_oauth2state'] = $provider->getState();
            header('Location: ' . $authorizationUrl);
            exit;
        } catch (\Throwable $e) {
            $_SESSION['teacher_error'] = $e->getMessage();
            header('Location: /teachers/login');
            exit;
        }
    }

    public function callback(): void
    {
        if (!empty($_SESSION['teacher_user'])) {
            header('Location: /teacher/dashboard');
            exit;
        }

        $code = (string)($_GET['code'] ?? '');
        $state = (string)($_GET['state'] ?? '');

        if ($code === '') {
            $_SESSION['teacher_error'] = 'HEMIS avtorizatsiya kodi topilmadi.';
            header('Location: /teachers/login');
            exit;
        }

        $expectedState = (string)($_SESSION['teacher_oauth2state'] ?? '');
        unset($_SESSION['teacher_oauth2state']);

        if ($expectedState === '' || $state === '' || $state !== $expectedState) {
            $_SESSION['teacher_error'] = 'Invalid state.';
            header('Location: /teachers/login');
            exit;
        }

        try {
            $provider = $this->provider();
            $accessToken = $provider->getAccessToken('authorization_code', ['code' => $code]);
            $resourceOwner = $provider->getResourceOwner($accessToken);
            $data = $resourceOwner->toArray();

            $teacher = $this->teacherService->upsertFromHemisOauth($data);
            $_SESSION['teacher_user'] = $teacher;

            header('Location: /teacher/dashboard');
            exit;
        } catch (IdentityProviderException $e) {
            $_SESSION['teacher_error'] = $e->getMessage();
            header('Location: /teachers/login');
            exit;
        } catch (\Throwable $e) {
            $_SESSION['teacher_error'] = $e->getMessage();
            header('Location: /teachers/login');
            exit;
        }
    }
}
