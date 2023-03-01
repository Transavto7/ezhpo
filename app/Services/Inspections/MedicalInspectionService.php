<?php

namespace App\Services\Inspections;

use App\Anketa;
use App\Services\Contracts\BaseInspectionService;

class MedicalInspectionService extends BaseInspectionService
{
    public function getInspectionConclusion(Anketa $inspection) : array
    {
        return [];
    }

}