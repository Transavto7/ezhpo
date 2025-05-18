<?php

declare(strict_types=1);

namespace Src\Notifications\Enums;

use Src\Core\Emuns\Enum;

final class NotificationFilterStatus extends Enum
{
    const VIEWED = 'viewed';
    const READ = 'read';
    const COMPLETED = 'completed';
    const EXPIRED = 'expired';

    public static function from(string $value): self
    {
        switch ($value) {
            case self::VIEWED:
                return new self(self::VIEWED);
            case self::READ:
                return new self(self::READ);
            case self::COMPLETED:
                return new self(self::COMPLETED);
            case self::EXPIRED:
                return new self(self::EXPIRED);
            default:
                throw new \LogicException("Invalid notification filter status value: $value");
        }
    }

    public function getTitle(): string
    {
        switch ($this->value()) {
            case self::VIEWED:
                return 'Просмотрено';
            case self::READ:
                return 'Прочтено';
            case self::EXPIRED:
                return 'Просрочено';
            case self::COMPLETED:
                return 'Выполнено';
            default:
                throw new \LogicException("Invalid notification filter status value: {$this->value()}");
        }
    }
}
