<?php

namespace App\Services\CompanyReqsChecker;

use App\Company;
use App\ValueObjects\CompanyReqs;
use Illuminate\Database\Eloquent\Builder;

class CompanyRepository
{
    public function findByReqs(CompanyReqs $companyReqs, $excludeId = null, bool $withTrashed = false): ?Company
    {
        /** @var Company|null $company */
        $company = Company::query()
            ->when($withTrashed, function (Builder $query) {
                $query->withTrashed();
            })
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->where('inn', $companyReqs->getInn())
            ->when($companyReqs->getOgrn(), function ($query) use ($companyReqs) {
                $query->where('ogrn', $companyReqs->getOgrn());
            })
            ->when($companyReqs->getKpp(), function ($query) use ($companyReqs) {
                $query->where('kpp', $companyReqs->getKpp());
            })
            ->first();

        return $company;
    }

    public function findById(string $id): Company
    {
        return Company::findOrFail($id);
    }

    public function findByName(string $name, $excludeId = null, bool $withTrashed = false): ?Company
    {
        return Company::query()
            ->when($withTrashed, function (Builder $query) {
                $query->withTrashed();
            })
            ->when($excludeId, function ($query) use ($excludeId) {
                $query->where('id', '!=', $excludeId);
            })
            ->where('name', trim($name ?? ''))
            ->first();
    }
}
