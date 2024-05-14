<?php

namespace App;

use Illuminate\Database\Eloquent\Collection;

/**
 * @property string $name
 * @property Collection $companies
 */
class AuthUser extends User
{
    protected $table = 'users';

    protected static function hideDefaultUser(): bool
    {
        return false;
    }
}
