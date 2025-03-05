<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\Enums\FeaturesEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;
use Src\Employees\Workdays\Commands\CalcEmployeeSalaries\CalcEmployeeSalaryConstant;
use Unleash\Client\Unleash;

class WorkdaysReportController extends Controller
{
    public function __invoke(Request $request, Unleash $unleash): View
    {
        if (! $unleash->isEnabled(FeaturesEnum::WORKDAYS_ENABLED)) {
            return view('common.disabled-feature-page', ['title' => 'Расчет ЗП']);
        }

        $townList = DB::table('towns')
            ->select([
                'towns.id',
                'towns.name',
            ])
            ->whereNull(['deleted_id', 'deleted_at'])
            ->get()
            ->toArray();

        $roleList = DB::table('roles')
            ->select([
                'roles.id',
                'roles.guard_name as name',
            ])
            ->whereNull(['deleted_id', 'deleted_at'])
            ->whereIn('roles.id', CalcEmployeeSalaryConstant::ROLE_IDS)
            ->get()
            ->toArray();

        $pointList = DB::table('points')
            ->select([
                'points.id',
                'points.name',
            ])
            ->whereNull(['deleted_id', 'deleted_at'])
            ->get()
            ->toArray();

        $employeeList = DB::table('users')
            ->select([
                'users.id',
                'users.name',
            ])
            ->join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
            ->whereIn('model_has_roles.role_id', CalcEmployeeSalaryConstant::ROLE_IDS)
            ->whereNull('users.deleted_at')
            ->get()
            ->toArray();

        return view('Workdays::report', [
            'months' => CalcEmployeeSalaryConstant::MONTH_LIST,
            'years' => range(CalcEmployeeSalaryConstant::START_YEAR, date('Y')),
            'selectedMonth' => (int) date('m'),
            'selectedYear' => (int) date('Y'),
            'townList' => $townList,
            'roleList' => $roleList,
            'pointList' => $pointList,
            'employeeList' => $employeeList,
        ]);
    }
}
