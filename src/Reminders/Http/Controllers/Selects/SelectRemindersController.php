<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers\Selects;

use Illuminate\Database\Query\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

final class SelectRemindersController
{
    public function __invoke(Request $request): JsonResponse
    {
        $items = DB::table('reminders')
            ->select([
                'id',
                'title as name',
            ])
            ->when($request->input('search'), function (Builder $builder) use ($request) {
                $pattern = "%{$request->input('search')}%";

                return $builder->orWhere('title', 'like', $pattern);
            })
            ->limit(15)
            ->get()
            ->toArray();

        return response()->json($items);
    }
}
