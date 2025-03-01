<?php

namespace App\Services;

use App\Car;
use App\Company;
use App\DDates;
use App\Driver;
use Exception;

class RedDatesCheckerService
{
    /**
     * @throws Exception
     */
    public static function check(string $date, $model): array
    {
        $datesMap = [
            Driver::class => 'Driver',
            Car::class => 'Car',
            Company::class => 'Company'
        ];

        $modelClass = get_class($model);
        if (!isset($datesMap[$modelClass])) {
            throw new Exception("Попытка контроля дат для неизвестной модели - {$modelClass}");
        }

        $datesToCheck = DDates::where('item_model', $datesMap[$modelClass])->get();

        $redDates = [];
        foreach ($datesToCheck as $dateToCheck) {
            $fieldDateCheck = $dateToCheck->field;

            if (!isset($model[$fieldDateCheck])) {
                continue;
            }

            $fieldDateItemValue = $model[$fieldDateCheck];

            $dateAction = $dateToCheck->action . ' ' . $dateToCheck->days . ' days';

            $dateCheckWithForm = date('Y-m-d', strtotime($fieldDateItemValue . ' ' . $dateAction));

            if ($dateCheckWithForm > $date) {
                continue;
            }

            $redDates[$fieldDateCheck] = [
                'value' => $fieldDateItemValue,
                'item_model' => $dateToCheck->item_model,
                'item_id' => $model->id,
                'item_field' => $fieldDateCheck
            ];
        }

        return $redDates;
    }
}
