<?php

namespace App\Services\CompanyReqsChecker;

class DaDataCompany
{
    private $name;

    private $inn;

    private $kpp;

    /**
     * @param string $name
     * @param string $inn
     * @param string|null $kpp
     */
    public function __construct(string $name, string $inn, string $kpp = null)
    {
        $this->name = $name;
        $this->inn = $inn;
        $this->kpp = $kpp;
    }

    /**
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
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
