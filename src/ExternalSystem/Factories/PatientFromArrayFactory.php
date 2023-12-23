<?php

namespace Src\ExternalSystem\Factories;

use Carbon\Carbon;
use Src\ExternalSystem\Dto\Common\HumanNameDto;
use Src\ExternalSystem\Dto\PatientDto;
use Src\ExternalSystem\Exceptions\HumanNameException;

final class PatientFromArrayFactory
{
    /**
     * @param array{
     *      aIdPatientMIS: string,
     *      aFamilyName: string,
     *      aGivenName: string,
     *      aBirthDate: string,
     *      aSex: string,
     *      aIdBloodType: array|int,
     *      aIdLivingAreaType: array|int,
     *      aMiddleName: array|string,
     *      aDeathTime: array|string,
     *      aSocialGroup: array|string,
     *      aSocialGroup: array|string,
     *      aSocialStatus: array|string,
     *      aAddresses: mixed,
     *      aContactPerson: mixed,
     *      aBirthPlace: mixed,
     *      aContacts: mixed,
     *      aDocuments: mixed,
     *      aIdGlobal: mixed,
     *      aIsVip: mixed,
     *      aPrivilege: mixed,
     *      aUseName: mixed,
     * } $data
     * @return PatientDto
     * @throws HumanNameException
     */
    public static function fromArray(array $data): PatientDto
    {
        array_walk($data, function (&$item) {
            $item = $item === [] ? null : $item;
        });

        $fio = $data['aFamilyName'].' '.$data['aGivenName'].' '.$data['aMiddleName'];

        return new PatientDto(
            $data['aIdPatientMIS'],
            HumanNameDto::fromString($fio),
            Carbon::parse($data['aBirthDate']),
            $data['aSex']
        );
    }
}
