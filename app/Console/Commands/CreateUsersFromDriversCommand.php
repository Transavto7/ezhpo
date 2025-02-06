<?php

namespace App\Console\Commands;

use App\Company;
use App\Enums\UserEntityType;
use App\GenerateHashIdTrait;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Throwable;

class CreateUsersFromDriversCommand extends Command
{
    use GenerateHashIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:create-from-drivers
                            {--save : Добавить учетную запись}
                            {--show : Показать ошибки}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание пользователей из водителей';

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
        $save = $this->option('save');
        $showErrors = $this->option('show');

        $drivers = DB::select('select * from drivers WHERE hash_id NOT IN (SELECT login FROM users) AND deleted_at is null');

        $this->info("Всего водителей без учетных записей пользователей - " . count($drivers));

        $created = 0;

        foreach ($drivers as $driver) {
            try {
                $company = $driver->company_id ? Company::where('id', $driver->company_id)->first() : null;

                $pvId = 0;
                $companyHashId = 0;

                if ($company) {
                    $pvId = $pvId->pv_id ?? 0;
                    $companyHashId = $company->hash_id;
                }

                $validator = function (int $hashId) {
                    if (User::where('hash_id', $hashId)->first()) {
                        return false;
                    }

                    return true;
                };

                $userHashId = $this->generateHashId(
                    $validator,
                    config('app.hash_generator.user.min'),
                    config('app.hash_generator.user.max'),
                    config('app.hash_generator.user.tries')
                );

                $password = Hash::make($driver->hash_id);
                $data = [
                    'entity_id' => $driver->id,
                    'entity_type' => UserEntityType::driver(),
                    'hash_id' => $userHashId,
                    'login' => $driver->hash_id,
                    'password' => $password,
                    'api_token' => Hash::make(date('H:i:s') . sha1($driver->hash_id)),
                    'name' => $driver->fio,
                    'pv_id' => $pvId,
                    'role' => 3,
                    'email' => $companyHashId . '-' . $userHashId . '@ta-7.ru',
                    'company_id' => $company ? $company->id : 0
                ];

                if (!$save) continue;

                User::create($data);

                $created++;
            } catch (Throwable $exception) {
                $this->error('Ошибка создания пользователя для водителя: ' . $driver->hash_id);

                if (!$showErrors) continue;

                $this->error($exception->getMessage());
            }
        }

        $this->info("Создано учетных записей - $created");
    }
}
