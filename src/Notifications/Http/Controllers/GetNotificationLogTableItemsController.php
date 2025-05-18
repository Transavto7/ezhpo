<?php

namespace Src\Notifications\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Src\Notifications\Queries\GetNotificationLogTableItems\GetNotificationLogTableItemsFilters;
use Src\Notifications\Queries\GetNotificationLogTableItems\GetNotificationLogTableItemsHandler;
use Src\Notifications\Queries\GetNotificationLogTableItems\GetNotificationLogTableItemsQuery;

final class GetNotificationLogTableItemsController extends Controller
{
    public function __invoke(Request $request, GetNotificationLogTableItemsHandler $handler)
    {
        $items = $handler->handle(new GetNotificationLogTableItemsQuery(
            $request->input('page'),
            $request->input('perPage'),
            $request->input('sortBy'),
            $request->input('sortDesc'),
            new GetNotificationLogTableItemsFilters(
                $request->input('filters.search') ?? '',
                $request->input('filters.notifications') ?? [],
                $request->input('filters.users') ?? [],
                $request->input('filters.actions') ?? []
            ),
        ));

        return response()->json($items);
    }
}
