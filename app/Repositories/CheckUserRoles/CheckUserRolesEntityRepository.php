<?php

namespace App\Repositories\CheckUserRoles;

use App\User;
use Illuminate\Support\Facades\DB;

final class CheckUserRolesEntityRepository
{
    public function findUsersWithDuplicatedRoles()
    {
        return DB::table('users as u')
            ->select('u.id')
            ->join('model_has_roles as mhr', 'u.id', '=', 'mhr.model_id')
            ->groupBy(['u.id', 'mhr.role_id'])
            ->havingRaw('COUNT(*) > 1')
            ->get()
            ->toArray();
    }

    public function findCompanies(): array
    {
        return DB::table('companies as c')
            ->select([
                'c.id',
                'c.hash_id',
                'c.name',
                'u.id as user_id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin(
                DB::raw("(select * from users where deleted_at is null) as u"),
                function ($join) {
                    $join->on('u.login', '=', DB::raw("CONCAT('0', c.hash_id)"));
                }
            )
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->whereNull('c.deleted_at')
            ->groupBy(['c.id', 'c.hash_id', 'c.name', 'u.id'])
            ->get()
            ->toArray();
    }

    public function findDrivers(): array
    {
        return DB::table('drivers as d')
            ->select([
                'd.id',
                'd.hash_id',
                'd.company_id',
                'd.fio',
                'u.id as user_id',
                DB::raw("json_arrayagg(mhr.role_id) as role_ids"),
            ])
            ->leftJoin(
                DB::raw("(select * from users where deleted_at is null) as u"),
                'u.login',
                '=',
                'd.hash_id'
            )
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->whereNull('d.deleted_at')
            ->groupBy(['d.id', 'd.hash_id', 'd.company_id', 'd.fio', 'u.id'])
            ->get()
            ->toArray();
    }

    public function findUsers(): array
    {
        return DB::table('users as u')
            ->select([
                'u.id',
                'u.login',
                'c.id as company_id',
                'd.id as driver_id',
                DB::raw('json_arrayagg(mhr.role_id) as role_ids')
            ])
            ->leftJoin('model_has_roles as mhr', 'mhr.model_id', '=', 'u.id')
            ->leftJoin(
                DB::raw("(select * from companies as c where deleted_at is null) as c"),
                function ($join) {
                    $join->on('u.login', '=', DB::raw("CONCAT('0', c.hash_id)"));
                }
            )
            ->leftJoin(
                DB::raw("(select * from drivers as d where deleted_at is null) as d"),
                'd.hash_id',
                '=',
                'u.login'
            )
            ->whereNull('u.deleted_at')
            ->groupBy(['u.id', 'u.login', 'c.id', 'd.id'])
            ->get()
            ->toArray();
    }
}
