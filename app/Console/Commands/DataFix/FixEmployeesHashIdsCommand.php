<?php

namespace App\Console\Commands\DataFix;

use App\Employee;
use App\Enums\LogActionTypesEnum;
use App\Log;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class FixEmployeesHashIdsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'data-fix:fix-employees-hash-ids';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Заполнение HASH_ID сотрудников нулями справа';

    public function handle()
    {
        $user = User::where('login', '=', User::DEFAULT_USER_LOGIN)->first();

        if (! $user) {
            $this->error('Не найден пользователь администратор');
        }

        DB::beginTransaction();

        Employee::withTrashed()
            ->whereRaw('length(hash_id) < 6')
            ->each(function (Employee $employee) use ($user) {
                try {
                    $employee->timestamps = false;

                    $newHashId = str_pad($employee->hash_id, 6, '0');

                    $logData = [];
                    $logData[] = [
                        'name' => 'hash_id',
                        'oldValue' => $employee->hash_id,
                        'newValue' => $newHashId,
                    ];

                    $this->comment("Обновление сотрудника [HASH_ID]: {$employee->hash_id} -> {$newHashId}");

                    Employee::withoutEvents(function () use ($employee, $newHashId) {
                        $employee->hash_id = $newHashId;
                        $employee->save();
                    });

                    $log = Log::create([
                        'user_id' => $user->id,
                        'type' => LogActionTypesEnum::CORRECTION,
                    ]);

                    $log->setAttribute('data', $logData);
                    $log->model()->associate($employee);

                    $log->save();
                } catch (\Exception $exception) {
                    DB::rollBack();
                    $this->error($exception->getMessage());
                    throw $exception;
                }
            });

        DB::commit();
        $this->info('Успешно завершено');
    }
}
