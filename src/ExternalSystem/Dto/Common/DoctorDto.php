<?php

namespace Src\ExternalSystem\Dto\Common;

final class DoctorDto
{
    /**
     * @var PersonDto
     */
    private $personDto;
    /**
     * @var int
     */
    private $idPosition;
    /**
     * @var int
     */
    private $idSpeciality;

    /**
     * @param PersonDto $personDto
     * @param int $idPosition
     * @param int $idSpeciality
     */
    public function __construct(PersonDto $personDto, int $idPosition, int $idSpeciality)
    {
        $this->personDto = $personDto;
        $this->idPosition = $idPosition;
        $this->idSpeciality = $idSpeciality;
    }

    public function getPersonDto(): PersonDto
    {
        return $this->personDto;
    }

    public function getIdPosition(): int
    {
        return $this->idPosition;
    }

    public function getIdSpeciality(): int
    {
        return $this->idSpeciality;
    }
}
