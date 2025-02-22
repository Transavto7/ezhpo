<?php

namespace Src\Users\Management\Commands\RestoreUser;

use App\User;

final class RestoreUserCommand
{
    /**
     * @var User
     */
    private $user;

    /**
     * @param User $user
     */
    public function __construct(User $user)
    {
        $this->user = $user;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}