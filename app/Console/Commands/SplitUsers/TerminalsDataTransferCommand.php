<?php

namespace App\Console\Commands\SplitUsers;

use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\Models\Forms\MedicForm;
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
    protected $signature = 'split-users:transfer-terminals';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Создание сущностей терминалов, добавление связи с пользователем, обновление связанных таблиц';

    public function handle()
    {
        $transferredCount = 0;

        $terminalIds = MedicForm::query()
            ->whereNotNull('terminal_id')
            ->distinct()
            ->pluck('terminal_id')
            ->toArray();

        $users = User::withTrashed()
            ->with([
                'roles',
            ])
            ->whereNull('entity_type')
            ->where(function ($query) use ($terminalIds) {
                $query->whereIn('id', $terminalIds)
                    ->orWhereHas('roles', function ($q) {
                        $q->where('roles.id', UserRoleEnum::TERMINAL);
                    });
            })
            ->get();

        $users->each(function (User $user) use (&$transferredCount) {
            DB::beginTransaction();

            try {
                $this->info('Добавление терминала: [user_id = '.$user->id.']');
                $terminal = Terminal::create([
                    'hash_id' => $user->hash_id,
                    'related_user_id' => $user->id,
                    'name' => $user->name,
                    'timezone' => $user->timezone,
                    'blocked' => $user->blocked,
                    'pv_id' => $user->pv_id,
                    'stamp_id' => $user->stamp_id,
                    'company_id' => $user->company_id,
                    'last_connection_at' => $user->last_connection_at,
                    'auto_created' => 1,
                    'deleted_at' => $user->deleted_at,
                    'deleted_id' => $user->deleted_id,
                ]);

                $count = DB::table('terminal_devices')
                    ->where('user_id', '=', $user->id)
                    ->update([
                        'terminal_id' => $terminal->id,
                    ]);
                $this->comment("Обновлено записей [terminal_devices]: $count");

                $count = DB::table('terminal_checks')
                    ->where('user_id', '=', $user->id)
                    ->update([
                        'terminal_id' => $terminal->id,
                    ]);
                $this->comment("Обновлено записей [terminal_checks]: $count");

                $count = DB::table('terminal_settings')
                    ->where('terminal_id', '=', $user->id)
                    ->update([
                        'terminal_id' => $terminal->id,
                    ]);
                $this->comment("Обновлено записей [terminal_settings]: $count");

                DB::table('users')
                    ->where('id', '=', $user->id)
                    ->update(['entity_type' => UserEntityType::TERMINAL]);
                $this->comment('Обновлено [users.entity_type]');

                $transferredCount++;

                $this->info("Терминал добавлен\n");

                DB::commit();
            } catch (\Exception $exception) {
                DB::rollBack();
                $this->error("Ошибка при обработке пользователя ID {$user->id}: ".$exception->getMessage()."\n");
            }
        });

        $this->info('Создано терминалов: '.$transferredCount);
    }
}
