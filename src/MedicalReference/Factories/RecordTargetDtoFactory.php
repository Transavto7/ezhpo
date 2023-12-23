<?php

namespace Src\MedicalReference\Factories;

use App\Driver;
use DomainException;
use Src\MedicalReference\Dto\Common\OID;
use Src\MedicalReference\Dto\RecordTargetDto;

class RecordTargetDtoFactory
{
    public function createDto(Driver $driver): RecordTargetDto
    {
        $id = new OID('9589237', '1.2.643.5.1.13.13.12.2.77.7823.100.1.1.10');
        $idSnils = $this->getIdSnils($driver);

        dd($driver);

        return new RecordTargetDto(
            $id,
            $idSnils,
            '',
            '',
            ''
        );
    }

    private function getIdSnils(Driver $driver): OID
    {
        if (!$driver->snils) {
            throw new DomainException('У водителя не указан СНИЛС');
        }

        return new OID('000-000-000 00', $driver->snils);
    }

    private function ()
    {

    }
}
