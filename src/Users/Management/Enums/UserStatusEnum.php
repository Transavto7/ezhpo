<?php

namespace Src\Users\Management\Enums;

use App\Enums\Enum;

final class UserStatusEnum extends Enum
{
    public const UNBLOCKED = 'unblocked';

    public const BLOCKED = 'blocked';

    public static function unblocked(): self
    {
        return new self(self::UNBLOCKED);
    }

    public static function blocked(): self
    {
        return new self(self::BLOCKED);
    }

    /**
     * @param string $value
     * @return UserStatusEnum
     */
    public static function from(string $value): Enum
    {
        switch (true) {
            case $value === self::UNBLOCKED:
                return self::unblocked();
            case $value === self::BLOCKED:
                return self::blocked();
            default:
                throw new \LogicException("Unsupported user status value '$value'");
        }
    }
}
