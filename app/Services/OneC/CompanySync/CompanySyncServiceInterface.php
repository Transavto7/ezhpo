<?php

namespace App\Services\OneC\CompanySync;

use App\Company;
use App\Services\OneC\OneCIntegrationServiceInterface;

interface CompanySyncServiceInterface extends OneCIntegrationServiceInterface
{
    public function create(Company $company);

    public function update(Company $company);
}
