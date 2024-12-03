<?php

namespace App\Services\CompanyReqsChecker;

class CompanyInfo
{
    private $officialName;

    private $inn;

    private $kpp;

    private $ogrn;

    private $address;

    /**
     * @param string $officialName
     * @param string $inn
     * @param string $ogrn
     * @param string|null $kpp
     * @param string|null $address
     */
    public function __construct(string $officialName, string $inn, string $ogrn, string $kpp = null, string $address = null)
    {
        $this->officialName = $officialName;
        $this->inn = $inn;
        $this->ogrn = $ogrn;
        $this->kpp = $kpp;
        $this->address = $address;
    }

    /**
     * @return string
     */
    public function getOfficialName(): string
    {
        return $this->officialName;
    }

    /**
     * @return string
     */
    public function getInn(): string
    {
        return $this->inn;
    }

    /**
     * @return string|null
     */
    public function getKpp(): ?string
    {
        return $this->kpp;
    }

    /**
     * @return string
     */
    public function getOgrn(): string
    {
        return $this->ogrn;
    }

    /**
     * @return string|null
     */
    public function getAddress(): ?string
    {
        return $this->address;
    }
}
