<?php

namespace Src\Companies\Commands\GetCompanyDebt;

use App\Services\OneC\CompanyDebt\CompanyDebtServiceInterface;
use Src\Companies\Entities\CompanyOneCDebt;
use Src\Companies\Repositories\CompanyDebtsRepository\CompanyDebtsRepository;

final class GetCompanyDebtHandler
{
    /**
     * @var CompanyDebtServiceInterface
     */
    private $companyDebtService;

    /**
     * @var CompanyDebtsRepository
     */
    private $companyRepository;

    public function __construct(
        CompanyDebtServiceInterface $companyDebtService,
        CompanyDebtsRepository $companyRepository
    ) {
        $this->companyDebtService = $companyDebtService;
        $this->companyRepository = $companyRepository;
    }

    public function handle(GetCompanyDebtCommand $command): ?CompanyOneCDebt
    {
        $debt = $this->companyDebtService->get($command->getCompany());

        $this->companyRepository->syncDebtStatus($command->getCompany(), $debt->hasDebt());

        return $debt;
    }
}
