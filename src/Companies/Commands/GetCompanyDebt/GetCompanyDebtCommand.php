<?php

namespace Src\Companies\Commands\GetCompanyDebt;

use App\Company;

final class GetCompanyDebtCommand
{
    /**
     * @var Company
     */
    private $company;

    public function __construct(Company $company)
    {
        $this->company = $company;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }
}
