<?php

declare(strict_types=1);

namespace Src\Reminders\Enums;

use LogicException;
use Src\Core\Emuns\Enum;

final class ReminderLogAction extends Enum
{
    const SHOW = 'show'; // Показ задачи сотруднику
    const UPDATE = 'update'; // Обновление задачи
    const ACTIVATE = 'activate'; // Активация/деактивация задачи
    const EXECUTE = 'execute'; // Выполнение задачи сотрудником / сотрудниками

    public static function from(string $value): self
    {
        switch ($value) {
            case self::SHOW:
                return new self(self::SHOW);
            case self::UPDATE:
                return new self(self::UPDATE);
            case self::ACTIVATE:
                return new self(self::ACTIVATE);
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
            case self::UPDATE:
                return 'Обновление';
            case self::ACTIVATE:
                return 'Активация/деактивация';
            case self::EXECUTE:
                return 'Выполнение';
            default:
                throw new LogicException('Invalid value');
        }
    }
}
