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

    const WORK_SCHEDULE_REQUEST = 'work_schedule_request';
    const SERVICE_REPORT_REQUEST = 'service_report_request';
    const MEDICAL_INSPECTIONS_NUMBER_REPORT_REQUEST = 'medical_inspections_number_report_request';
    const TECHNICAL_INSPECTIONS_NUMBER_REPORT_REQUEST = 'technical_inspections_number_report_request';
    const ALL_INSPECTIONS_NUMBER_REPORT_REQUEST = 'all_inspections_number_report_request';

    const CAR_IMPORT = 'car_import';
    const CAR_EXPORT = 'car_export';
    const DRIVER_IMPORT = 'driver_import';
    const DRIVER_EXPORT = 'driver_export';

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

            self::WORK_SCHEDULE_REQUEST => 'Запрос графика работы пунктов выпуска',
            self::SERVICE_REPORT_REQUEST => 'Запрос отчета по услугам компании',
            self::MEDICAL_INSPECTIONS_NUMBER_REPORT_REQUEST => 'Запрос отчета по количеству медосмотров',
            self::TECHNICAL_INSPECTIONS_NUMBER_REPORT_REQUEST => 'Запрос отчета по количеству техосмотров',
            self::ALL_INSPECTIONS_NUMBER_REPORT_REQUEST => 'Запрос отчета по количеству всех осмотров',

            self::CAR_IMPORT => 'Импорт автомобилей',
            self::CAR_EXPORT => 'Экспорт автомобилей',
            self::DRIVER_IMPORT => 'Импорт водителей',
            self::DRIVER_EXPORT => 'Экспорт водителей',
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
