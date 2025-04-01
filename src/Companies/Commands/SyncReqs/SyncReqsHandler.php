<?php

namespace Src\Companies\Commands\SyncReqs;

use App\Company;
use App\Enums\OneCSyncStatusEnum;
use App\Exceptions\EntityAlreadyExistException;
use App\Services\CompanyReqsChecker\CompanyRepository;
use App\Services\CompanyReqsChecker\CompanyReqsCheckerInterface;
use App\Services\OneC\CompanySync\CompanySyncServiceInterface;
use App\ValueObjects\CompanyReqs;
use Exception;
use Throwable;

class SyncReqsHandler
{
    /**
     * @var CompanyReqsCheckerInterface
     */
    private $checker;

    /**
     * @var CompanyRepository
     */
    private $companyRepository;

    /**
     * @var CompanySyncServiceInterface
     */
    private $companySyncService;

    public function __construct(
        CompanyReqsCheckerInterface $checker,
        CompanyRepository $companyRepository,
        CompanySyncServiceInterface $companySyncService
    ) {
        $this->checker = $checker;
        $this->companyRepository = $companyRepository;
        $this->companySyncService = $companySyncService;
    }

    /**
     * @throws Exception
     */
    public function handle(SyncReqsCommand $command)
    {
        $company = Company::find($command->getCompanyId());
        if ($company === null) {
            throw new Exception('Компания не найдена.');
        }

        /** @var Company $company */
        $companyReqs = new CompanyReqs(
            $company->getAttribute('inn') ?? '',
            $company->getAttribute('kpp') ?? '',
            $company->getAttribute('ogrn') ?? ''
        );

        if ($companyReqs->isPersonalToRestore()) {
            $companyInfo = $this->checker->restoreCompany($companyReqs);

            if ($companyInfo === null) {
                throw new Exception('Ошибка получения реквизитов компании!');
            }

            $company->setAttribute('ogrn', $companyInfo->getOgrn());
            $company->setAttribute('address', $companyInfo->getAddress());
        } elseif ($companyReqs->isOrganizationToRestore()) {
            $companyInfo = $this->checker->restoreCompany($companyReqs);

            if ($companyInfo === null) {
                throw new Exception('Ошибка получения реквизитов компании!');
            }

            $company->setAttribute('official_name', $companyInfo->getOfficialName());
            $company->setAttribute('kpp', $companyInfo->getKpp());
            $company->setAttribute('ogrn', $companyInfo->getOgrn());
            $company->setAttribute('address', $companyInfo->getAddress());
        } else {
            throw new Exception('Невалидные базовые реквизиты для восстановления!');
        }

        $company->setAttribute('inn', $companyReqs->getInn());

        $existItem = $this->companyRepository->findByReqs($companyReqs, $command->getCompanyId());
        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат компании по ИНН (+КПП)');
        }

        $company->setAttribute('reqs_validated', true);

        if ($company->getAttribute('one_c_synced') === OneCSyncStatusEnum::NON_CREATED) {
            $this->createCompanyInOneC($company);
        } else {
            $this->updateCompanyInOneC($company);
        }

        $company->save();
    }

    protected function createCompanyInOneC(Company $company)
    {
        try {
            $this->companySyncService->create($company);

            $company->setAttribute('one_c_synced', OneCSyncStatusEnum::SYNCED);
        } catch (Throwable $exception) {
        }
    }

    protected function updateCompanyInOneC(Company $company)
    {
        try {
            $this->companySyncService->update($company);

            $company->setAttribute('one_c_synced', OneCSyncStatusEnum::SYNCED);
        } catch (Throwable $exception) {
            $company->setAttribute('one_c_synced', OneCSyncStatusEnum::NEED_UPDATE);
        }
    }
}
