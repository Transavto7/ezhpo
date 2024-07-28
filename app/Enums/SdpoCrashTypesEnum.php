<?php

namespace App\Enums;

use Illuminate\Support\Collection;

class SdpoCrashTypesEnum
{
    public static function labels(): array
    {
        return [];
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
        return self::labels()[$value] ?? 'Неизвестный тип';
    }
}
