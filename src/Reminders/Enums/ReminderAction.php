<?php

declare(strict_types=1);

namespace Src\Reminders\Enums;

use LogicException;
use Src\Core\Emuns\Enum;

final class ReminderAction extends Enum
{
    const CREATE_INSPECTION = 'create_inspection';

    public static function from(string $value): self
    {
        switch ($value) {
            case self::CREATE_INSPECTION:
                return new self(self::CREATE_INSPECTION);
            default:
                throw new LogicException('Invalid value');
        }
    }
}
