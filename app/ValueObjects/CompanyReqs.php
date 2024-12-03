<?php

namespace App\ValueObjects;

use App\Services\CompanyReqsChecker\CompanyReqsCheckerInterface;

class CompanyReqs
{
    private $inn;

    private $kpp;

    /**
     * @param string $inn
     * @param string $kpp
     */
    public function __construct(string $inn, string $kpp = '')
    {
        $this->inn = trim($inn);
        $this->kpp = trim($kpp);
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

    public function isValid(CompanyReqsCheckerInterface $companyReqsChecker): bool
    {
        if (!$this->isValidFormat()) {
            return false;
        }

        return $companyReqsChecker->check($this);
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function getKpp(): string
    {
        return $this->kpp;
    }
}
