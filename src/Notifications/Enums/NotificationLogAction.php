<?php

declare(strict_types=1);

namespace Src\Notifications\Enums;

use LogicException;
use Src\Core\Emuns\Enum;

final class NotificationLogAction extends Enum
{
    const SHOW = 'show'; // Показ напоминания сотруднику
    const EXECUTE = 'execute'; // Выполнение напоминания сотрудником

    public static function from(string $value): self
    {
        switch ($value) {
            case self::SHOW:
                return new self(self::SHOW);
            case self::EXECUTE:
                return new self(self::EXECUTE);
            default:
                throw new LogicException('Invalid value');
        }
    }

    public function toTranslate(): string
    {
        switch ($this->value()) {
            case self::SHOW:
                return 'Показ сотруднику';
            case self::EXECUTE:
                return 'Выполнение';
            default:
                throw new LogicException('Invalid value');
        }
    }
}
