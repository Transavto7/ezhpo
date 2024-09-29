<?php

namespace App\Services\DuplicateChecker\Repositories;

use App\Services\DuplicateChecker\Dto\Inspection;
use DB;
use Illuminate\Database\Query\Builder;
use Illuminate\Support\Collection;

class InspectionDuplicatesRepository implements DuplicateRepository
{
    public function getDuplicates(Inspection $inspection): Collection
    {
        return DB::table('anketas')
            ->select('id')
            ->where('driver_id', '=', $inspection->getDriverId())
            ->when($inspection->getCarId(), function (Builder $query) use ($inspection) {
                $query->where('car_id', '=', $inspection->getCarId());
            })
            ->where(DB::raw('DATE(date)'), '=', $inspection->getDate()->format('Y-m-d'))
            ->where('type_view', '=', $inspection->getType())
            ->where('type_anketa', '=', $inspection->getFormType())
            ->where('in_cart', '<>', 1)
            ->get();
    }
}
