<?php

namespace App\Console\Commands\Companies;

use App\Company;
use App\Enums\OneCSyncStatusEnum;
use App\Services\OneC\CompanySync\CompanySyncServiceInterface;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Throwable;

class CreateCompaniesInOneC extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:create-in-one-c
                            {--force : Подтверждение создания}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание компаний с корректными реквизитами в 1С';

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
            $this->error('Для подтверждения создания - выполните команду с флагом --force! Проверьте, что работаете с тестовым доменом или валидными данными!');

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
                'inn',
                'kpp',
                'reqs_validated',
                'one_c_synced',
                'address',
                'ogrn'
            ])
            ->where('reqs_validated', true)
            ->where('one_c_synced', OneCSyncStatusEnum::NON_CREATED)
            ->get();

        foreach ($companies as $company) {
            try {
                $companySyncService->create($company);

                $company->setAttribute('one_c_synced', OneCSyncStatusEnum::SYNCED);
                $company->save();

                $this->info(sprintf(
                    "Создание компании в 1С %s (%s) ИНН: %s, КПП: %s, ОГРН: %s, адрес: %s",
                    $company->getAttribute('name'),
                    $company->getAttribute('hash_id'),
                    $company->getAttribute('inn'),
                    $company->getAttribute('kpp'),
                    $company->getAttribute('ogrn'),
                    $company->getAttribute('address')
                ));
            } catch (Throwable $exception) {
                $message = sprintf(
                    "Ошибка создания компании в 1С %s (%s) ИНН: %s, КПП: %s, ОГРН: %s, адрес: %s. %s",
                    $company->getAttribute('name'),
                    $company->getAttribute('hash_id'),
                    $company->getAttribute('inn'),
                    $company->getAttribute('kpp'),
                    $company->getAttribute('ogrn'),
                    $company->getAttribute('address'),
                    $exception->getMessage()
                );

                $this->error($message);

                Log::info($message);
            }
        }
    }
}
