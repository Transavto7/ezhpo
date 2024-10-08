<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Events\UserActions\ClientActionLogRequest;
use App\Exports\AnketasExport;
use App\FieldPrompt;
use App\Point;
use App\User;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Query\JoinClause;
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
            $connectModel = $fieldsKeys[$anketaModel['connectTo']];

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
        $fields = $request->all();
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
            ->with(['deleted_user']);

        if ($request->has('result_dop') && $request->input('result_dop') !== null) {
            filter_var($request->input('result_dop'), FILTER_VALIDATE_BOOLEAN)
                ? $forms->whereNotNull('result_dop')
                : $forms->whereNull('result_dop');
        }

        $duplicates = $request->get('duplicates', false);
        if (filter_var($duplicates, FILTER_VALIDATE_BOOLEAN) && $request->has('date') && $request->has('TO_date')) {
            if (! $request->has('date') || ! $request->has('TO_date')) {
                $request->session()->flash('error', 'Не выбран период проведения осмотров');
                $request->request->remove('filter');
            }

            $startDate = Carbon::parse($request->input('date'));
            $endDate = Carbon::parse($request->input('TO_date'))->addDay();

            if ($endDate->diff($startDate)->days > 31) {
                $request->session()->flash('error', 'Выбранный период проведения осмотров превышает 31 день');
                $request->request->remove('filter');
            }

            if (! $request->session()->has('error')) {
                $this->setDuplicatesQuery(
                    $validTypeForm,
                    $forms,
                    $startDate,
                    $endDate
                );
            }
        } else {
            $request->session()->forget('error');
        }

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
            'getFormFilter',
            'duplicates',
            'result_dop',
        ]);

        if (count($filterParams) > 0 && $filterActivated) {
            $formModel = new Anketa();

            foreach ($filterParams as $filterKey => $filterValue) {
                if ($filterKey == 'TO_date' || $filterKey == 'date') {
                    continue;
                }

                if ($filterValue === null) continue;

                if ($filterKey == 'hour_from') {
                    $forms->whereTime('date', '>=', $filterValue . ':00');
                    continue;
                }
                if ($filterKey == 'hour_to') {
                    $forms->whereTime('date', '<=', $filterValue . ':00');
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

                if ($filterKey === 'town_id') {
                    $forms = $forms
                        //TODO: нужно делать селект anketas.pv_id
                        ->leftJoin('points', 'anketas.point_id', '=', 'points.id')
                        ->whereIn('points.pv_id', $filterValue);
                    continue;
                }

                if (in_array($filterKey, $formModel->fillable)) {
                    if (in_array($filterKey, ['date', 'created_at'])) continue;

                    if ($filterKey == 'is_dop' && !$filterValue) {
                        $forms = $forms->where(function ($query) {
                            $query->whereNull('is_dop')->orWhere('is_dop', 0);
                        });
                        continue;
                    }

                    $explodeData = array_values(is_array($filterValue) ? $filterValue : explode(',', $filterValue));
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

                        if ($filterKey === 'flag_pak') {
                            $explodeData = array_map(function($item) {
                                return $item === 'internal' ? null : $item;
                            }, $explodeData);
                        }

                        if ($filterKey === 'driver_group_risk') {
                            $forms = $forms->where(function ($query) use ($explodeData, $filterKey) {
                                foreach ($explodeData as $fvItemValue) {
                                    $escapedFvItemValue = str_replace('\\', '\\\\', $fvItemValue);
                                    $query = $query->orWhere('anketas.' . $filterKey, 'like', '%' . trim($escapedFvItemValue) . '%');
                                }

                                return $query;
                            });

                            continue;
                        }

                        $forms = $forms->where(function ($query) use ($explodeData, $filterKey) {
                            foreach ($explodeData as $fvItemValue) {
                                if ($fvItemValue === null) {
                                    $query = $query->orWhereNull('anketas.' . $filterKey);
                                } else {
                                    $query = $query->orWhere('anketas.' . $filterKey, $fvItemValue);
                                }
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

                        if ($filterKey === 'flag_pak' && $explodeData === 'internal') {
                            $forms = $forms->whereNull('anketas.flag_pak');
                            continue;
                        }

                        if ($filterKey === 'driver_group_risk') {
                            $escapedExplodeData = str_replace('\\', '\\\\', $explodeData);
                            $forms = $forms->where('anketas.' . $filterKey, 'like', '%' . trim($escapedExplodeData) . '%');
                            continue;
                        }

                        $strictFilter = in_array($filterKey, ['company_name', 'driver_fio', 'admitted'])
                            || strpos($filterKey, '_id')
                            || $filterKey === 'id';

                        if ($strictFilter) {
                            $forms = $forms->where('anketas.' . $filterKey, $explodeData);
                            continue;
                        }

                        $forms = $forms->where('anketas.' . $filterKey, 'LIKE', '%' . $explodeData . '%');
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
                    $dateTo = Carbon::parse($filterValue)->endOfDay();
                    $forms = $forms->whereBetween('cars.date_prto', [$dateFrom, $dateTo]);
                    continue;
                }

                if ($filterKey === 'date_prmo') {
                    $dateFrom = Carbon::parse($filterValue)->startOfDay();
                    $dateTo = Carbon::parse($filterValue)->endOfDay();
                    $forms = $forms->whereBetween('drivers.date_prmo', [$dateFrom, $dateTo]);
                    continue;
                }

                if ($filterKey === 'straight_company_id') {
                    continue;
                }

                $forms = $forms->where('anketas.' . $filterKey, 'LIKE', '%' . $filterValue . '%');
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

            /**
             * Обогащение данных
             */
            if ($validTypeForm == 'tech') {
                $formsDistinctQuery = $formsDistinctQuery
                    ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id');
            } else if ($validTypeForm == 'pak') {
                $formsDistinctQuery = $formsDistinctQuery
                    ->leftJoin('points', 'anketas.pv_id', '=', 'points.id');
            }
            /**
             * Обогащение данных
             */


            return response()->json([
                'anketasCountDrivers' => $formsDistinctQuery->count('anketas.driver_id'),
                'anketasCountCars' => $formsDistinctQuery->count('anketas.car_id'),
                'anketasCountCompany' => $formsDistinctQuery->count('anketas.company_id'),
            ]);
        }
        /**
         * <Измеряем количество Авто и Водителей (уникальные ID)>
         */

        $export = $filterActivated && $request->get('export');

        /**
         * Обогащение данных
         */
        if ($validTypeForm == 'tech') {
            $forms = $forms
                ->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
                ->select([
                    'anketas.*',
                    'anketas.pv_id as pv_id',
                    'cars.type_auto as car_type_auto',
                    'cars.date_prto as date_prto'
                ]);
        } else if ($validTypeForm == 'pak') {
            $forms = $forms
                ->leftJoin('points', 'anketas.pv_id', '=', 'points.id')
                ->select([
                    'anketas.*',
                    'points.name as pv_id',
                ]);
        } else if (($validTypeForm == 'medic') && $export) {
            $forms = $forms
                ->with('operator')
                ->leftJoin('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
                ->leftJoin('medic_form_normalized_pressures', 'anketas.id', '=', 'medic_form_normalized_pressures.form_id')
                ->select([
                    'anketas.*',
                    'anketas.pv_id as pv_id',
                    'drivers.date_prmo as date_prmo',
                    DB::raw("COALESCE(medic_form_normalized_pressures.pressure, anketas.tonometer, NULL) as tonometer"),
                ]);
        } else if ($validTypeForm === 'medic') {
            $forms = $forms
                ->with('operator')
                ->leftJoin('drivers', 'anketas.driver_id', '=', 'drivers.hash_id')
                ->select([
                    'anketas.*',
                    'anketas.pv_id as pv_id',
                    'drivers.date_prmo as date_prmo',
                ]);
        } else {
            $forms = $forms
                ->select([
                    'anketas.*',
                    'anketas.pv_id as pv_id',
                ]);
        }
        /**
         * Обогащение данных
         */

        /**
         * Метод фильтрации полей для ЛКК и экспортов
         */
        $filterFields = function (Collection $fields, bool $itemsIsModels = false) use ($user, $validTypeForm) {
            $exclude = [];
            if ($user->hasRole('client')) {
                $exclude = config('fields.client_exclude.' . $validTypeForm) ?? [];
            }
            $exclude[] = 'town_id';

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
        if ($export) {
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
                $fields = $filterFields(collect(Anketa::$fieldsKeys[$validTypeForm]));
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

            if (in_array($orderKey, ['car_type_auto', 'date_prto', 'date_prmo'])) {
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

        $fieldPrompts = $filterFields(
            FieldPrompt::query()
                ->where('type', $validTypeForm)
                ->orderBy('sort')
                ->orderBy('id')
                ->whereNotIn('field', ['hour_from', 'hour_to'])
                ->get(),
            true
        );

        event(new ClientActionLogRequest(Auth::user(), $validTypeForm));

        $view = $request->get('getFormFilter') ? 'home_filters' : 'home';
        return view($view, [
            'title' => Anketa::$anketsKeys[$validTypeForm],
            'name' => $user->name,
            'ankets' => $forms,
            'filter_activated' => $filterActivated,
            'type_ankets' => $validTypeForm,
            'anketsFields' => $formsFields,
            'anketsFieldsTable' => Anketa::$fieldsKeysTable[$validTypeForm] ?? $formsFields,
            'fieldsKeys' => $fieldsKeys,
            'fieldPrompts' => $fieldPrompts,
            'fieldsGroupFirst' => Anketa::$fieldsGroupFirst[$validTypeForm] ?? [],
            'blockedToExportFields' => Anketa::$blockedToExportFields[$validTypeForm] ?? [],
            'anketasCountResult' => $formsCountResult,
            'typePrikaz' => $request->get('typePrikaz'),
            'currentRole' => $currentRole,
            'take' => $take,
            'orderBy' => $orderBy,
            'orderKey' => $orderKey,
            'queryString' => Arr::query($request->except(['orderKey', 'orderBy']))
        ]);
    }

    public function getFilters(Request $request)
    {
        $fromType = $request->type_ankets;

        $validFormType = User::$userRolesKeys[auth()->user()->role] ?? 'medic';
        if (isset(Anketa::$anketsKeys[$fromType])) {
            $validFormType = $fromType;
        }

        $fields = Anketa::$fieldsKeys[$validFormType];
        $fieldsGroupFirst = Anketa::$fieldsGroupFirst[$validFormType] ?? [];
        $fieldsKeys = array_keys($fields);

        if (auth()->user()->hasRole('client')) {
            unset($fields['created_at']);
            unset($fields['is_pak']);
        }

        $exclude = config('fields.client_exclude.' . $validFormType) ?? [];

        return view('home_filters', [
            'anketsFields' => $fieldsKeys,
            'type_ankets' => $validFormType,
            'fieldsKeys' => $fields,
            'fieldsGroupFirst' => $fieldsGroupFirst,
            'exclude' => $exclude
        ]);
    }

    private function setDuplicatesQuery(string $formType, Builder $builder, $start, $end)
    {
        $duplicates = DB::table('anketas')
            ->select('day_hash')
            ->where('type_anketa', '=', $formType)
            ->where('date', '>=', $start->format('Y-m-d'))
            ->where('date', '<', $end->format('Y-m-d'))
            ->whereNotNull('day_hash')
            ->where('in_cart', '<>', 1)
            ->groupBy(['day_hash'])
            ->havingRaw('COUNT(day_hash) > 1');

        $builder->whereNotNull('anketas.day_hash')
            ->joinSub($duplicates, 'duplicates', function (JoinClause $join) {
                $join->on('anketas.day_hash', '=', 'duplicates.day_hash');
            })
            ->orderByDesc(DB::raw('DATE(date)'))
            ->orderBy('driver_fio')
            ->orderBy('type_view');
    }
}
