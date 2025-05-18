<?php

declare(strict_types=1);

namespace Src\Reminders\Enums;

use Src\Core\Emuns\Enum;

final class ReminderAction extends Enum
{
    const CREATE_INSPECTION = 'create_inspection';

    public static function createInspection(): self
    {
        return new self(self::CREATE_INSPECTION);
    }

    public static function from(string $value): self
    {
        switch ($value) {
            case self::CREATE_INSPECTION:
                return self::createInspection();
            default:
                throw new \LogicException('Invalid value');
        }
    }

    public function getTitle(): string
    {
        switch ($this->value()) {
            case self::CREATE_INSPECTION:
                return 'Создание осмотра';
            default:
                throw new \LogicException("Invalid reminder action: {$this->value()}");
        }
    }
}
