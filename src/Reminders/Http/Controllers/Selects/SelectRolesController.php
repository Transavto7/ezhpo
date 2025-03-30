<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use App\Role;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SelectRolesController
{
    public function __invoke(Request $request): JsonResponse
    {
        $towns = Role::query()
            ->select('id', 'guard_name as name')
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                return $builder->where('guard_name', 'like', "%{$request->input('search')}%");
            })
            ->get();

        return response()->json($towns->toArray());
    }
}
