<?php

namespace Src\Users\Management\Commands\CreateUser;

use App\Enums\UserEntityType;

final class CreateUserCommand
{
    /**
     * @var UserEntityType
     */
    private $entityType;
    /**
     * @var string
     */
    private $login;
    /**
     * @var string
     */
    private $email;
    /**
     * @var string
     */
    private $password;
    /**
     * @var string|null
     */
    private $apiToken;
    /**
     * @var int|null
     */
    private $role;

    /**
     * @param UserEntityType $entityType
     * @param string $login
     * @param string $email
     * @param string $password
     * @param string|null $apiToken
     * @param int|null $role
     */
    public function __construct(
        UserEntityType $entityType,
        string         $login,
        string         $email,
        string         $password,
        ?string        $apiToken,
        ?int           $role = null
    )
    {
        $this->entityType = $entityType;
        $this->login = $login;
        $this->email = $email;
        $this->password = $password;
        $this->apiToken = $apiToken;
        $this->role = $role;
    }

    public function getEntityType(): UserEntityType
    {
        return $this->entityType;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): string
    {
        return $this->password;
    }

    public function getApiToken(): ?string
    {
        return $this->apiToken;
    }

    public function getRole(): ?int
    {
        return $this->role;
    }
}