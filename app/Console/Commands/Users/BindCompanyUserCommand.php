<?php

namespace App\Console\Commands\Users;

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
    protected $signature = 'users:bind-companies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Добавление компаниям связи с пользователем';

    public function handle()
    {
        $users = User::withTrashed()
            ->with([
                'roles',
            ])
            ->whereNull('entity_id')
            ->whereHas('roles', function ($q) {
                $q->where('roles.id', UserRoleEnum::CLIENT);
            })
            ->get();

        $companyMap = Company::withTrashed()
            ->whereIn(DB::raw("concat('0', hash_id)"), $users->pluck('login')->toArray())
            ->get()
            ->keyBy(function (Company $company) {
                return '0' . $company->hash_id;
            });

        $usersWithoutRelatedEntity = [];
        $usersToUpdate = [];
        foreach ($users as $user) {
            $relatedCompany = $companyMap->get($user->login);

            if (!$relatedCompany) {
                $usersWithoutRelatedEntity[] = $user->id;
                continue;
            }

            $usersToUpdate[] = [
                'id' => $user->id,
                'entity_id' => $relatedCompany->id,
            ];
        }

        $this->info('Пользователи без компании ('.count($usersWithoutRelatedEntity).'):');
        $this->warn(implode(', ', $usersWithoutRelatedEntity));

        DB::beginTransaction();

        try {
            foreach ($usersToUpdate as $item) {
                DB::table('users')
                    ->where('id','=', $item['id'])
                    ->update([
                        'entity_id' => $item['entity_id'],
                        'entity_type' => UserEntityType::COMPANY,
                    ]);
            }

            DB::commit();
        } catch (\Exception $exception) {
            DB::rollBack();

            throw $exception;
        }
    }
}
