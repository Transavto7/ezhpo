<?php

namespace App\Console\Commands\Companies;

use App\Company;
use App\Services\CompanyReqsChecker\CompanyReqsCheckerInterface;
use App\ValueObjects\CompanyReqs;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ValidateCompaniesReqs extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:validate-reqs
                            {--force : Подтверждение обновления}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Валидация и обновление ИНН и КПП компаний';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle(CompanyReqsCheckerInterface $companyReqsChecker)
    {
        if (!$this->option('force')) {
            $this->error('Для подтверждения восстановления данных - выполните команду с флагом --force! Проверьте, что есть лимиты в сервисе DaData!');

            return;
        }

        $companies = Company::withTrashed()
            ->select([
                'id',
                'hash_id',
                'name',
                'inn',
                'kpp',
                'reqs_validated'
            ])
            ->where('reqs_validated', false)
            ->get();

        $uniqueInn = [];

        foreach ($companies as $company) {
            $inn = $company->getAttribute('inn') ?? '';
            $kpp = $company->getAttribute('kpp') ?? '';
            $innWithKpp = $inn . $kpp;

            if (isset($uniqueInn[$innWithKpp])) {
                continue;
            }
            $uniqueInn[$innWithKpp] = true;

            $companyReqs = new CompanyReqs($inn, $kpp);

            if ($companyReqs->isPersonalInnFormat()) {
                $company->setAttribute('inn', $companyReqs->getInn());
                $company->setAttribute('reqs_validated', true);
                $company->save();
                continue;
            }

            if ($companyReqs->isOrganizationInnFormat()) {
                $restoredCompanyReqs = $companyReqsChecker->restoreOrganization($companyReqs);

                if ($restoredCompanyReqs === null) {
                    continue;
                }

                $company->setAttribute('kpp', $restoredCompanyReqs->getKpp());
                $company->setAttribute('inn', $restoredCompanyReqs->getInn());
                $company->setAttribute('reqs_validated', true);
                $company->save();

                $this->log(sprintf(
                    "Восстановление реквизитов %s (%s) ИНН: %s, КПП: %s",
                    $company->getAttribute('name'),
                    $company->getAttribute('hash_id'),
                    $company->getAttribute('inn'),
                    $company->getAttribute('kpp')
                ));
            }
        }
    }

    private function log(string $message)
    {
        $this->info($message);
        Log::info($message);
    }
}
