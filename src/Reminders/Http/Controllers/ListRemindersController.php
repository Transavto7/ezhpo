<?php

declare(strict_types=1);

namespace Src\Reminders\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Src\Reminders\Queries\GetRemindersTableItems\GetRemindersTableItemsHandler;
use Src\Reminders\Queries\GetRemindersTableItems\GetRemindersTableItemsQuery;
use Src\Reminders\Queries\GetRemindersTableItems\RemindersTableFilters;

final class ListRemindersController
{
    public function __invoke(Request $request, GetRemindersTableItemsHandler $handler): JsonResponse
    {
        $sortOrder = null;
        if ($request->input('sortDesc') !== null) {
            $sortOrder = filter_var($request->input('sortDesc'), FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc';
        }

        $tableItems = $handler->handle(new GetRemindersTableItemsQuery(
            (int) $request->input('page'),
            (int) $request->input('perPage'),
            $request->input('sortBy'),
            $sortOrder,
            new RemindersTableFilters(
                $request->input('filters.search'),
                $request->input('filters.reminders'),
                $request->input('filters.cities'),
                $request->input('filters.companies'),
                $request->input('filters.points'),
                $request->input('filters.roles'),
                $request->input('filters.subjects'),
                $request->input('filters.subject_type'),
                $request->input('filters.users'),
                $request->input('filters.actions'),
            ),
        ));

        return response()->json($tableItems->toArray());
    }
}
