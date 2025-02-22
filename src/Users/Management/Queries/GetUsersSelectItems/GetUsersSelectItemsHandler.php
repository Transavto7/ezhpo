<?php

namespace Src\Users\Management\Queries\GetUsersSelectItems;

use App\User;
use Illuminate\Support\Facades\DB;

final class GetUsersSelectItemsHandler
{
    public function handle(GetUsersSelectItemsQuery $query): array
    {
        $items = User::query()
            ->select([
                'users.id',
                DB::raw("CONCAT(
            '[', coalesce(employees.hash_id, drivers.hash_id, terminals.hash_id, companies.hash_id), '] ',
            coalesce(employees.name, drivers.fio, terminals.name, companies.name)
        ) as text"),
            ])
            ->leftJoin('employees', 'employees.related_user_id', '=', 'users.id')
            ->leftJoin('terminals', 'terminals.related_user_id', '=', 'users.id')
            ->leftJoin('companies', 'companies.related_user_id', '=', 'users.id')
            ->leftJoin('drivers', 'drivers.related_user_id', '=', 'users.id')
            ->whereNotNull('users.entity_type')
            ->when($query->getEntityType() !== null, function ($subQuery) use ($query) {
                return $subQuery->where('users.entity_type', $query->getEntityType());
            })
            ->when($query->getSearch() !== '', function ($subQuery) use ($query) {
                $search = '%'.$query->getSearch().'%';

                return $subQuery->where(function ($q) use ($search) {
                    $q->orWhere('employees.name', 'like', $search)
                        ->orWhere('employees.hash_id', 'like', $search)
                        ->orWhere('drivers.fio', 'like', $search)
                        ->orWhere('drivers.hash_id', 'like', $search)
                        ->orWhere('terminals.name', 'like', $search)
                        ->orWhere('terminals.hash_id', 'like', $search)
                        ->orWhere('companies.name', 'like', $search)
                        ->orWhere('companies.hash_id', 'like', $search);
                });
            })
            ->limit(10)
            ->get()
            ->toArray();

        return array_map(function ($item) {
            return [
                'id' => $item['id'],
                'name' => $item['text'],
            ];
        }, $items);
    }
}
