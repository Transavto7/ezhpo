<?php

namespace App\Enums;

class BlockActionReasonsEnum
{
    const COMPANY_BLOCK = 'company_block';

    const TERMINAL_BLOCK = 'terminal_block';

    const DRIVER_BLOCK = 'driver_block';

    const CAR_BLOCK = 'car_block';

    const DRIVER_OFFLINE_ONLY = 'driver_offline_only';

    public static function getLabel(string $reason): string
    {
        return self::getLabels()[$reason] ?? "Неизвестная причина - $reason";
    }

    private static function getLabels(): array
    {
        return [
            self::CAR_BLOCK => 'Авто с указанным ID уволено!',
            self::COMPANY_BLOCK => 'Компания временно заблокирована. Необходимо связаться с руководителем!',
            self::DRIVER_BLOCK => 'Водитель с указанным ID уволен!',
            self::DRIVER_OFFLINE_ONLY => 'Водителю ограничен дистанционный выпуск, обратитесь к медицинскому сотруднику на Пункте Выпуска!',
            self::TERMINAL_BLOCK => 'Терминал временно заблокирован. Проверьте оплату услуг!',
        ];
    }
}
