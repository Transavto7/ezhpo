<?php

namespace App\Enums;

class TripTicketTemplateEnum
{
    const S4 = '4s';

    public static function labels(): array
    {
        return [
            self::S4 => '4-С',
        ];
    }
}
