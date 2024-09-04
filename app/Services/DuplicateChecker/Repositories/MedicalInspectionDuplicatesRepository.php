<?php

namespace App\Services\DuplicateChecker\Repositories;

use App\Services\DuplicateChecker\Dto\Inspection;
use DB;

class MedicalInspectionDuplicatesRepository implements DuplicateRepository
{
    public function getDuplicates(Inspection $inspection)
    {
        return DB::table('anketas')
            ->select('id')
            ->where('driver_id', '=', $inspection->getDriverId())
            ->where(DB::raw('DATE(date)'), '=', $inspection->getDate()->format('Y-m-d'))
            ->where('type_view', '=', $inspection->getType())
            ->get();
    }
}
