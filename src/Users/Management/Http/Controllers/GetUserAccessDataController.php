<?php

namespace Src\Users\Management\Http\Controllers;

use App\User;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Symfony\Component\HttpFoundation\Response;

final class GetUserAccessDataController
{
    public function __invoke(int $id)
    {
        $user = User::find($id);

        if (!$user) {
            return response()->json([
                'message' => 'Пользователь не найден'
            ])->setStatusCode(Response::HTTP_NOT_FOUND);
        }

        $userPermissions = $user
            ->permissions
            ->map(function (Permission $permission) {
                return [
                    'id' => $permission->id,
                    'name' => $permission->guard_name,
                ];
            })
            ->toArray();

        $userRoles = $user
            ->roles
            ->map(function (Role $role) {
                return [
                    'id' => $role->id,
                    'name' => $role->guard_name,
                ];
            })
            ->toArray();


        return response()->json([
            'permissions' => $userPermissions,
            'roles' => $userRoles,
        ]);
    }
}