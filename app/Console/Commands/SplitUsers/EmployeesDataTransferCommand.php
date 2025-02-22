<?php

namespace App\Console\Commands\SplitUsers;

use App\Employee;
use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class EmployeesDataTransferCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:transfer-employees';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание сущностей сотрудников, добавление связи с пользователем';

    public function handle()
    {
        $transferredCount = 0;

        $users = User::withTrashed()
            ->with([
                'roles',
            ])
            ->whereNull('entity_type')
            ->where(function ($query) {
                $query->whereDoesntHave('roles')
                    ->orWhereHas('roles', function ($q) {
                        $q->whereNotIn('roles.id', [UserRoleEnum::DRIVER, UserRoleEnum::CLIENT, UserRoleEnum::TERMINAL]);
                    });
            })
            ->get();

        $users->each(function (User $user) use (&$transferredCount) {
            DB::beginTransaction();

            try {
                $this->info('Добавление сотрудника: [user_id = '.$user->id.']');
                $employee = Employee::create([
                    'hash_id' => $user->hash_id,
                    'related_user_id' => $user->id,
                    'name' => $user->name,
                    'blocked' => $user->blocked,
                    'pv_id' => $user->pv_id,
                    'timezone' => $user->timezone,
                    'eds' => $user->eds,
                    'validity_eds_start' => $user->validity_eds_start,
                    'validity_eds_end' => $user->validity_eds_end,
                    'auto_created' => 1,
                    'deleted_at' => $user->deleted_at,
                    'deleted_id' => $user->deleted_id,
                ]);

                DB::table('points_to_users')
                    ->where('user_id', '=', $user->id)
                    ->pluck('point_id')
                    ->each(function ($pointId) use ($employee) {
                        DB::table('points_to_employees')
                            ->insert([
                                'employee_id' => $employee->id,
                                'point_id' => $pointId,
                            ]);
                    });
                $this->comment('Добавление записей [points_to_employees]');

                $count = DB::table('trip_tickets')
                    ->where('user_id', '=', $user->id)
                    ->update([
                        'employee_id' => $employee->id,
                    ]);
                $this->comment("Обновлено записей [trip_tickets]: $count");

                DB::table('users')
                    ->where('id', '=', $user->id)
                    ->update(['entity_type' => UserEntityType::EMPLOYEE]);
                $this->comment('Обновлено [users.entity_type]');

                $transferredCount++;

                $this->info("Сотрудник добавлен\n");

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->error("Ошибка при обработке пользователя ID {$user->id}: ".$exception->getMessage()."\n");
            }
        });

        $this->info('Создано сотрудников: '.$transferredCount);
    }
}
