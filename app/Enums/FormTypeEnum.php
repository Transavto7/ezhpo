<?php

namespace App\Enums;

class FormTypeEnum
{
    const MEDIC = 'medic';

    const TECH = 'tech';

    const BDD = 'bdd';

    const REPORT_CARD = 'report_cart';

    const PRINT_PL = 'pechat_pl';

    const PAK = 'pak';

    const PAK_QUEUE = 'pak_queue';

    /** @deprecated  */
    const VID_PL = 'vid_pl';

    public static function toArray(): array
    {
        return [
            self::MEDIC,
            self::TECH,
            self::BDD,
            self::REPORT_CARD,
            self::PRINT_PL,
            self::PAK,
            self::PAK_QUEUE,
            self::VID_PL,
        ];
    }

    public static function getLabel(string $type): string
    {
        switch ($type) {
            case self::MEDIC:
                return 'медосмотр';
            case self::TECH:
                return 'техосмотр';
            case self::BDD:
                return 'инструктаж БДД';
            default:
                return $type;
        }
    }
}
