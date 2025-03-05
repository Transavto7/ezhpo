<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

use App\Town;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SelectTownsController
{
    public function __invoke(Request $request): JsonResponse
    {
        $towns = Town::query()
            ->select('id', 'name')
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                return $builder->where('name', 'like', "%{$request->input('search')}%");
            })
            ->get();

        return response()->json($towns->toArray());
    }
}
