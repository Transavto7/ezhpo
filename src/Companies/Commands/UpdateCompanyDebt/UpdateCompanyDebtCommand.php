<?php

namespace Src\Companies\Commands\UpdateCompanyDebt;

use App\Company;

class UpdateCompanyDebtCommand
{
    /**
     * @var Company
     */
    private $company;

    /**
     * @var bool
     */
    private $hasDebt;

    /**
     * @param Company $company
     * @param bool $hasDebt
     */
    public function __construct(Company $company, bool $hasDebt)
    {
        $this->company = $company;
        $this->hasDebt = $hasDebt;
    }

    /**
     * @return Company
     */
    public function getCompany(): Company
    {
        return $this->company;
    }

    /**
     * @return bool
     */
    public function hasDebt(): bool
    {
        return $this->hasDebt;
    }
}
