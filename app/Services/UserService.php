<?php

namespace App\Services;

use App\Company;
use App\Driver;
use App\Enums\UserEntityType;
use App\Services\HashIdGenerator\DefaultHashIdValidators;
use App\Services\HashIdGenerator\HashedType;
use App\Services\HashIdGenerator\HashIdGenerator;
use App\User;
use Illuminate\Support\Facades\Hash;

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

        return (bool) $user->blocked;
    }

    public static function createUserFromDriver(Driver $driver, ?Company $company = null): User
    {
        $userHashId = HashIdGenerator::generateWithType(DefaultHashIdValidators::user(), HashedType::user());

        $driverHashId = $driver->hash_id;

        if ($company === null) {
            $company = $driver->company;
        }

        /** @var User $user */
        $user = User::create([
            'entity_id' => $driver->id,
            'entity_type' => UserEntityType::driver(),
            'hash_id' => $userHashId,
            'email' => $company->hash_id . '-' . $userHashId . '@ta-7.ru',
            'api_token' => Hash::make(date('H:i:s') . sha1($driverHashId)),
            'login' => $driverHashId,
            'password' => Hash::make($driverHashId),
            'name' => $driver->fio,
            'role' => 3,
            'company_id' => $company->id
        ]);

        $user->roles()->attach(3);

        return $user;
    }
}
