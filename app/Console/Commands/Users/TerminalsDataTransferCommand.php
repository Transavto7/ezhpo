<?php

namespace App\Console\Commands\Users;

use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\Terminal;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class TerminalsDataTransferCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:transfer-terminals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание сущностей терминалов, добавление связи с пользователем';

    public function handle()
    {
        $users = User::withTrashed()
            ->with([
                'roles',
            ])
            ->whereNull('entity_id')
            ->whereHas('roles', function ($q) {
                $q->where('roles.id', UserRoleEnum::TERMINAL);
            })
            ->get();

        DB::beginTransaction();

        try {
            $users->each(function (User $user) {
                if ($user->entity_type || $user->entity_id) {
                    return;
                }

                $item = [
                    'blocked' => $user->blocked,
                    'pv_id' => $user->pv_id,
                    'last_connection_at' > $user,
                    'auto_created' => $user->auto_created,
                    'deleted_at' => $user->deleted_at,
                    'deleted_id' => $user->deleted_id,
                ];

                $employee = Terminal::create($item);

                $user->entity_id = $employee->id;
                $user->entity_type = UserEntityType::TERMINAL;
                $user->save();
            });

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
