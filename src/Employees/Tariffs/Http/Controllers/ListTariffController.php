<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\GetTariffsTableItemsHandler;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\GetTariffsTableItemsQuery;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\TariffsTableFilters;

final class ListTariffController
{
    public function __invoke(Request $request, GetTariffsTableItemsHandler $handler): JsonResponse
    {
        $sortOrder = null;
        if ($request->input('sort_desc') !== null) {
            $sortOrder = filter_var($request->input('sort_desc'), FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc';
        }

        $tableItems = $handler->handle(new GetTariffsTableItemsQuery(
            (int) $request->input('page'),
            (int) $request->input('per_page'),
            $request->input('sort_by'),
            $sortOrder,
            new TariffsTableFilters(
                $request->input('filters.search'),
                $request->input('filters.towns'),
                $request->input('filters.roles'),
                $request->input('filters.points'),
            ),
        ));

        return response()->json($tableItems->toArray());
    }
}
