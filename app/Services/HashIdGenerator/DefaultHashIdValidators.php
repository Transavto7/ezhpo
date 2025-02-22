<?php
declare(strict_types=1);

namespace App\Services\HashIdGenerator;

use App\Car;
use App\Driver;
use App\User;

final class DefaultHashIdValidators
{
    public static function driver(): callable
    {
        return function (int $hashId) {
            if (Driver::query()->where('hash_id', $hashId)->first()) {
                return false;
            }

            if (User::query()->where('login', $hashId)->first()) {
                return false;
            }

            return true;
        };
    }

    public static function car(): callable
    {
        return function (int $hashId) {
            if (Car::query()->where('hash_id', $hashId)->first()) {
                return false;
            }

            return true;
        };
    }
}
