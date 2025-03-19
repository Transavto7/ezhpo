<?php

namespace Src\Employees\Workdays\Http\Controllers;

use App\FieldPrompt;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\View\View;
use Src\Employees\Workdays\Eloquent\Workday;

class WorkdaysIndexController extends Controller
{
    public function __invoke(Request $request): View
    {
        $workdays = Workday::query();

        $workdays = $workdays
            //TODO: нужно ли инфо о терминале?
            ->leftJoin('points', 'workdays.point_id', '=', 'points.id')
            ->leftJoin('users', 'workdays.employee_id', '=', 'users.id');

        /**
         * Фильтрация анкет
         */
        $filterActivated = !empty($request->get('filter'));
        $filterParams = $request->except([
            'getCounts',
            'trash',
            'filter',
            'take',
            'orderBy',
            'orderKey',
            'page'
        ]);

        $filtersToTablesMap = [
            'id' => 'workdays.id',
            'town_id' => 'points.pv_id',
            'employee_id' => 'workdays.user_id',
        ];

        if (count($filterParams) > 0 && $filterActivated) {
            foreach ($filterParams as $filterKey => $filterValue) {
                if (array_key_exists($filterKey, $filtersToTablesMap)) {
                    $filterKey = $filtersToTablesMap[$filterKey];
                }

                if ($filterValue === null) continue;

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
                    //TODO: реализовать фильтр по ролям
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

                    if ($filterKey === 'is_closed') {
                        $workdays = $workdays->where(function ($query) use ($filterValue, $filterKey) {
                            foreach ($filterValue as $isClosedValue) {
                                if ($isClosedValue === '0') {
                                    $query = $query->orWhereNull('workdays.open_workday_id');
                                } else {
                                    $query = $query->orWhereNotNull('workdays.open_workday_id');
                                }
                            }

                            return $query;
                        });
                        continue;
                    }

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

                    $workdays = $workdays->where(function ($query) use ($filterValue, $filterKey) {
                        foreach ($filterValue as $fvItemValue) {
                            $query = $query->orWhere($filterKey, $fvItemValue);
                        }

                        return $query;
                    });
                } else {
                    $filterValue = trim(str_replace('\\', '\\\\', $filterValue));

                    if ($filterKey === 'flag_pak' && $filterValue === 'internal') {
                        $workdays = $workdays->whereNull('workdays.flag_pak');
                        continue;
                    }

                    if ($filterKey === 'is_closed') {
                        if ($filterValue === '0') {
                            $workdays = $workdays->whereNull('workdays.open_workday_id');
                        } else {
                            $workdays = $workdays->whereNotNull('workdays.open_workday_id');
                        }
                        continue;
                    }

                    $strictFilter = strpos($filterKey, '_id') || $filterKey === 'workdays.id';

                    if ($strictFilter) {
                        $workdays = $workdays->where($filterKey, $filterValue);
                        continue;
                    }

                    $workdays = $workdays->where($filterKey, 'LIKE', '%' . $filterValue . '%');
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
        ];

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
            'deleted_at'             => 'Время удаления'
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
            'queryString' => Arr::query($request->except(['orderKey', 'orderBy']))
        ]);
    }
}
