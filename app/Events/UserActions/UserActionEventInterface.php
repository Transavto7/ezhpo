<?php

namespace App\Events\UserActions;

use App\User;

interface UserActionEventInterface
{
    public function getUser(): User;

    public function getType(): string;
}
