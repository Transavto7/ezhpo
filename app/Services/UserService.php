<?php

namespace App\Services;

use App\Services\Contracts\ServiceInterface;
use App\User;

class UserService implements ServiceInterface
{
    /**
     * @param  string  $login
     * @return bool
     */
    public static function checksIsBlockedByLogin(string $login): bool
    {
        $user = User::whereLogin($login)->first();
        if (!$user) {
            return false;
        }

        return (bool) $user->blocked;
    }


}