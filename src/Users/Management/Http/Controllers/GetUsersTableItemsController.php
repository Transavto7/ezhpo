<?php

namespace Src\Users\Management\Http\Controllers;

use App\Enums\UserEntityType;
use Illuminate\Http\Request;
use Src\Users\Management\Enums\UserStatusEnum;
use Src\Users\Management\Queries\GetUsersTableItems\GetUsersTableItemsHandler;
use Src\Users\Management\Queries\GetUsersTableItems\GetUsersTableItemsQuery;

final class GetUsersTableItemsController
{
    public function __invoke(Request $request, GetUsersTableItemsHandler $handler)
    {
        $userStatus = null;
        if ($request->input('filter.status')) {
            $userStatus = UserStatusEnum::from($request->input('filter.status'));
        }

        $entityType = null;
        if ($request->input('filter.entity_type') !== 'untyped' && $request->input('filter.entity_type') !== null) {
            $entityType = UserEntityType::from($request->input('filter.entity_type'));
        }

        $sortOrder = null;
        if ($request->input('sort_desc') !== null) {
            $sortOrder = filter_var($request->input('sort_desc'), FILTER_VALIDATE_BOOLEAN) ? 'desc' : 'asc';
        }

        $tableItems = $handler->handle(new GetUsersTableItemsQuery(
            $request->input('page'),
            $request->input('per_page'),
            $request->input('sort_by'),
            $sortOrder,
            $request->input('filter.search'),
            $userStatus,
            $entityType,
            $request->input('filter.user_ids') ?? [],
            $request->input('filter.entity_type') === 'untyped'
        ));

        return response()->json($tableItems);
    }
}