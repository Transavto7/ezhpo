<?php

namespace Src\MedicalReference\Dto\Common;

class InitialsDto
{
    /**
     * @var string
     */
    private $family;
    /**
     * @var string
     */
    private $given;
    /**
     * @var ?string
     */
    private $middle;

    /**
     * @param string $family
     * @param string $given
     * @param string|null $middle
     */
    public function __construct(string $family, string $given, ?string $middle)
    {
        $this->family = $family;
        $this->given = $given;
        $this->middle = $middle;
    }

    public function getFamily(): string
    {
        return $this->family;
    }

    public function getGiven(): string
    {
        return $this->given;
    }

    public function getMiddle(): ?string
    {
        return $this->middle;
    }
}
