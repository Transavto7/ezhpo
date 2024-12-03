<?php

namespace App\Console\Commands\Companies;

use App\Company;
use App\ValueObjects\Phone;
use Illuminate\Console\Command;

class ValidateCompaniesPhoneNumberCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'companies:validate-phones
                            {--update : Обновить валидные номера}
                            {--show : Показать валидные номера}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Валидация и обновление номеров компаний для интеграции с SMS API';

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
    public function handle()
    {
        $updateAfterValidate = $this->option('update');
        $showValid = $this->option('show');

        $companies = Company::query()
            ->select([
                'id',
                'hash_id',
                'where_call'
            ])
            ->whereNotNull('where_call')
            ->get();

        $updatedCount = 0;
        $invalidCount = 0;

        /** @var Company $company */
        foreach ($companies as $company) {
            $nativePhone = $company->getAttribute('where_call');
            $phone = new Phone($nativePhone);

            if (!$phone->isValid()) {
                $invalidCount++;

                $message = sprintf(
                    "%s: %s",
                    $company->getAttribute('hash_id'),
                    $nativePhone
                );

                $this->error($message);
            }

            if ($showValid && $phone->isValid()) {
                $message = sprintf(
                    "%s: %s => %s",
                    $company->getAttribute('hash_id'),
                    $nativePhone,
                    $phone
                );

                $this->info($message);
            }

            if ($updateAfterValidate && $phone->isValid() && !$phone->isSanitized()) {
                $company->setAttribute('where_call', $phone);
                $company->save();

                $updatedCount++;
            }
        }

        $this->info("Компаний с номерами мобильных телефонов = " . $companies->count());
        $this->info("Компаний с обновленными номерами = " . $updatedCount);
        $this->warn("Компаний с невалидными номерами = " . $invalidCount);
    }
}
