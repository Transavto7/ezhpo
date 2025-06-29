<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use App\Company;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class SelectCompaniesController
{
    public function __invoke(Request $request): JsonResponse
    {
        $items = Company::query()
            ->select([
                'id',
                DB::raw("CONCAT('[', hash_id, '] ', name) as name"),
            ])
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                return $builder
                    ->orWhere('name', 'like', "%{$request->input('search')}%")
                    ->orWhere('hash_id', 'like', "%{$request->input('search')}%");
            })
            ->get();

        return response()->json($items->toArray());
    }
}
