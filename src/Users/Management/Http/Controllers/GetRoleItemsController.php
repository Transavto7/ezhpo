<?php

namespace Src\Users\Management\Http\Controllers;

use App\Enums\UserRoleEnum;
use Spatie\Permission\Models\Role;

final class GetRoleItemsController
{
    public function __invoke()
    {
        $permissions = Role::query()
            ->select([
                'id',
                'guard_name as name',
            ])
            ->whereNotIn('id', [UserRoleEnum::TERMINAL, UserRoleEnum::CLIENT, UserRoleEnum::DRIVER])
            ->get()
            ->toArray();

        return response()->json($permissions);
    }
}