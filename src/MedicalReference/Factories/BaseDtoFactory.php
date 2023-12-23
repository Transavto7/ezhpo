<?php

namespace Src\MedicalReference\Factories;

use Carbon\Carbon;
use Src\MedicalReference\Dto\BaseDto;
use Src\MedicalReference\Dto\Common\OID;

class BaseDtoFactory
{
    public function createDto(): BaseDto
    {
        return new BaseDto(
            new OID('7133', '1.2.643.5.1.13.13.12.2.77.7823.100.1.1.51'),
            Carbon::now(),
            config('medical-reference.confidentiality_code'),
            new OID('7123', '1.2.643.5.1.13.13.12.2.77.7823.100.1.1.50'),
            '1'
        );
    }
}
