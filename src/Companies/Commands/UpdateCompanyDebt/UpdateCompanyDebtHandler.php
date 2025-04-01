<?php

namespace Src\Companies\Commands\UpdateCompanyDebt;

use Src\Companies\Repositories\CompanyDebtsRepository\CompanyDebtsRepository;

class UpdateCompanyDebtHandler
{
    /**
     * @var CompanyDebtsRepository
     */
    private $companyRepository;

    public function __construct(
        CompanyDebtsRepository $companyRepository
    ) {
        $this->companyRepository = $companyRepository;
    }

    public function handle(UpdateCompanyDebtCommand $command)
    {
        $this->companyRepository->syncDebtStatus($command->getCompany(), $command->hasDebt());
    }
}
