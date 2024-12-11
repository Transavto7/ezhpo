<?php

namespace App\Services\CompanyReqsChecker;

class DaDataCompany
{
    private $officialName;

    private $inn;

    private $kpp;

    /**
     * @param string $officialName
     * @param string $inn
     * @param string|null $kpp
     */
    public function __construct(string $officialName, string $inn, string $kpp = null)
    {
        $this->officialName = $officialName;
        $this->inn = $inn;
        $this->kpp = $kpp;
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
}
