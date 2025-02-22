<?php

namespace Src\Users\Management\Http\Controllers;

use Spatie\Permission\Models\Permission;

final class GetPermissionItemsController
{
    public function __invoke()
    {
        $permissions = Permission::query()
            ->select([
                'id',
                'guard_name as name',
            ])
            ->orderBy('guard_name')
            ->get()
            ->toArray();

        return response()->json($permissions);
    }
}