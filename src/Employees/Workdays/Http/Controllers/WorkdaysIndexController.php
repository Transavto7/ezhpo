<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\Enums\FeaturesEnum;
use App\FieldPrompt;
use App\Http\Controllers\Controller;
use App\Role;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Src\Employees\Workdays\Eloquent\Workday;
use Unleash\Client\Unleash;

class WorkdaysIndexController extends Controller
{
    public function __invoke(Request $request, Unleash $unleash): View
    {
        if (! $unleash->isEnabled(FeaturesEnum::WORKDAYS_ENABLED)) {
            return view('common.disabled-feature-page', ['title' => 'Журнал смен']);
        }

        $trash = filter_var($request->get('trash', 0), FILTER_VALIDATE_BOOLEAN);

        if ($trash) {
            $workdays = Workday::onlyTrashed();
        } else {
            $workdays = Workday::query();
        }

        $workdays = $workdays
            //TODO: нужно ли инфо о терминале?
            ->leftJoin('points', 'workdays.point_id', '=', 'points.id')
            ->leftJoin('users', 'workdays.employee_id', '=', 'users.id')
            ->leftJoin('workdays as closed_workdays', function ($query) {
                $query->on('workdays.id', '=', 'closed_workdays.open_workday_id')
                    ->orOn('closed_workdays.id', '=', 'workdays.open_workday_id');
            });

        if ($trash) {
            $workdays = $workdays->leftJoin('users as delete_users', 'workdays.deleted_id', '=', 'delete_users.id');
        }

        /**
         * Фильтрация анкет
         */
        $filterActivated = ! empty($request->get('filter'));
        $filterParams = $request->except([
            'getCounts',
            'trash',
            'filter',
            'take',
            'orderBy',
            'orderKey',
            'page',
        ]);

        $filtersToTablesMap = [
            'id' => 'workdays.id',
            'town_id' => 'points.pv_id',
            'employee_id' => 'workdays.employee_id',
        ];

        if (count($filterParams) > 0 && $filterActivated) {
            foreach ($filterParams as $filterKey => $filterValue) {
                if (array_key_exists($filterKey, $filtersToTablesMap)) {
                    $filterColumn = $filtersToTablesMap[$filterKey];
                } else {
                    $filterColumn = "workdays.$filterKey";
                }

                if ($filterValue === null) {
                    continue;
                }

                if ($filterKey == 'TO_date' || $filterKey == 'date') {
                    continue;
                }

                if (is_array($filterValue)) {
                    $filterValue = array_unique(array_values($filterValue));

                    if (count($filterValue) === 1) {
                        $filterValue = $filterValue[0];
                    }
                }

                if ($filterKey === 'roles') {
                    $roleIds = Role::query()
                        ->select(['id'])
                        ->where('name', $filterValue)
                        ->get()
                        ->pluck('id')
                        ->toArray();

                    $workdays = $workdays->leftJoin('model_has_roles', function ($query) use ($roleIds) {
                        $query->on('workdays.employee_id', '=', 'model_has_roles.model_id')
                            ->where('model_has_roles.role_id', $roleIds);
                    })
                    ->whereNotNull('model_has_roles.role_id');

                    continue;
                }

                if ($filterKey === 'is_closed') {
                    if ($filterValue === '0') {
                        $workdays = $workdays->whereNull('closed_workdays.id');
                    } else {
                        $workdays = $workdays->whereNotNull('closed_workdays.id');
                    }
                    continue;
                }

                if ($filterKey === 'created_at') {
                    $workdays = $workdays->where('workdays.created_at', '>=', Carbon::parse($filterValue)->startOfDay());
                    continue;
                }

                if ($filterKey === 'TO_created_at') {
                    $workdays = $workdays->where('workdays.created_at', '<=', Carbon::parse($filterValue)->endOfDay());
                    continue;
                }

                //TODO: дублирование как и в анкетах, подумать как переделать
                if (is_array($filterValue)) {
                    $filterValue = array_map(function ($fvItemValue) {
                        return trim(str_replace('\\', '\\\\', $fvItemValue));
                    }, $filterValue);

                    if ($filterKey === 'flag_pak') {
                        $workdays = $workdays->where(function ($query) use ($filterValue, $filterKey) {
                            foreach ($filterValue as $flagPakValue) {
                                if ($flagPakValue === 'internal') {
                                    $query = $query->orWhereNull('workdays.flag_pak');
                                } else {
                                    $query = $query->orWhere('workdays.flag_pak', $flagPakValue);
                                }
                            }

                            return $query;
                        });
                        continue;
                    }

                    $workdays = $workdays->where(function ($query) use ($filterColumn, $filterValue) {
                        foreach ($filterValue as $fvItemValue) {
                            $query = $query->orWhere($filterColumn, $fvItemValue);
                        }

                        return $query;
                    });
                } else {
                    $filterValue = trim(str_replace('\\', '\\\\', $filterValue));

                    if ($filterKey === 'flag_pak' && $filterValue === 'internal') {
                        $workdays = $workdays->whereNull('workdays.flag_pak');
                        continue;
                    }

                    $strictFilter = strpos($filterKey, '_id') || $filterKey === 'workdays.id';

                    if ($strictFilter) {
                        $workdays = $workdays->where($filterColumn, $filterValue);
                        continue;
                    }

                    $workdays = $workdays->where($filterColumn, 'LIKE', '%'.$filterValue.'%');
                }
            }

            if (($filterParams['date'] ?? null) || ($filterParams['TO_date'] ?? null)) {
                $dateFrom = isset($filterParams['date'])
                    ? Carbon::parse($filterParams['date'])->startOfDay()
                    : Carbon::now()->subYears(10);
                $dateTo = isset($filterParams['TO_date'])
                    ? Carbon::parse($filterParams['TO_date'])->endOfDay()
                    : Carbon::now()->addYears(10);

                $workdays = $workdays
                    ->whereBetween('workdays.date', [$dateFrom, $dateTo]);
            }
        }

        $defaultFieldsToSelect = [
            'workdays.*',
            'workdays.created_at as created_at',
            'workdays.updated_at as updated_at',
            'users.name as employee_fio',
            'points.name as point_id',
            'closed_workdays.id as closed_workday_id',
        ];

        if ($trash) {
            $defaultFieldsToSelect[] = 'delete_users.name as deleted_user_name';
        }

        $workdays = $workdays->select($defaultFieldsToSelect);

        /**
         * Выбор полей
         */
        $fieldsKeys = [
            'employee_id'            => 'ID Сотрудника',
            'date'                   => 'Дата и время осмотра',
            'realy'                  => 'Осмотр реальный?',
            'type_view'              => 'Тип осмотра',
            'proba_alko'             => 'Признаки опьянения',
            'test_narko'             => 'Тест на наркотики',
            'admitted'               => 'Допуск к работе',
            'created_at'             => 'Дата создания',
            'point_id'                  => 'Пункт выпуска',
            'town_id'                => 'Город',
            'flag_pak'               => 'Вид осмотра',
            'deleted_at'             => 'Время удаления',
        ];
        /**
         * Выбор полей
         */

        /**
         * Получение данных
         */
        $orderKey = $request->get('orderKey', 'date');
        $orderBy = $request->get('orderBy', 'DESC');
        $take = $request->get('take') ?? 100;

        if ($filterActivated) {
            $workdays = $workdays->orderBy($orderKey, $orderBy);

            $workdays = $workdays->paginate($take);
            $formsCountResult = $workdays->total();
        } else {
            $workdays = [];
            $formsCountResult = 0;
        }
        /**
         * Получение данных
         */
        $formsFields = array_keys($fieldsKeys);

        $fieldPrompts = FieldPrompt::query()
            ->where('type', 'workdays')
            ->orderBy('sort')
            ->orderBy('id')
            ->get();

        return view('Workdays::index', [
            'workdays' => $workdays,
            'filter_activated' => $filterActivated,
            'fields' => $formsFields,
            'fieldsKeys' => $fieldsKeys,
            'fieldPrompts' => $fieldPrompts,
            'count' => $formsCountResult,
            'take' => $take,
            'orderBy' => $orderBy,
            'orderKey' => $orderKey,
            'queryString' => Arr::query($request->except(['orderKey', 'orderBy'])),
        ]);
    }
}
