<?php

declare(strict_types=1);

namespace Src\Notifications\Enums;

use Src\Core\Emuns\Enum;

final class NotificationLogAction extends Enum
{
    const CREATE = 'create';
    const READ = 'read';
    const COMPLETE = 'complete';

    public static function create(): self
    {
        return new self(self::CREATE);
    }

    public static function read(): self
    {
        return new self(self::READ);
    }

    public static function complete(): self
    {
        return new self(self::COMPLETE);
    }

    public static function from(string $value): self
    {
        switch ($value) {
            case self::CREATE:
                return self::create();
            case self::READ:
                return self::read();
            case self::COMPLETE:
                return self::complete();
            default:
                throw new \LogicException("Invalid notification log action value: $value");
        }
    }

    public function getTitle(): string
    {
        switch ($this->value()) {
            case self::CREATE:
                return 'Создание';
            case self::READ:
                return 'Прочтение';
            case self::COMPLETE:
                return 'Выполнение';
            default:
                throw new \LogicException("Invalid notification log action value: {$this->value()}");
        }
    }
}
