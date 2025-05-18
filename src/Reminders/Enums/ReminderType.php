<?php

declare(strict_types=1);

namespace Src\Reminders\Enums;

use Src\Core\Emuns\Enum;

final class ReminderType extends Enum
{
    const INFO = 'info';

    const WARNING = 'warning';

    const DANGER = 'danger';

    public static function from(string $value): self
    {
        switch ($value) {
            case self::INFO:
                return new self(self::INFO);
            case self::WARNING:
                return new self(self::WARNING);
            case self::DANGER:
                return new self(self::DANGER);
            default:
                throw new \LogicException('Unknown reminder type: '.$value);
        }
    }

    public function getTitle(): string
    {
        switch ($this->value()) {
            case self::INFO:
                return 'Информационное';
            case self::WARNING:
                return 'Предупреждение';
            case self::DANGER:
                return 'Критическое';
            default:
                throw new \LogicException("Invalid reminder type: {$this->value()}");
        }
    }
}
