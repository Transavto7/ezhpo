<?php

namespace Src\Users\Management\Commands\UpdateUser;

use App\User;

final class UpdateUserCommand
{
    /**
     * @var User
     */
    private $user;

    /**
     * @var string
     */
    private $login;

    /**
     * @var string
     */
    private $email;

    /**
     * @var string|null
     */
    private $password;

    /**
     * @param User $user
     * @param string $login
     * @param string $email
     * @param string|null $password
     */
    public function __construct(
        User    $user,
        string  $login,
        string  $email,
        ?string $password
    ) {
        $this->user = $user;
        $this->login = $login;
        $this->email = $email;
        $this->password = $password;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getLogin(): string
    {
        return $this->login;
    }

    public function getEmail(): string
    {
        return $this->email;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }
}
