<?php

namespace App\Enums;

class OneCSyncStatusEnum
{
    const NON_CREATED = 0;

    const NEED_UPDATE = 1;

    const SYNCED = 2;

    static function getTitles(): array
    {
        return [
            self::NON_CREATED => 'Нет',
            self::NEED_UPDATE => 'Нужно обновление в 1С',
            self::SYNCED => 'Да',
        ];
    }

    static function getTitle(int $value): string
    {
        return self::getTitles()[$value] ?? 'Неизвестный статус';
    }
}
