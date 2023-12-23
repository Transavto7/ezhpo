<?php

namespace Src\ExternalSystem\Services\Soap;

use Src\ExternalSystem\Dto\CaseAmbDto;
use Src\ExternalSystem\Dto\PatientDto;
use Throwable;

final class SoapService extends AbstractSoapService
{
    /**
     * @param $idPatientMis
     * @return array
     * @throws Throwable
     */
    public function getPatient($idPatientMis): array
    {
        $xmlRequest = view('external-system::get_patient.request', [
            'idPatientMis' => $idPatientMis
        ])->render();

        return $this->_request(
            config('external-system.api_pix'),
            'GetPatient',
            $xmlRequest
        );
    }

    /**
     * @param PatientDto $dto
     * @throws Throwable
     */
    public function addPatient(PatientDto $dto)
    {
        $xmlRequest = view('external-system::add_patient.request', [
            'patient' => $dto,
            'dateBirth' => $dto->getBirthDate()->format(config('external-system.date_birth_format'))
        ])->render();

        $this->_request(
            config('external-system.api_pix'),
            'AddPatient',
            $xmlRequest
        );
    }

    /**
     * @param PatientDto $dto
     * @return array
     * @throws Throwable
     */
    public function updatePatient(PatientDto $dto): array
    {
        $xmlRequest = view('external-system::update_patient.request', [
            'patient' => $dto,
            'dateBirth' => $dto->getBirthDate()->format(config('external-system.date_birth_format'))
        ])->render();

        return $this->_request(
            config('external-system.api_pix'),
            'UpdatePatient',
            $xmlRequest
        );
    }

    /**
     * @param CaseAmbDto $dto
     * @throws Throwable
     */
    public function addCaseAmb(CaseAmbDto $dto)
    {
        $xmlRequest = view('external-system::add_case.request_amb', [
            'case' => $dto
        ])->render();

        $this->_request(
            config('external-system.api_emk'),
            'AddCase',
            $xmlRequest
        );
    }
}
