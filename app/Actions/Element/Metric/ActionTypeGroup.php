<?php

namespace App\Actions\Element\Metric;

use App\Enums\UserActionTypesEnum;

class ActionTypeGroup
{
    protected $authorization = [
        UserActionTypesEnum::CLIENT_LOGIN
    ];
    protected $logRequest = [
        UserActionTypesEnum::MEDICAL_CHECKUP_LOG_REQUEST,
        UserActionTypesEnum::TECHNICAL_INSPECTION_LOG_REQUEST,
        UserActionTypesEnum::BRIEFING_LOG_REQUEST,
        UserActionTypesEnum::TRIP_TICKET_PRINTING_LOG_REQUEST,
        UserActionTypesEnum::REPORT_CARD_LOG_REQUEST,
        UserActionTypesEnum::ERROR_REGISTER_LOG_REQUEST,
        UserActionTypesEnum::WAITING_LIST_REQUEST,
    ];
    protected $reportRequest = [
        UserActionTypesEnum::WORK_SCHEDULE_REQUEST,
        UserActionTypesEnum::SERVICE_REPORT_REQUEST,
        UserActionTypesEnum::MEDICAL_INSPECTIONS_NUMBER_REPORT_REQUEST,
        UserActionTypesEnum::TECHNICAL_INSPECTIONS_NUMBER_REPORT_REQUEST,
        UserActionTypesEnum::ALL_INSPECTIONS_NUMBER_REPORT_REQUEST,
    ];
    protected $import = [
        UserActionTypesEnum::CLIENT_DOC_IMPORT,
        UserActionTypesEnum::CAR_IMPORT,
        UserActionTypesEnum::DRIVER_IMPORT,
    ];
    protected $docRequest = [
        UserActionTypesEnum::CLIENT_DOC_EXPORT,
        UserActionTypesEnum::CAR_EXPORT,
        UserActionTypesEnum::DRIVER_EXPORT,
    ];

    public function fromType(array $actions): array
    {
        $agg = [
            'authorization' => 0,
            'logRequest' => 0,
            'reportRequest' => 0,
            'import' => 0,
            'docRequest' => 0,
        ];

        foreach ($actions as $action => $count) {
            foreach ($agg as $key => $value) {
                $agg = $this->checkAction($action, $count, $agg, $this->{$key}, $key);
            }
        }

        return $agg;
    }

    private function checkAction(string $action, int $count, array $agg, array $group, string $key): array
    {
        if (in_array($action, $group)) {
            $agg[$key] += $count;
        }

        return $agg;
    }
}
