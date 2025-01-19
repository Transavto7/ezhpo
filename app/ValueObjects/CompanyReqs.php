<?php

namespace App\ValueObjects;

class CompanyReqs
{
    private $inn;

    private $kpp;

    private $ogrn;

    /**
     * @param string $inn
     * @param string $kpp
     * @param string $ogrn
     */
    public function __construct(string $inn, string $kpp = '', string $ogrn = '')
    {
        $this->inn = trim($inn);
        $this->ogrn = trim ($ogrn);
        $this->kpp = trim($kpp);
    }

    public function isValidFormat(): bool
    {
        return $this->isOrganizationFormat() || $this->isPersonalFormat();
    }

    private function isPersonalInnFormat(): bool
    {
        if (!ctype_digit($this->inn)) {
            return false;
        }

        return strlen($this->inn) === 12;
    }

    private function isPersonalOgrnFormat(): bool
    {
        if (strlen($this->ogrn) === 0) {
            return true;
        }

        if (!ctype_digit($this->ogrn)) {
            return false;
        }

        return strlen($this->ogrn) === 15;
    }

    private function isOrganizationInnFormat(): bool
    {
        if (!ctype_digit($this->inn)) {
            return false;
        }

        return strlen($this->inn) === 10;
    }

    private function isOrganizationOgrnFormat(): bool
    {
        if (!ctype_digit($this->ogrn)) {
            return false;
        }

        return strlen($this->ogrn) === 13;
    }

    private function isOrganizationKppFormat(): bool
    {
        if (!ctype_digit($this->kpp)) {
            return false;
        }

        if (strlen($this->kpp) !== 9) {
            return false;
        }

        return true;
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function getKpp(): string
    {
        return $this->kpp;
    }

    public function getOgrn(): string
    {
        return $this->ogrn;
    }

    public function isPersonalFormat(): bool
    {
        return $this->isPersonalInnFormat()
            && $this->isPersonalOgrnFormat();
    }

    public function isOrganizationFormat(): bool
    {
        return $this->isOrganizationInnFormat()
            && $this->isOrganizationOgrnFormat()
            && $this->isOrganizationKppFormat();
    }
}
