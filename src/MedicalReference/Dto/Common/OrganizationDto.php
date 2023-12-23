<?php

namespace Src\MedicalReference\Dto\Common;

class OrganizationDto
{
    /**
     * @var string
     */
    private $ogrn;
    /**
     * @var string
     */
    private $name;
    /**
     * @var AddressDto
     */
    private $addr;

    /**
     * @param string $ogrn
     * @param string $name
     * @param AddressDto $addr
     */
    public function __construct(string $ogrn, string $name, AddressDto $addr)
    {
        $this->ogrn = $ogrn;
        $this->name = $name;
        $this->addr = $addr;
    }

    public function getOgrn(): string
    {
        return $this->ogrn;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getAddr(): AddressDto
    {
        return $this->addr;
    }
}
