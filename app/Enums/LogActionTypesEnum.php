<?php

namespace App\Enums;

use Illuminate\Support\Collection;

class LogActionTypesEnum
{
    const CREATING = 'creating';
    const UPDATING = 'updating';
    const DELETING = 'deleting';
    const RESTORING = 'restoring';
    const DETACHING = 'detaching';
    const ATTACHING = 'attaching';

    public static function labels(): array
    {
        return [
            self::CREATING => 'Создание',
            self::UPDATING => 'Редактирование',
            self::DELETING => 'Удаление',
            self::RESTORING => 'Восстановление',
            self::ATTACHING => 'Добавление связи',
            self::DETACHING => 'Удаление связи'
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
        return self::labels()[$value] ?? 'Неизвестное действие';
    }
}
