<?php

namespace Src\ExternalSystem\Dto\Common;

final class PersonDto
{
    /**
     * @var HumanNameDto
     */
    private $humanNameDto;
    /**
     * @var string
     */
    private $isPersonMis;

    /**
     * @param HumanNameDto $humanNameDto
     * @param string $isPersonMis
     */
    public function __construct(HumanNameDto $humanNameDto, string $isPersonMis)
    {
        $this->humanNameDto = $humanNameDto;
        $this->isPersonMis = $isPersonMis;
    }

    public function getHumanNameDto(): HumanNameDto
    {
        return $this->humanNameDto;
    }

    public function getIsPersonMis(): string
    {
        return $this->isPersonMis;
    }
}
