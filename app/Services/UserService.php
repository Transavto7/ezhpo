<?php

namespace App\Services;

use App\Company;
use App\Driver;
use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\User;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Facades\Hash;
use Src\Users\Management\Commands\CreateUser\CreateUserCommand;
use Src\Users\Management\Commands\UpdateUserAccess\UpdateUserAccessCommand;

class UserService
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

        return $user->isBlocked();
    }

    public static function createUserFromDriver(Driver $driver, ?Company $company = null): User
    {
        $driverHashId = $driver->hash_id;

        if ($company === null) {
            $company = $driver->company;
        }

        $dispatcher = app()->make(Dispatcher::class);

        $user = $dispatcher->dispatch(new CreateUserCommand(
            UserEntityType::driver(),
            $driverHashId,
            $company->hash_id . '-' . $driverHashId . '@ta-7.ru',
            $driverHashId,
            Hash::make(date('H:i:s') . sha1($driverHashId)),
            3
        ));

        $dispatcher->dispatch(new UpdateUserAccessCommand(
            $user,
            [UserRoleEnum::DRIVER],
            []
        ));

        return $user;
    }
}
