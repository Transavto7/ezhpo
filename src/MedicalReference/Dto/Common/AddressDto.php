<?php

namespace Src\MedicalReference\Dto\Common;

class AddressDto
{
    /**
     * @var string
     */
    private $streetAddressLine;
    /**
     * @var int
     */
    private $state;

    /**
     * @param string $streetAddressLine
     * @param int $state
     */
    public function __construct(string $streetAddressLine, int $state)
    {
        $this->streetAddressLine = $streetAddressLine;
        $this->state = $state;
    }

    public function getStreetAddressLine(): string
    {
        return $this->streetAddressLine;
    }

    public function getState(): int
    {
        return $this->state;
    }
}
