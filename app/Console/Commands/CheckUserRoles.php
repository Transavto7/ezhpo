<?php

namespace App\Console\Commands;

use App\GenerateHashIdTrait;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CheckUserRoles extends Command
{
    use GenerateHashIdTrait;

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'users:check-roles';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Проверка ролей пользователей';

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
     * @throws Exception
     */
    public function handle()
    {
        $companies = DB::table('companies as c')
            ->select([
                'c.id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin('users as u', function ($join) {
                $join->on('u.login', '=', DB::raw("'0' + c.hash_id"));
            })
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->groupBy(['c.id'])
            ->get()
            ->toArray();

        $companiesWithoutUsers = $this->findEntitiesWithoutUser($companies);
        if (count($companiesWithoutUsers)) {
            $this->info('Компании без пользователя: ' . count($companiesWithoutUsers));
            $this->comment(implode(', ', $companiesWithoutUsers));
            $this->info('');
        }

        $companiesWithoutUsersWithRole = $this->findEntitiesWithoutUserWithRole($companies, 6);
        if (count($companiesWithoutUsersWithRole)) {
            $this->info('Компании без пользователя с ролью client: ' . count($companiesWithoutUsersWithRole));
            $this->comment(implode(', ', $companiesWithoutUsersWithRole));
            $this->info('');
        }

        $companiesWithUsersHavingMultipleRoles = $this->findEntitiesWithUsersHavingMultipleRoles($companies, 6);
        if (count($companiesWithUsersHavingMultipleRoles)) {
            $this->info('Компании с пользователями, у которых несколько ролей (включая роль client): ' . count($companiesWithUsersHavingMultipleRoles));
            $this->comment(implode(', ', $companiesWithUsersHavingMultipleRoles));
            $this->info('');
        }

        $drivers = DB::table('drivers as d')
            ->select([
                'd.id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin('users as u', function ($join) {
                $join->on('u.login', '=', DB::raw("'0' + d.hash_id"));
            })
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->groupBy(['d.id'])
            ->get()
            ->toArray();

        $driversWithoutUsers = $this->findEntitiesWithoutUser($drivers);
        if (count($driversWithoutUsers)) {
            $this->info('Водители без пользователя: ' . count($driversWithoutUsers));
            $this->comment(implode(', ', $driversWithoutUsers));
            $this->info('');
        }

        $driversWithoutUsersWithRole = $this->findEntitiesWithoutUserWithRole($drivers, 3);
        if (count($driversWithoutUsersWithRole)) {
            $this->info('Водители без пользователя с ролью driver: ' . count($driversWithoutUsersWithRole));
            $this->comment(implode(', ', $driversWithoutUsersWithRole));
            $this->info('');
        }

        $driversWithUsersHavingMultipleRoles = $this->findEntitiesWithUsersHavingMultipleRoles($drivers, 3);
        if (count($driversWithUsersHavingMultipleRoles)) {
            $this->info('Водители с пользователями, у которых несколько ролей (включая роль driver): ' . count($driversWithUsersHavingMultipleRoles));
            $this->comment(implode(', ', $driversWithUsersHavingMultipleRoles));
            $this->info('');
        }

        $users = DB::table('users as u')
            ->select([
                'u.id',
                'u.login',
                'c.id as company_id',
                'd.id as driver_id',
                DB::raw('json_arrayagg(mhr.role_id) as role_ids')
            ])
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->leftJoin('companies as c', function ($join) {
                $join->on(DB::raw("'0' + c.hash_id"), '=', 'u.login');
            })
            ->leftJoin('drivers as d', 'd.hash_id', '=', 'u.login')
            ->whereNull('u.deleted_at')
            ->groupBy(['u.id', 'u.login', 'c.hash_id', 'd.hash_id'])
            ->get()
            ->toArray();

        $usersWithoutCompany = $this->findUsersWithoutCompany($users);
        if (count($usersWithoutCompany)) {
            $this->info('Пользователи с ролью client без компании: ' . count($usersWithoutCompany));
            $this->comment(implode(', ', $usersWithoutCompany));
            $this->info('');
        }

        $usersWithoutDriver = $this->findUsersWithoutDriver($users);
        if (count($usersWithoutDriver)) {
            $this->info('Пользователи с ролью driver без водителя: ' . count($usersWithoutDriver));
            $this->comment(implode(', ', $usersWithoutDriver));
        }
    }

    private function findEntitiesWithoutUser(array $entities): array
    {
        return array_reduce($entities, function ($carry, $entity) {
            $roleIds = $this->castJson($entity->role_ids);
            if (!count($roleIds)) {
                $carry[] = $entity->id;
            }
            return $carry;
        }, []);
    }

    private function findEntitiesWithoutUserWithRole(array $entities, int $requiredRole): array
    {
        return array_reduce($entities, function ($carry, $entity) use ($requiredRole) {
            $roleIds = $this->castJson($entity->role_ids);
            if (!in_array($requiredRole, $roleIds) && count($roleIds)) {
                $carry[] = $entity->id;
            }
            return $carry;
        }, []);
    }

    private function findEntitiesWithUsersHavingMultipleRoles(array $entities, int $requiredRole): array
    {
        return array_reduce($entities, function ($carry, $entity) use ($requiredRole) {
            $roleIds = $this->castJson($entity->role_ids);
            if (in_array($requiredRole, $roleIds) && count($roleIds) > 1) {
                $carry[] = $entity->id;
            }
            return $carry;
        }, []);
    }

    private function findUsersWithoutCompany(array $users): array
    {
        return array_reduce($users, function ($carry, $user) {
            $roleIds = $this->castJson($user->role_ids);
            if (in_array(6, $roleIds) && !$user->company_id) {
                $carry[] = $user->id;
            }
            return $carry;
        }, []);
    }

    private function findUsersWithoutDriver(array $users): array
    {
        return array_reduce($users, function ($carry, $user) {
            $roleIds = $this->castJson($user->role_ids);
            if (in_array(3, $roleIds) && !$user->driver_id) {
                $carry[] = $user->id;
            }
            return $carry;
        }, []);
    }

    private function castJson($rawValue): ?array
    {
        if ($rawValue === null) {
            return null;
        }

        $value = json_decode($rawValue, true);

        return array_unique(array_filter($value, function ($item) {
            return $item !== null;
        }));
    }
}
