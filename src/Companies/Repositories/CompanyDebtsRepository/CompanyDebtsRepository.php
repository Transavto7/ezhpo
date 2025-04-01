<?php

namespace Src\Companies\Repositories\CompanyDebtsRepository;

use App\Company;
use DateTimeImmutable;
use Illuminate\Support\Facades\DB;
use Src\Companies\Entities\CompanyDebt;

final class CompanyDebtsRepository
{
    public function get(Company $company): CompanyDebt
    {
        $debt = DB::table('company_debts')
            ->where('hash_id', '=', $company->hash_id)
            ->first();

        $relevantOn = $debt
            ? DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $debt->relevant_on)
            : new DateTimeImmutable();

        return new CompanyDebt(
            $relevantOn,
            (bool) $debt
        );
    }

    public function syncDebtStatus(Company $company, bool $hasDebt)
    {
        if ($hasDebt) {
            $this->storeDebt($company, new DateTimeImmutable());

            return;
        }

        DB::table('company_debts')
            ->where('hash_id', '=', $company->hash_id)
            ->delete();
    }

    public function storeDebt(Company $company, DateTimeImmutable $relevantOn)
    {
        DB::table('company_debts')
            ->updateOrInsert(
                ['hash_id' => $company->hash_id],
                ['relevant_on' => $relevantOn]
            );
    }

    public function resetAllStatuses()
    {
        DB::table('company_debts')->truncate();
    }
}
