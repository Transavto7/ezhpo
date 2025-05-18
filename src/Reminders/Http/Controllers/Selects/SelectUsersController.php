<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use App\Enums\UserEntityType;
use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SelectUsersController
{
    public function __invoke(Request $request): JsonResponse
    {
        $items = User::query()
            ->select([
                'users.id',
                'employees.name as user_name',
                'employees.hash_id as user_hash_id',
            ])
            ->leftJoin('employees', 'employees.related_user_id', '=', 'users.id')
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                $pattern = "%{$request->input('search')}%";

                return $builder->orWhere('employees.name', 'like', $pattern)
                    ->orWhere('employees.hash_id', 'like', $pattern);
            })
            ->where('users.entity_type', UserEntityType::EMPLOYEE)
            ->whereNull('employees.deleted_at')
            ->limit(15)
            ->get()
            ->toArray();

        $items = array_map(function (array $item) {
            return [
                'id' => $item['id'],
                'name' => '['.$item['user_hash_id'].'] '.$item['user_name'],
            ];
        }, $items);

        return response()->json($items);
    }
}
