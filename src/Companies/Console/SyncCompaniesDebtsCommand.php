<?php

namespace Src\Companies\Console;

use App\Company;
use App\Services\OneC\CompanyDebt\CompanyDebtServiceInterface;
use DateTimeImmutable;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Src\Companies\Repositories\CompanyDebtsRepository\CompanyDebtsRepository;
use Throwable;

class SyncCompaniesDebtsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:sync-debts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Синхронизация задолженностей компаний';

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
    public function handle(CompanyDebtServiceInterface $companySyncService, CompanyDebtsRepository $companyDebtsRepository)
    {
        if (! $companySyncService->healthCheck()) {
            $message = 'Сервис интеграции с 1С недоступен!';

            $this->error($message);

            Log::channel('one-c-api')->error($message);

            return;
        }

        $syncDate = new DateTimeImmutable();

        try {
            DB::beginTransaction();

            $debts = $companySyncService->getAll();
            $companiesWithDebtsCount = count($debts);

            $this->info("Получено инфо о задолженности $companiesWithDebtsCount компаний");

            $companyDebtsRepository->resetAllStatuses();

            foreach ($debts as $debt) {
                $company = Company::withTrashed()->where('hash_id', $debt->getHashId())->first();

                if ($company === null) {
                    $message = "Получена неизвестная компания с hash_id {$debt->getHashId()}";

                    Log::channel('one-c-api')->error($message);

                    $this->error($message);

                    continue;
                }

                if (! $debt->hasDebt()) {
                    $this->info(sprintf(
                        'Задолженности у компании в 1С %s нет',
                        $company->getAttribute('name'),
                    ));

                    continue;
                }

                $this->warn(sprintf(
                    'Задолженность у компании в 1С %s, сумма %s',
                    $company->getAttribute('name'),
                    $debt->getDebt(),
                ));

                $companyDebtsRepository->storeDebt($company, $syncDate);
            }

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            $this->error("Ошибка синхронизации задолженностей компаний! {$exception->getMessage()}");
        }
    }
}
