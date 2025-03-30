<?php

namespace Src\Companies\Commands\CheckForCompanyDebts;

use App\Company;

final class CheckForCompanyDebtsCommand
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
