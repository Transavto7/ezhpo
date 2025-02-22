<?php

namespace App\Console\Commands\SplitUsers;

use App\Company;
use App\Enums\UserEntityType;
use App\Enums\UserRoleEnum;
use App\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

final class BindCompanyUserCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'split-users:bind-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление компаниям связи с пользователем';

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
                $q->where('roles.id', UserRoleEnum::CLIENT);
            })
            ->orderBy('id')
            ->chunkById($chunkSize, function ($users) use(&$allIds) {
                $companyMap = Company::withTrashed()
                    ->whereNull('related_user_id')
                    ->whereIn(DB::raw("concat('0', hash_id)"), $users->pluck('login')->toArray())
                    ->get()
                    ->keyBy(function (Company $company) {
                        return '0' . $company->hash_id;
                    });

                $usersWithoutRelatedEntity = [];
                $companiesToUpdate = [];
                $usersToUpdate = [];
                foreach ($users as $user) {
                    $relatedCompany = $companyMap->get($user->login);

                    if (!$relatedCompany) {
                        $usersWithoutRelatedEntity[] = $user->id;
                        continue;
                    }

                    $usersToUpdate[] = $user->id;

                    $companiesToUpdate[] = [
                        'id' => $relatedCompany->id,
                        'related_user_id' => $user->id,
                    ];
                }

                DB::beginTransaction();

                try {
                    DB::table('users')
                        ->whereIn('id', $usersToUpdate)
                        ->update(['entity_type' => UserEntityType::COMPANY]);

                    foreach ($companiesToUpdate as $item) {
                        DB::table('companies')
                            ->where('id','=', $item['id'])
                            ->update([
                                'related_user_id' => $item['related_user_id'],
                            ]);
                    }

                    $this->info("Обработано компаний: " . count($users));
                    $allIds = array_merge($allIds, $usersWithoutRelatedEntity);

                    DB::commit();
                } catch (\Exception $exception) {
                    DB::rollBack();

                    throw $exception;
                }
            });

        if (count($allIds)) {
            $this->warn("\nПользователей без компании всего: " . count($allIds));
            $this->warn(implode(', ', $allIds));
        } else {
            $this->info("\nВсе пользователи успешно связаны с компаниями");
        }
    }
}
