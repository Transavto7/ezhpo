<?php

namespace App\Enums;

class TransportationTypeEnum
{
    const REGULAR = 'regular';

    const ORDER = 'order';

    const TAXI = 'taxi';

    const CARGO = 'cargo';

    const SELF_NEEDS = 'self_needs';

    const CHILD_TRANSPORTATION = 'child_transportation';

    public static function labels(): array
    {
        return [
            self::REGULAR => 'Регулярная перевозка пассажиров и багажа',
            self::ORDER => 'Перевозка пассажиров и багажа по заказу',
            self::TAXI => 'Перевозка пассажиров и багажа легковым такси',
            self::CARGO => 'Перевозка грузов',
            self::SELF_NEEDS => 'Перевозка для собственных нужд',
            self::CHILD_TRANSPORTATION => 'Организованная перевозка группы детей',
        ];
    }
}
