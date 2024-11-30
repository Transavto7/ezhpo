<?php

namespace App\Enums;

class LogisticsMethodEnum
{
    const URBAN = 'urban';

    const SUBURBAN = 'suburban';

    const LONG_DISTANCE = 'long_distance';

    const INTERNATIONAL = 'international';

    public static function labels(): array
    {
        return [
            self::URBAN => 'Городское',
            self::SUBURBAN => 'Пригородное',
            self::LONG_DISTANCE => 'Междугородное',
            self::INTERNATIONAL => 'Международное',
        ];
    }
}
