<?php
declare(strict_types=1);

namespace App;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

/**
 * OneID Resource Owner
 * 
 * Представляет данные пользователя, полученные от OneID
 */
class OneIdResourceOwner implements ResourceOwnerInterface
{
    private array $response;

    public function __construct(array $response)
    {
        $this->response = $response;
    }

    /**
     * Получить ID пользователя
     */
    public function getId(): ?string
    {
        return $this->response['id'] ?? $this->response['user_id'] ?? $this->response['pin'] ?? null;
    }

    /**
     * Получить полное имя пользователя
     */
    public function getFullName(): ?string
    {
        return $this->response['full_name'] ?? $this->response['name'] ?? null;
    }

    /**
     * Получить email
     */
    public function getEmail(): ?string
    {
        return $this->response['email'] ?? null;
    }

    /**
     * Получить телефон
     */
    public function getPhone(): ?string
    {
        return $this->response['phone'] ?? $this->response['phone_number'] ?? null;
    }

    /**
     * Получить PIN (личный идентификационный номер)
     */
    public function getPin(): ?string
    {
        return $this->response['pin'] ?? $this->response['id'] ?? null;
    }

    /**
     * Получить все данные пользователя
     */
    public function toArray(): array
    {
        return $this->response;
    }
}

