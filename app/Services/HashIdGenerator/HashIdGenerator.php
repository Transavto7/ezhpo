<?php
declare(strict_types=1);

namespace App\Services\HashIdGenerator;

use Illuminate\Support\Facades\Facade;

/**
 * @method static int generateWithType(callable $validator, HashedType|null $hashedType = null)
 * @method static int generateWithSettings(callable $validator, int $min = 0, int $max = 999999, int $maxTries = 2)
 *
 * @see HashIdGeneratorService
 */
final class HashIdGenerator extends Facade
{
    public static function getFacadeAccessor(): string
    {
        return 'hash-id-generator';
    }
}
