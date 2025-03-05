<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

use App\Point;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SelectPointsController
{
    public function __invoke(Request $request): JsonResponse
    {
        $towns = Point::query()
            ->select('id', 'name')
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                return $builder->where('name', 'like', "%{$request->input('search')}%");
            })
            ->when($request->input('town_id'), function (Builder $builder) use ($request) {
                return $builder->where('pv_id', '=', $request->input('town_id'));
            })
            ->get();

        return response()->json($towns->toArray());
    }
}
