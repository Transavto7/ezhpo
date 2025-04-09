<?php

namespace App\Services\FindSimilarElement\FindSimilarCompany;

use App\Exceptions\CompanyWithSameINNAlreadyExist;
use App\Exceptions\CompanyWithSameINNAlreadyExistInTrash;
use App\Exceptions\CompanyWithSameNameAlreadyExist;
use App\Exceptions\CompanyWithSameNameAlreadyExistInTrash;
use App\Services\CompanyReqsChecker\CompanyRepository;
use App\ValueObjects\CompanyReqs;

final class FindSimilarCompanyHandler
{
    /**
     * @var CompanyRepository
     */
    private $repository;

    public function __construct(CompanyRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(FindSimilarCompanyAction $action)
    {
        $company = $this->repository->findByName(
            $action->getName(),
            $action->getExcludeId(),
            $action->withTrashed()
        );

        if ($company && ! $company->trashed()) {
            throw new CompanyWithSameNameAlreadyExist();
        }
        if ($company && $company->trashed()) {
            throw new CompanyWithSameNameAlreadyExistInTrash();
        }

        $companyReqs = new CompanyReqs(
            $action->getInn() ?? '',
                $action->getKpp() ?? '',
                $action->getOgrn() ?? ''
        );
        $company = $this->repository->findByReqs(
            $companyReqs,
            $action->getExcludeId(),
            $action->withTrashed()
        );

        if ($company && ! $company->trashed()) {
            throw new CompanyWithSameINNAlreadyExist();
        }
        if ($company && $company->trashed()) {
            throw new CompanyWithSameINNAlreadyExistInTrash();
        }
    }
}
