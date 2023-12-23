<?php

namespace Src\MedicalReference\Dto\Common;

use Carbon\Carbon;

class PatientDto
{
    /**
     * @var InitialsDto
     */
    private $initials;
    /**
     * @var GenderDto
     */
    private $administrativeGenderCode;
    /**
     * @var Carbon
     */
    private $birthTime;

    /**
     * @param InitialsDto $initials
     * @param GenderDto $administrativeGenderCode
     * @param Carbon $birthTime
     */
    public function __construct(InitialsDto $initials, GenderDto $administrativeGenderCode, Carbon $birthTime)
    {
        $this->initials = $initials;
        $this->administrativeGenderCode = $administrativeGenderCode;
        $this->birthTime = $birthTime;
    }

    public function getInitials(): InitialsDto
    {
        return $this->initials;
    }

    public function getAdministrativeGenderCode(): GenderDto
    {
        return $this->administrativeGenderCode;
    }

    public function getBirthTime(): Carbon
    {
        return $this->birthTime;
    }
}
