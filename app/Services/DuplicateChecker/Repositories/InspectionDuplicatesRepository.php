<?php

namespace App\Services\DuplicateChecker\Repositories;

use App\Enums\FormTypeEnum;
use App\Services\DuplicateChecker\Dto\Inspection;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class InspectionDuplicatesRepository implements DuplicateRepository
{
    public function getDuplicates(Inspection $inspection): Collection
    {
        $formType = $inspection->getFormType();

        switch ($formType) {
            case FormTypeEnum::MEDIC:
                $table = 'medic_forms';
                break;
            case FormTypeEnum::TECH:
                $table = 'tech_forms';
                break;
            default:
                throw new \Exception("Проверка дубликатов для типа осмотра - $formType не доступна");
        };

        return DB::table('forms')
            ->select('forms.id')
            ->join('forms', 'forms.uuid', '=', "$table.forms_uuid")
            ->where('forms.driver_id', '=', $inspection->getDriverId())
            ->when($inspection->getCarId(), function (Builder $query) use ($table, $inspection) {
                $query->where("$table.car_id", '=', $inspection->getCarId());
            })
            ->where(DB::raw('DATE(date)'), '=', $inspection->getDate()->format('Y-m-d'))
            ->where("$table.type_view", '=', $inspection->getType())
            ->get();
    }
}
