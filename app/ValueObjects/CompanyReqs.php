<?php

namespace App\ValueObjects;

class CompanyReqs
{
    private $inn;

    private $kpp;

    private $officialName;

    /**
     * @param string $inn
     * @param string $kpp
     * @param string $officialName
     */
    public function __construct(string $inn, string $kpp = '', string $officialName = '')
    {
        $this->inn = trim($inn);
        $this->kpp = trim($kpp);
        $this->officialName = trim($officialName);
    }

    public function isValidFormat(): bool
    {
        if (!ctype_digit($this->inn)) {
            return false;
        }

        if (!$this->isPersonalInnFormat() && !$this->isOrganizationInnFormat()) {
            return false;
        }

        if ($this->isOrganizationInnFormat()) {
            if (!ctype_digit($this->kpp)) {
                return false;
            }

            if (strlen($this->kpp) !== 9) {
                return false;
            }
        }

        return true;
    }

    public function isPersonalInnFormat(): bool
    {
        return strlen($this->inn) === 12;
    }

    public function isOrganizationInnFormat(): bool
    {
        return strlen($this->inn) === 10;
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function getKpp(): string
    {
        return $this->kpp;
    }

    public function getOfficialName(): string
    {
        return $this->officialName;
    }
}
