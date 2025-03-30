<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use App\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SelectUsersController
{
    public function __invoke(Request $request): JsonResponse
    {
        $towns = User::query()
            ->selectRaw("id, concat(name, ' (', login , ')') as name")
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                return $builder->where('name', 'like', "%{$request->input('search')}%");
            })
            ->get();

        return response()->json($towns->toArray());
    }
}
