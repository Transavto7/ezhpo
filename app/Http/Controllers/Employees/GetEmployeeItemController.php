<?php

namespace App\Http\Controllers\Employees;

use App\Employee;
use App\Http\Controllers\Controller;
use Symfony\Component\HttpFoundation\Response;

final class GetEmployeeItemController extends Controller
{
    public function __invoke(int $id)
    {
        $result = Employee::withTrashed()
            ->with([
                'pv',
                'user' => function($query) {
                    return $query->withTrashed();
                },
                'user.roles',
                'user.permissions',
                'user.points' // todo: перенести в Employees
            ])
            ->find($id);

        if (!$result) {
            return response()->json([
                'message' => 'Employee not found'
            ], Response::HTTP_NOT_FOUND);
        }

        $result->userPermissionIds = $result->user->permissions
            ->pluck('id')
            ->values();

        $result->pvs = $result->user->points
            ->pluck('id')
            ->values();

        return response()->json($result);
    }
}