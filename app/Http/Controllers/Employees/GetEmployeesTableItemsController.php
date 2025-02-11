<?php

namespace App\Http\Controllers\Employees;

use App\Employee;
use App\Enums\UserEntityType;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

final class GetEmployeesTableItemsController extends Controller
{
    public function __invoke(Request $request)
    {
        $employees = Employee::query()
            ->with([
                'user' => function ($query) {
                    return $query->withTrashed();
                },
                'pv',
                'user.roles',
            ]);

        if ($request->get('deleted')) {
            $employees->with(['whoDeleted'])->onlyTrashed();
        }

        $ids = $request->get('employee_id');
        if ($ids) {
            $employees->whereIn('id', $ids);
        }

        $email = $request->get('email');
        if ($email) {
            $employees
                ->join('users', function ($join) {
                    $join->on('users.entity_id', '=', 'employees.id')
                        ->where('users.entity_type', '=', UserEntityType::employee());
                })
                ->where('users.email', 'like', "%$email%")
                ->select('employees.*')
                ->groupBy(['employees.id']);
        }

        $pvId = $request->get('point_id');
        if ($pvId) {
            $employees->where('pv_id', $pvId);
        }

        $role = $request->get('role');
        if ($role) {
            $employees
                ->join('users', function ($join) {
                    $join->on('users.entity_id', '=', 'employees.id')
                        ->where('users.entity_type', '=', UserEntityType::employee());
                })
                ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                ->join('roles', function ($join) {
                    $join->on('model_has_roles.role_id', '=', 'roles.id');
                })
                ->where('roles.id', '=', $role)
                ->select('employees.*', 'guard_name')
                ->groupBy(['employees.id']);
        }

        $sortBy = $request->get('sortBy', 'id');
        $sortOrder = $request->get('sortDesc') == 'true' ? 'DESC' : 'ASC';

        if ($sortBy) {
            if ($sortBy == 'roles') {
                $employees
                    ->join('users', function ($join) {
                        $join->on('users.entity_id', '=', 'employees.id')
                            ->where('users.entity_type', '=', UserEntityType::employee());
                    })
                    ->join('model_has_roles', 'users.id', '=', 'model_has_roles.model_id')
                    ->join('roles', function ($join) use ($sortOrder) {
                        $join->on('model_has_roles.role_id', '=', 'roles.id')
                            ->orderBy('roles.guard_name', $sortOrder);
                    })
                    ->select('employees.*', 'guard_name')
                    ->orderBy('roles.guard_name', $sortOrder)
                    ->groupBy(['employees.id']);
            } else {
                $userAttrs = ['login', 'email', 'timezone', 'photo'];

                if (in_array($sortBy, $userAttrs)) {
                    $employees
                        ->join('users', function ($join) {
                            $join->on('users.entity_id', '=', 'employees.id')
                                ->where('users.entity_type', '=', UserEntityType::employee());
                        })
                        ->select('employees.*', 'users.' . $sortBy)
                        ->orderBy("users.$sortBy", $sortOrder)
                        ->groupBy(['employees.id']);
                } else {
                    $employees->orderBy("employees.$sortBy", $sortOrder);
                }
            }
        }

        $res = $employees->paginate($request->get('take', 1));

        return response([
            'total' => $res->total(),
            'page' => $res->currentPage(),
            'items' => $res->getCollection(),
        ]);
    }
}