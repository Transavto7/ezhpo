<?php

namespace App\Enums;

use Illuminate\Support\Collection;

class UserActionTypesEnum
{
    const CLIENT_LOGIN = 'client_login';
    const CLIENT_DOC_IMPORT = 'client_doc_import';
    const CLIENT_DOC_EXPORT = 'client_doc_export';

    public static function labels(): array
    {
        return [
            self::CLIENT_LOGIN => 'Вход в ЛКК',
            self::CLIENT_DOC_IMPORT => 'Импорт документа в ЛКК',
            self::CLIENT_DOC_EXPORT => 'Экспорт документа в ЛКК',
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
