<?php

namespace App\Enums;

use Illuminate\Support\Collection;

class FormLogActionTypesEnum
{
    const UPDATING = 'updating';
    const DELETING = 'deleting';
    const RESTORING = 'restoring';
    const APPROVAL = 'approval';
    const QUEUE_PROCESSING = 'queue_processing';
    const SET_FEEDBACK = 'set_feedback';

    public static function labels(): array
    {
        return [
            self::UPDATING => 'Редактирование',
            self::DELETING => 'Удаление',
            self::RESTORING => 'Восстановление',
            self::APPROVAL => 'Утверждение',
            self::QUEUE_PROCESSING => 'Обработка очереди',
            self::SET_FEEDBACK => 'Оценка осмотра'
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

    public static function values(): array
    {
        return [
            self::UPDATING,
            self::DELETING,
            self::RESTORING,
            self::APPROVAL,
            self::QUEUE_PROCESSING,
            self::SET_FEEDBACK
        ];
    }
}
