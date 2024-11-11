<?php

namespace App\Enums;

class BlockActionReasonsEnum
{
    const COMPANY_BLOCK = 'company_block';

    public static function getLabel(string $reason): string
    {
        switch ($reason) {
            case self::COMPANY_BLOCK:
                return 'Компания временно заблокирована. Необходимо связаться с руководителем!';
            default:
                return "Неизвестная причина - $reason";
        }
    }
}
