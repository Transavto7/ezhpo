<?php

namespace Src\ExternalSystem\Dto;

use Carbon\Carbon;
use Src\ExternalSystem\Dto\Common\HumanNameDto;

final class PatientDto
{
    /**
     * @var string
     */
    private $idPatientMIS;
    /**
     * @var HumanNameDto
     */
    private $humanNameDto;
    /**
     * @var Carbon
     */
    private $birthDate;
    /**
     * @var int
     */
    private $sex;

    /**
     * @param string $idPatientMIS
     * @param HumanNameDto $humanNameDto
     * @param Carbon $birthDate
     * @param int $sex
     */
    public function __construct(string $idPatientMIS, HumanNameDto $humanNameDto, Carbon $birthDate, int $sex)
    {
        $this->idPatientMIS = $idPatientMIS;
        $this->humanNameDto = $humanNameDto;
        $this->birthDate = $birthDate;
        $this->sex = $sex;
    }

    public function getIdPatientMIS(): string
    {
        return $this->idPatientMIS;
    }

    public function getHumanNameDto(): HumanNameDto
    {
        return $this->humanNameDto;
    }

    public function getBirthDate(): Carbon
    {
        return $this->birthDate;
    }

    public function getSex(): int
    {
        return $this->sex;
    }
}
