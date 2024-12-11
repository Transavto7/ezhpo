<?php

namespace App\Console\Commands\Companies;

use App\Company;
use App\Enums\OneCSyncStatusEnum;
use App\Services\OneC\CompanySync\CompanySyncServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class UpdateCompaniesInOneC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:update-in-one-c
                            {--force : Подтверждение обновления}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление компаний, синхронизированных с 1С';

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
    public function handle(CompanySyncServiceInterface $companySyncService)
    {
        if (!$this->option('force')) {
            $this->error('Для подтверждения обновления - выполните команду с флагом --force! Проверьте, что работаете с тестовым доменом или валидными данными!');

            return;
        }

        if (!$companySyncService->healthCheck()) {
            $this->error('Сервис интеграции с 1С недоступен!');

            return;
        }

        $companies = Company::query()
            ->select([
                'id',
                'hash_id',
                'name',
                'official_name',
                'reqs_validated',
                'one_c_synced'
            ])
            ->where('one_c_synced', OneCSyncStatusEnum::NEED_UPDATE)
            ->get();

        foreach ($companies as $company) {
            try {
                $companySyncService->update($company);

                $company->setAttribute('one_c_synced', OneCSyncStatusEnum::SYNCED);
                $company->save();

                $this->info(sprintf(
                    "Обновление компании в 1С %s (%s), полное наименование: %s",
                    $company->getAttribute('name'),
                    $company->getAttribute('hash_id'),
                    $company->getAttribute('official_name')
                ));
            } catch (Throwable $exception) {
                $message = sprintf(
                    "Ошибка обновления компании в 1С %s (%s), полное наименование: %s. %s",
                    $company->getAttribute('name'),
                    $company->getAttribute('hash_id'),
                    $company->getAttribute('official_name'),
                    $exception->getMessage()
                );

                $this->error($message);

                Log::info($message);
            }
        }
    }
}
