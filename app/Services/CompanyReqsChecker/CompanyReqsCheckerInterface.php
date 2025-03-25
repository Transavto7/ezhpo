<?php

namespace App\Services\CompanyReqsChecker;

use App\ValueObjects\CompanyReqs;

interface CompanyReqsCheckerInterface
{
    /**
     * @param CompanyReqs $companyReqs
     * @return bool
     */
    public function check(CompanyReqs $companyReqs): bool;

    /**
     * @param CompanyReqs $companyReqs
     * @return CompanyInfo|null
     */
    public function restoreCompany(CompanyReqs $companyReqs): ?CompanyInfo;
}
