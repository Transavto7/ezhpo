<?php

declare(strict_types=1);

namespace Src\Reminders\Enums;

use LogicException;
use Src\Core\Emuns\Enum;

final class ReminderStatus extends Enum
{
    const ENABLE = 'enable';

    const DISABLE = 'disable';

    public static function from(string $value): self
    {
        switch ($value) {
            case self::ENABLE:
                return self::enable();
            case self::DISABLE:
                return self::disable();
            default:
                throw new LogicException('Invalid value');
        }
    }

    public static function enable(): self
    {
        return new self(self::ENABLE);
    }

    public static function disable(): self
    {
        return new self(self::DISABLE);
    }

    public function getTitle(): string
    {
        switch ($this->value()) {
            case self::ENABLE:
                return 'Активно';
            case self::DISABLE:
                return 'Неактивно';
            default:
                throw new \LogicException("Invalid reminder status: {$this->value()}");
        }
    }
}
