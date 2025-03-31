<?php

namespace Src\Companies\Commands\GetCompanyDebt;

use Src\Companies\Entities\CompanyOneCDebt;
use Src\Companies\Repositories\CompanyDebtsRepository\CompanyDebtsRepository;
use Src\Companies\Repositories\GetCompanyDebtOneCRepository\GetCompanyDebtOneCRepository;

final class GetCompanyDebtHandler
{
    /**
     * @var GetCompanyDebtOneCRepository
     */
    private $debtRepository;

    /**
     * @var CompanyDebtsRepository
     */
    private $companyRepository;

    public function __construct(
        GetCompanyDebtOneCRepository $debtRepository,
        CompanyDebtsRepository $companyRepository
    ) {
        $this->debtRepository = $debtRepository;
        $this->companyRepository = $companyRepository;
    }

    public function handle(GetCompanyDebtCommand $command): ?CompanyOneCDebt
    {
        $debt = $this->debtRepository->get($command->getCompany());

        if ($debt) {
            $this->companyRepository->syncDebtStatus($command->getCompany(), $debt->isHasDebt());
        }

        return $debt;
    }
}
