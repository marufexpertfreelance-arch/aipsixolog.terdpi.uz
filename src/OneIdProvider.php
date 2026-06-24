<?php
declare(strict_types=1);

namespace App;

use League\OAuth2\Client\Provider\GenericProvider;
use League\OAuth2\Client\Provider\Exception\IdentityProviderException;
use League\OAuth2\Client\Token\AccessToken;
use Psr\Http\Message\ResponseInterface;

/**
 * OneID OAuth2 Provider
 * 
 * Провайдер для авторизации через систему OneID Узбекистана
 * Использует GenericProvider как основу
 */
class OneIdProvider extends GenericProvider
{
    /**
     * Конструктор с настройками OneID
     */
    public function __construct(array $options = [], array $collaborators = [])
    {
        // Получаем URL из .env или используем дефолтные
        $authorizeUrl = $_ENV['ONEID_AUTHORIZE_URL'] ?? 'https://sso.egov.uz:8443/sso/oauth/Authorization.do';
        $tokenUrl = $_ENV['ONEID_TOKEN_URL'] ?? 'https://sso.egov.uz:8443/sso/oauth/Authorization.do';
        $userInfoUrl = $_ENV['ONEID_USERINFO_URL'] ?? 'https://sso.egov.uz:8443/sso/oauth/Authorization.do';

        // Настройка проверки SSL
        $verifySsl = $options['verify'] ?? true;
        unset($options['verify']);

        // Настройка HTTP клиента с проверкой SSL
        if (isset($collaborators['httpClient'])) {
            $httpClient = $collaborators['httpClient'];
        } else {
            $httpClient = new \GuzzleHttp\Client([
                'verify' => $verifySsl,
            ]);
            $collaborators['httpClient'] = $httpClient;
        }

        // Вызываем конструктор родителя с правильными параметрами
        parent::__construct([
            'clientId'                => $options['clientId'] ?? '',
            'clientSecret'            => $options['clientSecret'] ?? '',
            'redirectUri'             => $options['redirectUri'] ?? '',
            'urlAuthorize'            => $authorizeUrl,
            'urlAccessToken'          => $tokenUrl,
            'urlResourceOwnerDetails' => $userInfoUrl,
            'scopes'                  => ['openid', 'profile', 'email'],
        ], $collaborators);
    }

    /**
     * Создание объекта ResourceOwner из ответа API
     */
    protected function createResourceOwner(array $response, AccessToken $token): OneIdResourceOwner
    {
        return new OneIdResourceOwner($response);
    }
}

