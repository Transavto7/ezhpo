<?php

namespace App\Actions\Element\Remove;

use App\Company;
use App\Exceptions\EntityAlreadyExistException;
use App\ValueObjects\CompanyReqs;
use Exception;

class RemoveCompanyHandler implements RemoveElementHandlerInterface
{
    /**
     * @throws Exception
     */
    public function handle($id, bool $deleting)
    {
        if ($deleting) {
            $this->delete($id);
        } else {
            $this->restore($id);
        }
    }

    /**
     * @throws Exception
     */
    private function delete($id)
    {
        $existModel = Company::query()->find($id);
        if (!$existModel) {
            throw new Exception("Компания с ID $id не найдена");
        }

        $existModel->delete();
    }

    /**
     * @throws Exception
     */
    private function restore($id)
    {
        $existModel = Company::onlyTrashed()->find($id);
        if (!$existModel) {
            throw new Exception("Компания с ID $id не найдена в корзине");
        }

        $this->checkDuplicates($existModel);

        $existModel->restore();
    }

    /**
     * @throws EntityAlreadyExistException
     */
    private function checkDuplicates(Company $company)
    {
        $this->checkDuplicateByName($company);
        $this->checkDuplicateByReqs($company);
    }

    /**
     * @throws EntityAlreadyExistException
     */
    private function checkDuplicateByName(Company $company)
    {
        $existItem = Company::query()
            ->where('name', $company->getAttribute('name'))
            ->first();

        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат по названию компании');
        }
    }

    /**
     * @throws EntityAlreadyExistException
     */
    private function checkDuplicateByReqs(Company $company)
    {
        $companyReqs = new CompanyReqs(
            $company->getAttribute('inn'),
            $company->getAttribute('kpp')
        );

        $existItem = Company::query()
            ->where('inn', $companyReqs->getInn())
            ->when($companyReqs->isOrganizationInnFormat(), function ($query) use ($companyReqs) {
                $query->where('kpp', $companyReqs->getKpp());
            })
            ->first();

        if ($existItem) {
            throw new EntityAlreadyExistException('Найден дубликат компании по ИНН (+КПП)');
        }
    }
}
