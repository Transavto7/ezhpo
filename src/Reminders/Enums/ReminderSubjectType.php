<?php

declare(strict_types=1);

namespace Src\Reminders\Enums;

use LogicException;
use Src\Core\Emuns\Enum;

final class ReminderSubjectType extends Enum
{
    const DRIVER = 'driver';
    const CAR = 'car';

    public static function from(string $value): self
    {
        switch ($value) {
            case self::DRIVER:
                return self::driver();
            case self::CAR:
                return self::car();
            default:
                throw new LogicException('Invalid value');
        }
    }

    public static function driver(): self
    {
        return new self(self::DRIVER);
    }

    public static function car(): self
    {
        return new self(self::CAR);
    }
}
