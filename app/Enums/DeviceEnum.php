<?php

namespace App\Enums;

use Illuminate\Support\Collection;

final class DeviceEnum
{
    const ALCOHOL_METER = 'alcohol_meter';
    const BLOOD_PRESSURE_MONITOR = 'blood_pressure_monitor';
    const THERMOMETER = 'thermometer';
    const MONOBLOCK = 'monoblock';
    const PRINTER = 'printer';
    const CAMERA = 'camera';
    const USB_MODEM = 'usb_modem';

    public static function labels(): array
    {
        return [
            self::ALCOHOL_METER => 'Алкометр',
            self::BLOOD_PRESSURE_MONITOR => 'Тонометр',
            self::THERMOMETER => 'Термометр',
            self::MONOBLOCK => 'Моноблок',
            self::PRINTER => 'Принтер',
            self::CAMERA => 'Камера',
            self::USB_MODEM => 'USB модем',
        ];
    }

    public static function options(): Collection
    {
        return collect(self::labels())
            ->map(function ($value, $key) {
                return [
                    'id' => $key,
                    'text' => $value
                ];
            })
            ->values();
    }

    public static function label(string $value): string
    {
        return self::labels()[$value];
    }
}
