<?php

namespace App\Enums;

class TripTicketTemplateEnum
{
    const S_4 = '4_s';

    public static function labels(): array
    {
        return [
            self::S_4 => '4_s',
        ];
    }
}
