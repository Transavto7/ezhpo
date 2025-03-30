<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use App\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

final class SelectCompaniesController
{
    public function __invoke(Request $request): JsonResponse
    {
        $towns = Company::query()
            ->select('id', 'name')
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                return $builder->where('name', 'like', "%{$request->input('search')}%");
            })
            ->get();

        return response()->json($towns->toArray());
    }
}
