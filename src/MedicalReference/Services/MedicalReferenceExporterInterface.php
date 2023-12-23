<?php

namespace Src\MedicalReference\Services;

use Src\MedicalReference\Dto\BaseDto;

interface MedicalReferenceExporterInterface
{
    public function export(BaseDto $baseDto);
}
