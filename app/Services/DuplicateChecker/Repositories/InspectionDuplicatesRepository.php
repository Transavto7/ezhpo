<?php

namespace App\Services\DuplicateChecker\Repositories;

use App\Services\DuplicateChecker\Dto\Inspection;
use DB;
use Illuminate\Support\Collection;

class InspectionDuplicatesRepository implements DuplicateRepository
{
    public function getDuplicates(Inspection $inspection): Collection
    {
        return DB::table('anketas')
            ->select('id')
            ->where('driver_id', '=', $inspection->getDriverId())
            ->where(DB::raw('DATE(date)'), '=', $inspection->getDate()->format('Y-m-d'))
            ->where('type_view', '=', $inspection->getType())
            ->where('type_anketa', '=', $inspection->getFormType())
            ->where('in_cart', '<>', 1)
            ->get();
    }
}
