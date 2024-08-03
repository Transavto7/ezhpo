<?php

namespace App\Enums;

use Illuminate\Support\Collection;

class UserActionTypesEnum
{
    const CLIENT_LOGIN = 'client_login';
    const CLIENT_DOC_IMPORT = 'client_doc_import';
    const CLIENT_DOC_EXPORT = 'client_doc_export';
    const MEDICAL_CHECKUP_LOG_REQUEST = 'medical_checkup_log_request';
    const TECHNICAL_INSPECTION_LOG_REQUEST = 'technical_inspection_log_request';
    const BRIEFING_LOG_REQUEST = 'briefing_log_request';
    const TRIP_TICKET_PRINTING_LOG_REQUEST = 'trip_ticket_printing_log_request';
    const REPORT_CARD_LOG_REQUEST = 'report_card_log_request';
    const ERROR_REGISTER_LOG_REQUEST = 'error_register_log_request';
    const WAITING_LIST_REQUEST = 'waiting_list_request';

    public static function labels(): array
    {
        return [
            self::CLIENT_LOGIN => 'Вход в ЛКК',
            self::CLIENT_DOC_IMPORT => 'Импорт документа в ЛКК',
            self::CLIENT_DOC_EXPORT => 'Экспорт документа в ЛКК',
            self::MEDICAL_CHECKUP_LOG_REQUEST => 'Запрос журнала МО',
            self::TECHNICAL_INSPECTION_LOG_REQUEST => 'Запрос журнала ТО',
            self::BRIEFING_LOG_REQUEST => 'Запрос журнала инструктажей по БДД',
            self::TRIP_TICKET_PRINTING_LOG_REQUEST => 'Запрос журнала печати ПЛ',
            self::REPORT_CARD_LOG_REQUEST => 'Запрос реестра снятия отчетов с карт',
            self::ERROR_REGISTER_LOG_REQUEST => 'Запрос реестра ошибок СДПО',
            self::WAITING_LIST_REQUEST => 'Запрос очереди на утверждение',
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
