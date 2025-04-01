<?php

namespace App\Services\OneC\CompanyDebt;

use App\Company;
use App\Services\OneC\OneCIntegrationServiceInterface;
use Src\Companies\Entities\CompanyOneCDebt;
use Src\Companies\Entities\CompanyOneCShortDebt;

interface CompanyDebtServiceInterface extends OneCIntegrationServiceInterface
{
    /**
     * @param Company $company
     * @return CompanyOneCDebt|null
     */
    public function get(Company $company): CompanyOneCDebt;

    /**
     * @return CompanyOneCShortDebt[]
     */
    public function getAll(): array;
}
