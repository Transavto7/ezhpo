<?php

namespace App\Console\Commands\SplitUsers;

use App\Company;
use App\Employee;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class FillResponsibleInCompaniesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:fill-responsible-in-companies
                            {--default_employee= : HASH_ID сотрудника по умолчанию}
                            {--companies= : HASH_ID компаний (через запятую без пробелов), для которых будет установлен сотрудник по умолчанию, если не был найден соотв. сотрудник по user_id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Обновление ID ответственного у компаний';

    /**
     * Execute the console command.
     * @throws \Exception
     */
    public function handle()
    {
        DB::beginTransaction();

        $unsuccessfulItems = [];

        $fixMode = false;
        $defaultEmployee = null;

        $defaultEmployeeHashId = $this->option('default_employee');
        $companyHashIds = $this->option('companies');

        if ($companyHashIds) {
            if (! $defaultEmployeeHashId) {
                $this->error('Параметр --default_employee является обязательным при указании параметра --companies');

                return;
            }

            $companyHashIds = explode(',', $companyHashIds);
        } else {
            $companyHashIds = [];
        }

        if ($defaultEmployeeHashId) {
            $fixMode = true;

            $defaultEmployee = Employee::withTrashed()->where('hash_id', $defaultEmployeeHashId)->first();
            if (! $defaultEmployee) {
                throw new \Exception("Сотрудник с hash_id = $defaultEmployeeHashId не найден");
            }
        }

        try {
            Company::withTrashed()
                ->whereNotNull('user_id')
                ->whereNull('responsible_id')
                ->orderBy('user_id')
                ->get()
                ->each(function (Company $company) use (&$unsuccessfulItems, $fixMode, $defaultEmployee, $companyHashIds) {
                    $userId = $company->user_id;

                    $employee = Employee::withTrashed()->where('related_user_id', '=', $userId)->first();

                    if (! $employee) {
                        if ($fixMode && (in_array($company->hash_id, $companyHashIds) || count($companyHashIds) === 0)) {
                            $this->info("Исправление ответственного компании hash_id = {$company->hash_id}");
                            DB::table('companies')
                                ->where('id', $company->id)
                                ->update([
                                    'responsible_id' => $defaultEmployee->id,
                                ]);

                            return;
                        }

                        $unsuccessfulItems[] = [
                            'company_hash_id' => $company->hash_id,
                            'user_id' => $userId,
                        ];

                        return;
                    }

                    DB::table('companies')
                        ->where('id', $company->id)
                        ->update([
                            'responsible_id' => $employee->id,
                        ]);

                    $this->info('['.$company->hash_id.']: замена пользователя id='.$userId.' на сотрудника id='.$employee->id);
                });

            if (count($unsuccessfulItems)) {
                $this->warn("\nКомпании, у которых не удалось заменить ID ответственного:");

                $map = array_reduce($unsuccessfulItems, function (array $carry, array $item) {
                    $userId = $item['user_id'];
                    $companyHashId = $item['company_hash_id'];

                    if (! array_key_exists($userId, $carry)) {
                        $carry[$userId] = [];
                    }

                    $carry[$userId][] = $companyHashId;

                    return $carry;
                }, []);

                foreach ($map as $userId => $companyIds) {
                    $this->warn("ID ответственного: $userId, HASH_ID компаний:");
                    $this->line(implode(',', $companyIds)."\n");
                }
            }

            DB::commit();
        } catch (\Exception $exception) {
            $this->error($exception->getMessage());
            DB::rollBack();
            throw $exception;
        }
    }
}
