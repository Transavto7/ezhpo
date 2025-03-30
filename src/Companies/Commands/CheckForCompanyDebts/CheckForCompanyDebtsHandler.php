<?php

namespace Src\Companies\Commands\CheckForCompanyDebts;

use Src\Companies\Entities\CompanyDebt;
use Src\Companies\Repositories\CompanyDebtsRepository\CompanyDebtsRepository;

final class CheckForCompanyDebtsHandler
{
    /**
     * @var CompanyDebtsRepository
     */
    private $repository;

    public function __construct(CompanyDebtsRepository $repository)
    {
        $this->repository = $repository;
    }

    public function handle(CheckForCompanyDebtsCommand $action): CompanyDebt
    {
        return $this->repository->get($action->getCompany());
    }
}
