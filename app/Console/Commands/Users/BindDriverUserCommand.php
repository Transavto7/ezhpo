<?php

namespace App\Console\Commands\Users;

use App\Driver;
use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class BindDriverUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:bind-drivers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление водителям связи с пользователем';

    public function handle()
    {
        $users = User::withTrashed()
            ->with([
                'roles',
            ])
            ->whereNull('entity_id')
            ->whereHas('roles', function ($q) {
                $q->where('roles.id', UserRoleEnum::DRIVER);
            })
            ->get();

        $driverMap = Driver::withTrashed()
            ->whereIn('hash_id', $users->pluck('login')->toArray())
            ->get()
            ->keyBy(function (Driver $driver) {
                return $driver->hash_id;
            });

        $usersWithoutRelatedEntity = [];
        $usersToUpdate = [];
        foreach ($users as $user) {
            $relatedDriver = $driverMap->get($user->login);

            if (!$relatedDriver) {
                $usersWithoutRelatedEntity[] = $user->id;
                continue;
            }

            $usersToUpdate[] = [
                'id' => $user->id,
                'entity_id' => $relatedDriver->id,
            ];
        }

        $this->info('Пользователи без водителя ('.count($usersWithoutRelatedEntity).'):');
        $this->warn(implode(', ', $usersWithoutRelatedEntity));

        DB::beginTransaction();

        try {
            foreach ($usersToUpdate as $item) {
                DB::table('users')
                    ->where('id','=', $item['id'])
                    ->update([
                        'entity_id' => $item['entity_id'],
                        'entity_type' => UserEntityType::DRIVER,
                    ]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
