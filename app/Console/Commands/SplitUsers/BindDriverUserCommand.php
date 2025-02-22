<?php

namespace App\Console\Commands\SplitUsers;

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
    protected $signature = 'split-users:bind-drivers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление водителям связи с пользователем';

    public function handle()
    {
        $chunkSize = 15000;
        $allIds = [];

        User::withTrashed()
            ->with([
                'roles',
            ])
            ->whereNull('entity_type')
            ->whereHas('roles', function ($q) {
                $q->where('roles.id', UserRoleEnum::DRIVER);
            })
            ->orderBy('id')
            ->chunkById($chunkSize, function ($users) use(&$allIds) {
                $driverMap = Driver::withTrashed()
                    ->whereNull('related_user_id')
                    ->whereIn('hash_id', $users->pluck('login')->toArray())
                    ->get()
                    ->keyBy(function (Driver $driver) {
                        return $driver->hash_id;
                    });

                $usersWithoutRelatedEntity = [];
                $driversToUpdate = [];
                $usersToUpdate = [];
                foreach ($users as $user) {
                    $relatedDriver = $driverMap->get($user->login);

                    if (!$relatedDriver) {
                        $usersWithoutRelatedEntity[] = $user->id;
                        continue;
                    }

                    $usersToUpdate[] = $user->id;

                    $driversToUpdate[] = [
                        'id' => $relatedDriver->id,
                        'related_user_id' => $user->id,
                    ];
                }

                DB::beginTransaction();

                try {
                    DB::table('users')
                        ->whereIn('id', $usersToUpdate)
                        ->update(['entity_type' => UserEntityType::DRIVER]);

                    foreach ($driversToUpdate as $item) {
                        DB::table('drivers')
                            ->where('id','=', $item['id'])
                            ->update([
                                'related_user_id' => $item['related_user_id'],
                            ]);
                    }

                    $this->info("Обработано водителей: " . count($users));
                    $allIds = array_merge($allIds, $usersWithoutRelatedEntity);

                    DB::commit();
                } catch (\Exception $exception) {
                    DB::rollBack();

                    throw $exception;
                }
            });

        if (count($allIds)) {
            $this->warn("\nПользователей без водителя всего: " . count($allIds));
            $this->warn(implode(', ', $allIds));
        } else {
            $this->info("\nВсе пользователи успешно связаны с водителями");
        }
    }
}
