<?php

namespace Src\ExternalSystem\Dto\Common;

final class MedRecordDto
{
    /**
     * @var DiagnosisInfoDto
     */
    private $diagnosisInfoDto;
    /**
     * @var DoctorDto
     */
    private $doctorDto;

    /**
     * @param DiagnosisInfoDto $diagnosisInfoDto
     * @param DoctorDto $doctorDto
     */
    public function __construct(DiagnosisInfoDto $diagnosisInfoDto, DoctorDto $doctorDto)
    {
        $this->diagnosisInfoDto = $diagnosisInfoDto;
        $this->doctorDto = $doctorDto;
    }

    public function getDiagnosisInfoDto(): DiagnosisInfoDto
    {
        return $this->diagnosisInfoDto;
    }

    public function getDoctorDto(): DoctorDto
    {
        return $this->doctorDto;
    }
}
