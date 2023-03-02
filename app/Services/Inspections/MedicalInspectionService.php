<?php

namespace App\Services\Inspections;

use App\Anketa;
use App\Services\Contracts\BaseInspectionService;

class MedicalInspectionService extends BaseInspectionService
{
    /**
     * @param  \App\Anketa  $anketa
     * @return bool
     */
    public static function acceptConclusion(Anketa $anketa) : bool
    {
        if ($anketa->complaint === 'Да') {
            return false;
        }

        return ($anketa->checkTemperatureFine() && $anketa->checkBloodPressureFine());
    }
}