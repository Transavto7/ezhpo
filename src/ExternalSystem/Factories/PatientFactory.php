<?php

namespace Src\ExternalSystem\Factories;

use App\Driver;
use Carbon\Carbon;
use DomainException;
use Src\ExternalSystem\Dto\Common\HumanNameDto;
use Src\ExternalSystem\Dto\PatientDto;
use Src\ExternalSystem\Exceptions\HumanNameException;

final class PatientFactory
{
    /**
     * @throws HumanNameException
     */
    public static function fromModel(Driver $driver, $driverIdMis): PatientDto
    {
        $sexList = config('external-system.sex');

        $sexList = array_values(array_filter($sexList, function ($item) use ($driver) {
            return mb_strtolower($item['display']) === mb_strtolower($driver->gender);
        }));

        if (!count($sexList)) {
            throw new DomainException('Указанный пол водителя не соответствует ни одной из записей справочника 1.2.643.5.1.13.2.1.1.156');
        }

        return new PatientDto(
            $driverIdMis,
            HumanNameDto::fromString($driver->fio),
            Carbon::parse($driver->year_birthday),
            $sexList[0]['code']
        );
    }
}
