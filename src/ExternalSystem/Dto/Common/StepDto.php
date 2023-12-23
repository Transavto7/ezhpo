<?php

namespace Src\ExternalSystem\Dto\Common;

use Carbon\Carbon;

final class StepDto
{
    /**
     * @var Carbon
     */
    private $dateStart;
    /**
     * @var Carbon
     */
    private $dateEnd;
    /**
     * @var string
     */
    private $idStepMis;
    /**
     * @var DoctorDto
     */
    private $doctor;
    /**
     * @var int
     */
    private $idVisitPlace;
    /**
     * @var int
     */
    private $idVisitPurpose;

    /**
     * @param Carbon $dateStart
     * @param Carbon $dateEnd
     * @param string $idStepMis
     * @param DoctorDto $doctor
     * @param int $idVisitPlace
     * @param int $idVisitPurpose
     */
    public function __construct(Carbon $dateStart, Carbon $dateEnd, string $idStepMis, DoctorDto $doctor, int $idVisitPlace, int $idVisitPurpose)
    {
        $this->dateStart = $dateStart;
        $this->dateEnd = $dateEnd;
        $this->idStepMis = $idStepMis;
        $this->doctor = $doctor;
        $this->idVisitPlace = $idVisitPlace;
        $this->idVisitPurpose = $idVisitPurpose;
    }

    public function getDateStart(): Carbon
    {
        return $this->dateStart;
    }

    public function getDateEnd(): Carbon
    {
        return $this->dateEnd;
    }

    public function getIdStepMis(): string
    {
        return $this->idStepMis;
    }

    public function getDoctor(): DoctorDto
    {
        return $this->doctor;
    }

    public function getIdVisitPlace(): int
    {
        return $this->idVisitPlace;
    }

    public function getIdVisitPurpose(): int
    {
        return $this->idVisitPurpose;
    }
}
