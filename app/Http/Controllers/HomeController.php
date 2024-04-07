<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Exports\AnketasExport;
use App\FieldPrompt;
use App\Point;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return Renderable
     */
    public static function searchFieldsAnkets($anketa, $anketaModel, $fieldsKeys)
    {
        $valueWhere = $anketa[$anketaModel['connectTo']];

        if (isset($anketaModel['connectItemProp'])) {
            $connectItemProp = $anketaModel['connectItemProp'];
            $connectModel    = $fieldsKeys[$anketaModel['connectTo']];

            $valueWhere = app($connectModel['model'])
                              ->where($connectItemProp['check'], $anketa[$connectModel['connectTo']])
                              ->first()[$connectItemProp['get']];
        }

        return app($anketaModel['model'])
                   ->where($anketaModel['key'], $valueWhere)
                   ->first()[$anketaModel['resultKey']];
    }

    public function SaveCheckedFieldsFilter(Request $request)
    {
        $fields      = $request->all();
        $type_ankets = $request->type_ankets;

        unset($fields['_token']);

        session(["fields_$type_ankets" => $fields]);

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function index(Request $request)
    {
        $user = Auth::user();

        /**
         * Очистка корзины в очереди на утверждение от СДПО
         */
        $clearPakQueue = $request->input('clear')
            && $user->hasRole('admin')
            && ($request->input('type_anketa') === 'pak_queue');

        if ($clearPakQueue) {
            Anketa::where('type_anketa', 'pak_queue')->delete();

            return redirect(route('home', 'pak_queue'));
        }
        /**
         * Очистка корзины в очереди на утверждение от СДПО
         */

        $formType = $request->type_ankets;
        $validTypeForm = User::$userRolesKeys[$user->role] ?? 'medic';
        if (isset(Anketa::$anketsKeys[$formType])) {
            $validTypeForm = $formType;
        }

        $trash = $request->get('trash', 0);
        $forms = Anketa::query()
            ->where('type_anketa', $validTypeForm)
            ->where('in_cart', $trash)
            ->with([
                'deleted_user'
            ]);

        /**
         * Фильтрация анкет в ЛКК
         */
        if ($user->hasRole('client')) {
            $forms = $forms
                ->where('anketas.company_id', $user->company->hash_id)
                ->whereNotNull('date')
                ->where(function ($query) use ($user) {
                    $query
                        ->where('date', '<=', Carbon::now()->addHours($user->timezone ?? 3))
                        ->orWhere(function ($subQuery) {
                            $subQuery
                                ->whereNotNull('flag_pak')
                                ->where('date', '<=', Carbon::now()->addHours(12));
                        });
                });

            if (in_array($validTypeForm, ['medic', 'pechat_pl', 'bdd', 'report_cart'])) {
                $forms = $forms->whereNotNull('driver_fio');
            } else if ($validTypeForm === 'tech') {
                $forms = $forms->whereNotNull('car_gos_number');
            }
        }
        /**
         * Фильтрация анкет в ЛКК
         */

        /**
         * Фильтрация анкет
         */
        $filterActivated = !empty($request->get('filter'));
        $filterParams = $request->except([
            'getCounts',
            'trash',
            'export',
            'exportPrikazPL',
            'exportPrikaz',
            'filter',
            'take',
            'orderBy',
            'orderKey',
            'typePrikaz',
            'page',
            'getFormFilter'
        ]);
        if (count($filterParams) > 0 && $filterActivated) {
            $formModel = new Anketa();

            foreach ($filterParams as $filterKey => $filterValue) {
                if ($filterKey == 'TO_date' || $filterKey == 'date') {
                    continue;
                }

                if ($filterValue === null) continue;

                if ($filterKey == 'hour_from') {
                    $forms->whereTime('date', '>=', $filterValue.':00');
                    continue;
                }
                if ($filterKey == 'hour_to') {
                    $forms->whereTime('date', '<=', $filterValue.':00');
                    continue;
                }
                if ($filterKey == 'created_at') {
                    $forms = $forms->where('anketas.created_at', '>=', Carbon::parse($filterValue)->startOfDay());
                    continue;
                }
                if ($filterKey == 'TO_created_at') {
                    $forms = $forms->where('anketas.created_at', '<=', Carbon::parse($filterValue)->endOfDay());
                    continue;
                }
                if ($filterKey === 'pulse') {
                    $forms = $forms->where('anketas.pulse', $filterValue);
                    continue;
                }

                if (in_array($filterKey, $formModel->fillable)) {
                    if (in_array($filterKey, ['date', 'created_at'])) continue;

                    if ($filterKey == 'is_dop' && !$filterValue){
                        $forms = $forms->where(function ($query){
                            $query->whereNull('is_dop')->orWhere('is_dop', 0);
                        });
                        continue;
                    }

                    $explodeData = is_array($filterValue) ? $filterValue : explode(',', $filterValue);
                    $explodeData = (count($explodeData) == 1) ? $explodeData[0] : $explodeData;

                    if (is_array($explodeData)) {
                        if ($filterKey === 'pv_id') {
                            $points = Point::whereIn('id', $explodeData)->get();
                            $forms = $forms->where(function ($query) use ($points, $filterKey) {
                                foreach ($points as $point) {
                                    $query = $query->orWhere('anketas.pv_id', $point->name)
                                        ->orWhere('anketas.point_id', $point->id);
                                }

                                return $query;
                            });
                            continue;
                        }

                        $forms = $forms->where(function ($query) use ($explodeData, $filterKey) {
                            foreach ($explodeData as $fvItemValue) {
                                $query = $query->orWhere('anketas.' . $filterKey, $fvItemValue);
                            }

                            return $query;
                        });
                        continue;
                    }

                    /**
                     * Проверяем что данные есть (повлияло на ФЛАГ СДПО)
                     */
                    if ($explodeData) {
                        if ($filterKey === 'pv_id') {
                            $point = Point::find($explodeData);
                            $forms = $forms->where(function ($query) use ($point) {
                                $query->where('anketas.pv_id', $point->name)
                                    ->orWhere('anketas.point_id', $point->id);

                                return $query;
                            });
                            continue;
                        }

                        $strictFilter = in_array($filterKey, ['company_name', 'driver_fio', 'admitted'])
                            || strpos($filterKey, '_id')
                            || $filterKey === 'id';

                        if ($strictFilter) {
                            $forms = $forms->where('anketas.' . $filterKey, $explodeData);
                            continue;
                        }

                        $forms = $forms->where('anketas.' . $filterKey, 'LIKE', '%'.$explodeData.'%');
                        continue;
                    }

                    if ($explodeData === null) {
                        $forms = $forms->where('anketas.' . $filterKey, null);
                        continue;
                    }

                    continue;
                }

                if ($filterKey === 'car_type_auto') {
                    $forms = $forms->whereIn('cars.type_auto', $filterValue);
                    continue;
                }

                if ($filterKey === 'date_prto') {
                    $dateFrom = Carbon::parse($filterValue)->startOfDay();
                    $dateTo   = Carbon::parse($filterValue)->endOfDay();
                    $forms = $forms->whereBetween('cars.date_prto', [$dateFrom, $dateTo]);
                    continue;
                }

                if ($filterKey === 'straight_company_id') {
                    continue;
                }

                $forms = $forms->where('anketas.' . $filterKey, 'LIKE', '%'.$filterValue.'%');
            }

            if (($filterParams['date'] ?? null) || ($filterParams['TO_date'] ?? null)) {
                $dateFrom = $filterParams['date']
                    ? Carbon::parse($filterParams['date'])->startOfDay()
                    : Carbon::now()->subYears(10);
                $dateTo = $filterParams['TO_date']
                    ? Carbon::parse($filterParams['TO_date'])->endOfDay()
                    : Carbon::now()->addYears(10);
                $forms = $forms->where(function ($query) use ($dateFrom, $dateTo) {
                    $query->where(function ($subQuery) use ($dateFrom, $dateTo) {
                        $subQuery
                            ->whereNotNull('date')
                            ->whereBetween('date', [$dateFrom, $dateTo]);
                    })->orWhere(function ($subQuery) use ($dateFrom, $dateTo) {
                        $subQuery
                            ->whereNull('date')
                            ->whereBetween('period_pl', [
                                $dateFrom->format('Y-m'),
                                $dateTo->format('Y-m')
                            ]);
                    });
                });
            }
        }
        /**
         * Фильтрация анкет
         */

        /**
         * <Измеряем количество Авто и Водителей (уникальные ID)>
         */
        if ($filterActivated && $request->input('getCounts')) {
            $formsDistinctQuery = $forms->distinct();

            return response()->json([
                'anketasCountDrivers' => $formsDistinctQuery->count('driver_id'),
                'anketasCountCars'    => $formsDistinctQuery->count('car_id'),
                'anketasCountCompany' => $formsDistinctQuery->count('company_id'),
            ]);
        }
        /**
         * <Измеряем количество Авто и Водителей (уникальные ID)>
         */

        /**
         * Обогащение данных
         */
        if ($validTypeForm == 'tech') {
            $forms = $forms
                ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
                ->select([
                    'anketas.*',
                    'cars.type_auto as car_type_auto',
                    'cars.date_prto as date_prto'
                ]);
        } else if ($validTypeForm == 'pak') {
            $forms = $forms
                ->leftJoin('points', 'anketas.pv_id', '=', 'points.id')
                ->select([
                    'anketas.*',
                    'points.name as pv_id'
                ]);
        } else if ($validTypeForm == 'medic') {
            $forms = $forms->with('operator');
        }
        /**
         * Обогащение данных
         */

        /**
         * Метод фильтрации полей для ЛКК
         */
        $filterFields = function (Collection $fields, bool $itemsIsModels = false) use ($user, $validTypeForm) {
            if (!$user->hasRole('client')) {
                return $fields;
            }

            $exclude = config('fields.client_exclude.' . $validTypeForm) ?? [];

            if (count($exclude) === 0) {
                return $fields;
            }

            if ($itemsIsModels) {
                return $fields->filter(function ($field) use ($exclude) {
                    return !in_array($field->field, $exclude);
                });
            }

            return $fields->filter(function ($title, $field) use ($exclude) {
                return !in_array($field, $exclude);
            });
        };
        /**
         * Метод фильтрации полей для ЛКК
         */

        /**
         * Экспорт журнала
         */
        if ($filterActivated && $request->get('export')) {
            $forms = $forms->orderBy('date');

            if ($validTypeForm == 'tech' && $request->get('exportPrikaz')) {
                $fields = $filterFields(collect(Anketa::$fieldsKeys['tech_export_to']));
                $forms = $forms->get();
                $title = 'ЭЖ ПРТО.xlsx';
            } else if ($validTypeForm == 'tech' && $request->get('exportPrikazPL')) {
                $fields = $filterFields(collect(Anketa::$fieldsKeys['tech_export_pl']));
                $forms = $forms->where(['type_view' => 'Предрейсовый/Предсменный'])->get();
                $title = 'ЭЖ учета ПЛ.xlsx';
            } else if ($validTypeForm == 'medic' && $request->get('exportPrikaz')) {
                $fields = $filterFields(collect(Anketa::$fieldsKeys['medic_export_pl']));
                $forms = $forms->get();
                $title = 'ЭЖ ПРМО.xlsx';
            } else if ($validTypeForm == 'bdd' && $request->get('exportPrikaz')) {
                $fields = $filterFields(collect(Anketa::$fieldsKeys['bdd_export_prikaz']));
                $forms = $forms->with(['user.roles'])->get()->map(function ($form) {
                    $form->user_id = 'Инженер по безопасности дорожного движения';
                    unset($form->user);
                    return $form;
                });
                $title = 'ЭЖ инструктажей БДД.xlsx';
            } else {
                $fields = Anketa::$fieldsKeys[$validTypeForm];
                $forms = $forms->get();
                $title = 'ЭЖ.xlsx';
            }

            return Excel::download(new AnketasExport($forms, $fields), $title);
        }
        /**
         * Экспорт журнала
         */

        /**
         * Выбор полей
         */
        $fieldsKeys = Anketa::$fieldsKeys[$validTypeForm];
        if ($trash) {
            $fieldsKeys['deleted_at'] = 'Время удаления';
        }
        if ($user->hasRole('client')) {
            $fieldsKeysToReset = [
                'created_at',
                'is_pak',
                'realy',
                'period_pl',
                'company_id',
                'is_dop',
                'driver_id'
            ];
            foreach ($fieldsKeysToReset as $fieldsKeyToReset) {
                unset($fieldsKeys[$fieldsKeyToReset]);
            }
            $fieldsKeys['id'] = true;
        }
        /**
         * Выбор полей
         */

        /**
         * Получение данных
         */
        $defaultOrderBy = $validTypeForm === 'pak_queue' ? 'ASC' : 'DESC';
        $orderKey = $request->get('orderKey', 'date');
        $orderBy = $request->get('orderBy', $defaultOrderBy);
        $take = $request->get('take') ?? 500;
        if ($request->get('export')) {
            $take = 10000;
        }

        if ($filterActivated || $validTypeForm === 'pak_queue') {
            $table = 'anketas.';

            if ($orderKey === 'car_type_auto' || $orderKey === 'date_prto') {
                $table = '';
            }

            $forms = $forms->orderBy($table . $orderKey, $orderBy);
            if ($orderKey === 'result_dop') {
                $forms = $forms->orderBy($table . 'is_dop', $orderBy === 'ASC' ? 'DESC' : 'ASC');
            }

            $forms = $forms->paginate($take);
            $formsCountResult = $forms->total();
        } else {
            $forms = [];
            $formsCountResult = 0;
        }
        /**
         * Получение данных
         */

        $formsFields = array_keys($fieldsKeys);

        $currentRole = $validTypeForm;
        if (($validTypeForm === 'pak_queue') && $user->hasRole('operator_sdpo')) {
            $currentRole = 'operator_sdpo';
        }

        $view = $request->get('getFormFilter') ? 'home_filters' : 'home';
        return view($view, [
            'title'                 => Anketa::$anketsKeys[$validTypeForm],
            'name'                  => $user->name,
            'ankets'                => $forms,
            'filter_activated'      => $filterActivated,
            'type_ankets'           => $validTypeForm,
            'anketsFields'          => $formsFields,
            'anketsFieldsTable'     => Anketa::$fieldsKeysTable[$validTypeForm] ?? $formsFields,
            'fieldsKeys'            => $fieldsKeys,
            'fieldPrompts'          => $filterFields(FieldPrompt::where('type', $validTypeForm)->get(), true),
            'fieldsGroupFirst'      => Anketa::$fieldsGroupFirst[$validTypeForm] ?? [],
            'blockedToExportFields' => Anketa::$blockedToExportFields[$validTypeForm] ?? [],
            'anketasCountResult'    => $formsCountResult,
            'typePrikaz'            => $request->get('typePrikaz'),
            'currentRole'           => $currentRole,
            'take'                  => $take,
            'orderBy'               => $orderBy,
            'orderKey'              => $orderKey,
            'queryString'           => Arr::query($request->except(['orderKey', 'orderBy']))
        ]);
    }

    public function getFilters(Request $request)
    {

        $typePrikaz = $request->get('typePrikaz');
        $typeAnkets = $request->type_ankets;

        $validTypeAnkets = User::$userRolesKeys[auth()->user()->role];
        if (isset(Anketa::$anketsKeys[$typeAnkets])) {
            $validTypeAnkets = $typeAnkets;
        }

        /**
         * Выбор полей
         */
        $fieldsKeysTypeAnkets = $validTypeAnkets;

        $fieldsKeys       = Anketa::$fieldsKeys[$fieldsKeysTypeAnkets];
        $fieldsGroupFirst = Anketa::$fieldsGroupFirst[$fieldsKeysTypeAnkets] ?? [];
        $anketsFields     = array_keys($fieldsKeys);

        if (auth()->user()->hasRole('client')) {
            unset($fieldsKeys['created_at']);
            unset($fieldsKeys['is_pak']);
        }

        $exclude = config('fields.client_exclude.' . $validTypeAnkets) ?? [];

        return view('home_filters', [
            'anketsFields'     => $anketsFields,
            'type_ankets'      => $validTypeAnkets,
            'fieldsKeys'       => $fieldsKeys,
            'fieldsGroupFirst' => $fieldsGroupFirst,
            'exclude' => $exclude
        ]);
    }
}
