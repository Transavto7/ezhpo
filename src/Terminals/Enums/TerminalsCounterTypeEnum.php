<?php

namespace Src\Terminals\Enums;

use App\Enums\Enum;

final class TerminalsCounterTypeEnum extends Enum
{
    const LAST_MONTH_AMOUNT = 'last_month_amount';
    const CURRENT_MONTH_AMOUNT = 'current_month_amount';

    public static function lastMonthAmount(): TerminalsCounterTypeEnum
    {
        return new self(self::LAST_MONTH_AMOUNT);
    }

    public static function currentMonthAmount(): TerminalsCounterTypeEnum
    {
        return new self(self::CURRENT_MONTH_AMOUNT);
    }

    /**
     * @param string $value
     * @return TerminalsCounterTypeEnum
     */
    public static function from(string $value): Enum
    {
        switch (true) {
            case $value === self::LAST_MONTH_AMOUNT:
                return self::lastMonthAmount();
            case $value === self::CURRENT_MONTH_AMOUNT:
                return self::currentMonthAmount();
            default:
                throw new \LogicException("Unsupported user entity type value '$value'");
        }
    }
}
