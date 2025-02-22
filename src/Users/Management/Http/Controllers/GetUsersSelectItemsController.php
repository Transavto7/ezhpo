<?php

namespace Src\Users\Management\Http\Controllers;

use App\Enums\UserEntityType;
use Illuminate\Bus\Dispatcher;
use Illuminate\Http\Request;
use Src\Users\Management\Queries\GetUsersSelectItems\GetUsersSelectItemsQuery;

final class GetUsersSelectItemsController
{
    public function __invoke(Request $request, Dispatcher $dispatcher)
    {
        $search = $request->input('search') ?? '';
        $entityType = null;

        if ($request->input('entity_type') !== null && $request->input('entity_type') !== 'untyped') {
            $entityType = UserEntityType::from($request->input('entity_type'));
        }

        $items = $dispatcher->dispatch(new GetUsersSelectItemsQuery($search, $entityType));

        return response()->json($items);
    }
}