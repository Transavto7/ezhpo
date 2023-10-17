<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Exports\AnketasExport;
use App\FieldPrompt;
use App\Point;
use App\User;
use Auth;
use Carbon\Carbon;
use Illuminate\Contracts\Support\Renderable;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
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
        $queryString = '';

        $oKey = 'orderKey';
        $oBy  = 'orderBy';


        /**
         * All Query String Without Params
         */
        foreach ($_GET as $getK => $getV) {
            if ($getK !== $oKey && $getK !== $oBy) {
                if (is_array($getV)) {
                    foreach ($getV as $getVkey => $getVvalue) {
                        $queryString .= '&'.$getK."[$getVkey]".'='.$getVvalue;
                    }
                } else {
                    $queryString .= '&'.$getK.'='.$getV;
                }

            }
        }

        $user = Auth::user();

        $validTypeAnkets       = User::$userRolesKeys[$user->role] ?? 'medic';
        $blockedToExportFields = [];
        $typeAnkets            = $request->type_ankets;

        $anketasModel = new Anketa();
        $anketas      = $anketasModel;

        $take     = $request->get('take') ? $request->get('take') : 500;
        $orderKey = $request->get($oKey, 'date');
        $orderBy  = $request->get($oBy,
            ($typeAnkets === 'pak_queue' ? 'ASC' : 'DESC')
        );

        // Если пользователь менее менеджера - то показываем только свои анкеты, заполненные
        if (isset(Anketa::$anketsKeys[$typeAnkets])) {
            $validTypeAnkets = $typeAnkets;
        }

        /**
         * Экспорт по приказу
         */
        $typePrikaz = $request->get('typePrikaz');
        $export     = $request->get('export');


        if (isset(Anketa::$blockedToExportFields[$validTypeAnkets])) {
            $blockedToExportFields = Anketa::$blockedToExportFields[$validTypeAnkets];
        }

        /**
         * Выбор полей
         */
        $fieldsKeysTypeAnkets = $validTypeAnkets;
        $fieldsKeys       = Anketa::$fieldsKeys[$fieldsKeysTypeAnkets];
        $fieldsGroupFirst = isset(Anketa::$fieldsGroupFirst[$fieldsKeysTypeAnkets])
            ? Anketa::$fieldsGroupFirst[$fieldsKeysTypeAnkets] : [];

        if ($request->get('export')) {
            $take = 10000;
        }

        /**
         * Очистка корзины в очереди на утверждение от СДПО
         */
        if (isset($_GET['clear']) && isset($_GET['type_anketa']) && $user->hasRole('admin')) {
            $typeClearAnkets = trim($_GET['type_anketa']);

            if ($typeClearAnkets === 'pak_queue') {
                Anketa::where('type_anketa', 'pak_queue')->delete();

                return redirect(route('home', $typeClearAnkets));
            }
        }

        /**
         * Фильтрация анкет
         */
        $filter_activated = !empty($request->get('filter'));

        $filter_params = $request->all(); // ИСПРАВИЛ: array_diff($request->all(), array(''))
        $is_export     = isset($_GET['export']);
        $trash         = $request->get('trash', 0);
        $getCounts     = isset($_GET['getCounts']);

        if($trash){
            $fieldsKeys['deleted_at'] = 'Время удаления';
        }

        unset($filter_params['getCounts']);
        unset($filter_params['trash']);
        unset($filter_params['export']);
        unset($filter_params['exportPrikazPL']);
        unset($filter_params['exportPrikaz']);
        unset($filter_params['filter']);
        unset($filter_params['take']);
        unset($filter_params['orderBy']);
        unset($filter_params['orderKey']);
        unset($filter_params['typePrikaz']);
        unset($filter_params['page']);
        unset($filter_params['getFormFilter']);

        // Уникальные и независимые поля
        // dolboeb ebanii
//        $filterExcept = [
//            'TO_created_at' => 'created_at',
//            'TO_date' => 'date'
//        ];

        // Фильтр
        if (count($filter_params) > 0 && $filter_activated) {
            foreach ($filter_params as $fk => &$fv) {
                if ($fk == 'hour_from' && $fv) {
                    $anketas->whereTime('date', '>=', $fv.':00');
                    continue;
                }
                if ($fk == 'hour_to' && $fv) {
                    $anketas->whereTime('date', '<=', $fv.':00');
                    continue;
                }
                // В любом случае все ключи передаются
                if ($fk == 'TO_date') {
                    continue;
                }
                // если ключ date, и date или TO_date не пустые
                if ($fk == 'date' && ($filter_params['date'] || $filter_params['TO_date'])) {
                    $date_from = $fv ? Carbon::parse($fv)->startOfDay() : Carbon::now()->subYears(10);
                    $date_to   = $filter_params['TO_date'] ? Carbon::parse($filter_params['TO_date'])->endOfDay() : Carbon::now()->addYears(10);

                    $anketas = $anketas->where(function ($q) use ($date_from, $date_to) {
                        $q->where(function ($q) use ($date_from, $date_to) {
                            $q->whereNotNull('anketas.date')
                              ->whereBetween('date', [$date_from, $date_to]);
                        })->orWhere(function ($q) use ($date_from, $date_to) {
                            $q->whereNull('anketas.date')
                              ->whereBetween('period_pl', [
                                  $date_from->format('Y-m'),
                                  $date_to->format('Y-m')
                              ]);
                        });
                    });

                    unset($date_from);
                    unset($date_to);
                    continue;
                }

                if ($fk == 'created_at' && $fv) {
                    $anketas = $anketas->where('anketas.created_at', '>=', Carbon::parse($fv)->startOfDay());
                    continue;
                }
                if ($fk == 'TO_created_at' && $fv) {
                    $anketas = $anketas->where('anketas.created_at', '<=', Carbon::parse($fv)->endOfDay());
                    continue;
                }

                if ((in_array($fk, $anketasModel->fillable))) {
                    // Поиск по дефолтным полям в таблице Anketas

                    // Проверяем пустые поля
                    if (isset($fv)) {
                        if ($fk == 'is_dop' && !$fv){
                            $anketas = $anketas->where(function ($q){
                                $q->whereNull('is_dop')
                                  ->orwhere('is_dop', 0);
                            });
                            continue;
                        }

                        if ($fk !== 'date' && $fk !== 'created_at') {
                            $explodeData = is_array($fv) ? $fv : explode(',', $fv);
                            $explodeData = (count($explodeData) == 1) ? $explodeData[0] : $explodeData;

                            if (is_array($explodeData)) {
                                if ($fk === 'pv_id') {
                                    $points = Point::whereIn('id', $explodeData)->get();
                                    $anketas = $anketas->where(function ($q) use ($points, $fk) {
                                        foreach ($points as $point) {
                                            $q = $q->orWhere('anketas.pv_id', $point->name)
                                            ->orWhere('anketas.point_id', $point->id);
                                        }

                                        return $q;
                                    });
                                } else {
                                    $anketas = $anketas->where(function ($q) use ($explodeData, $fk) {
                                        foreach ($explodeData as $fvItemKey => $fvItemValue) {
                                            $q = $q->orWhere('anketas.' . $fk, $fvItemValue); // TODO: поправили Like
                                        }

                                        return $q;
                                    });
                                }
                            } else {
                                /**
                                 * Проверяем что данные есть (повлияло на ФЛАГ СДПО)
                                 */
                                if ($explodeData) {
                                    // Для строгих значений
                                    if (in_array($fk, ['company_name', 'driver_fio']) || strpos($fk, '_id')
                                        || $fk === 'id') {
                                        if ($fk === 'pv_id') {
                                            $point = Point::find($explodeData);
                                            $anketas = $anketas->where(function ($q) use ($point) {
                                                $q->where('anketas.pv_id', $point->name)
                                                    ->orWhere('anketas.point_id', $point->id);

                                                return $q;
                                            });
                                        } else {
                                            $anketas = $anketas->where('anketas.' . $fk, $explodeData);
                                        }
                                    } // Для динамичных значений
                                    else {
                                        $anketas = $anketas->where('anketas.' . $fk, 'LIKE', '%'.$explodeData.'%');
                                    }
                                } else if ($explodeData === null) {
                                    $anketas = $anketas->where('anketas.' . $fk, null);
                                }
                            }
                        }
                    }
                } else {
                    if (!empty($fv)) {
                        if ($fk === 'car_type_auto') {
                            $anketas = $anketas->whereIn('cars.type_auto', $fv);
                        } else if ($fk === 'date_prto') {
                            $date_from = $fv ? Carbon::parse($fv)->startOfDay() : Carbon::now()->subYears(10);
                            $date_to   = $fv ? Carbon::parse($fv)->endOfDay() : Carbon::now()->addYears(10);

                            $anketas = $anketas->whereBetween('cars.date_prto', [$date_from, $date_to]);
                        } else {
                            if ($fk === 'straight_company_id') continue;
                            $anketas = $anketas->where('anketas.' . $fk, 'LIKE', '%'.$fv.'%');
                        }
                    }
                }
            }

        }

        if (auth()->user()->hasRole('client')) {
            $company_id_client = auth()->user()->company->hash_id;
            $anketas = $anketas->where('anketas.company_id', $company_id_client);
        }

        $anketas = $anketas->where('type_anketa', $validTypeAnkets)->where('in_cart', $trash)->with('deleted_user');

        /**
         * <Измеряем количество Авто и Водителей (уникальные ID)>
         */
        if ($filter_activated && $getCounts) {
            //https://web-answers.ru/php/kak-podschitat-unikalnye-znachenija-stolbcov.html
            $anketasCountDrivers = 0;
            $anketasCountCars    = 0;
            $anketasCountCompany = 0;

            $anketasTrigger = $anketas->distinct();

            $anketasCountDrivers = $anketasTrigger->count('driver_id');
            $anketasCountCars    = $anketasTrigger->count('car_id');
            $anketasCountCompany = $anketasTrigger->count('company_id');

            return response()->json([
                                        'anketasCountDrivers' => $anketasCountDrivers,
                                        'anketasCountCars'    => $anketasCountCars,
                                        'anketasCountCompany' => $anketasCountCompany,
                                    ]);
        }

        if ($validTypeAnkets == 'tech') {
            $anketas = $anketas->leftJoin('cars', 'anketas.car_id', '=', 'cars.hash_id')
                ->select('anketas.*', 'cars.type_auto as car_type_auto', 'cars.date_prto as date_prto');
        } else if ($validTypeAnkets == 'pak') {
            $anketas = $anketas->leftJoin('points', 'anketas.pv_id', '=', 'points.id')->select('anketas.*',
                                                                                               'points.name as pv_id');
        } else if ($validTypeAnkets == 'medic') {
            $anketas = $anketas->with('operator');
        }

        /**
         * </Измеряем количество Авто и Водителей (уникальные ID)>
         */

        // Экспорт из техосмотров и БДД
        if ($is_export && $filter_activated) {
            if ($validTypeAnkets == 'tech') {
                if ($request->get('exportPrikaz')) {
                    $techs = $anketas->where('type_anketa', 'tech');

                    $fields = collect(Anketa::$fieldsKeys['tech_export_to']);
                    if ($request->user()->hasRole('client')) {
                        $techs = $techs->whereNotNull('date');

                        if (in_array($validTypeAnkets, ['medic', 'pechat_pl', 'bdd', 'report_cart'])) {
                            $techs = $techs->whereNotNull('driver_fio');
                        } else if ($validTypeAnkets === 'tech') {
                            $techs = $techs->whereNotNull('car_gos_number');
                        }

                        $exclude = config('fields.client_exclude.' . $validTypeAnkets) ?? [];
                        $fields = $fields->filter(function ($field) use ($exclude) {
                            if (in_array($field, $exclude)) {
                                return false;
                            }

                            return true;
                        });
                    }
                    $techs = $techs->get();

                    return Excel::download(new AnketasExport($techs, $fields),
                        'ЭЖ ПРТО.xlsx');
                }

                if ($request->get('exportPrikazPL')) {
                    $techs = $anketas->where('type_anketa', 'tech')->where(['type_view' => 'Предрейсовый/Предсменный']);

                    $fields = collect(Anketa::$fieldsKeys['tech_export_pl']);
                    if ($request->user()->hasRole('client')) {
                        $techs = $techs->whereNotNull('date');

                        if (in_array($validTypeAnkets, ['medic', 'pechat_pl', 'bdd', 'report_cart'])) {
                            $techs = $techs->whereNotNull('driver_fio');
                        } else if ($validTypeAnkets === 'tech') {
                            $techs = $techs->whereNotNull('car_gos_number');
                        }

                        $exclude = config('fields.client_exclude.' . $validTypeAnkets) ?? [];
                        $fields = $fields->filter(function ($field) use ($exclude) {
                            if (in_array($field, $exclude)) {
                                return false;
                            }

                            return true;
                        });
                    }

                    $techs = $techs->cursor();

                    return Excel::download(new AnketasExport($techs, $fields),
                        'ЭЖ учета ПЛ.xlsx');
                }
            }

            if ($validTypeAnkets == 'medic') {
                if ($request->get('exportPrikaz')) {
                    $medic = $anketas->where('type_anketa', 'medic');

                    $fields = collect(Anketa::$fieldsKeys['medic_export_pl']);
                    if ($request->user()->hasRole('client')) {
                        $medic = $medic->whereNotNull('date');

                        if (in_array($validTypeAnkets, ['medic', 'pechat_pl', 'bdd', 'report_cart'])) {
                            $medic = $medic->whereNotNull('driver_fio');
                        } else if ($validTypeAnkets === 'tech') {
                            $medic = $medic->whereNotNull('car_gos_number');
                        }

                        $exclude = config('fields.client_exclude.' . $validTypeAnkets) ?? [];
                        $fields = $fields->filter(function ($field) use ($exclude) {
                            if (in_array($field, $exclude)) {
                                return false;
                            }

                            return true;
                        });
                    }

                    $medic = $medic->get();

                    return Excel::download(new AnketasExport($medic, $fields),
                                           'ЭЖ ПРМО.xlsx');
                }
            }

            if ($validTypeAnkets == 'bdd') {
                if ($request->get('exportPrikaz')) {
                    $bdd = $anketas->where('type_anketa', 'bdd')
                                   ->with(['user.roles']);

                    $fields = collect(Anketa::$fieldsKeys['bdd_export_prikaz']);
                    if ($request->user()->hasRole('client')) {
                        $bdd = $bdd->whereNotNull('date');

                        if (in_array($validTypeAnkets, ['medic', 'pechat_pl', 'bdd', 'report_cart'])) {
                            $bdd = $bdd->whereNotNull('driver_fio');
                        } else if ($validTypeAnkets === 'tech') {
                            $bdd = $bdd->whereNotNull('car_gos_number');
                        }

                        $exclude = config('fields.client_exclude.' . $validTypeAnkets) ?? [];
                        $fields = $fields->filter(function ($field) use ($exclude) {
                            if (in_array($field, $exclude)) {
                                return false;
                            }

                            return true;
                        });
                    }

                    $bdd = $bdd->get()->map(function ($q) {
                       $q->user_id = 'Инженер по безопасности дорожного движения';
                       unset($q->user);
                       return $q;
                   });

                    return Excel::download(new AnketasExport($bdd, $fields->toArray()),
                                           'ЭЖ инструктажей БДД.xlsx');
                }
            }


            $anketas = $anketas->where('type_anketa', $validTypeAnkets);

            $fields = collect(Anketa::$fieldsKeys[$validTypeAnkets]);
            if ($request->user()->hasRole('client')) {
                $anketas = $anketas->whereNotNull('date');

                if (in_array($validTypeAnkets, ['medic', 'pechat_pl', 'bdd', 'report_cart'])) {
                    $anketas = $anketas->whereNotNull('driver_fio');
                } else if ($validTypeAnkets === 'tech') {
                    $anketas = $anketas->whereNotNull('car_gos_number');
                }

                $exclude = config('fields.client_exclude.' . $validTypeAnkets) ?? [];
                $fields = $fields->filter(function ($field) use ($exclude) {
                    if (in_array($field, $exclude)) {
                        return false;
                    }

                    return true;
                });
            }

            $anketas = $anketas->get();

            return Excel::download(new AnketasExport($anketas, Anketa::$fieldsKeys[$validTypeAnkets]),
                                   'ЭЖ.xlsx');
        }


        if (auth()->user()->hasRole('client')) {
            unset($fieldsKeys['created_at']);
            unset($fieldsKeys['is_pak']);
            unset($fieldsKeys['realy']);
            unset($fieldsKeys['period_pl']);
            unset($fieldsKeys['company_id']);
            unset($fieldsKeys['is_dop']);
            unset($fieldsKeys['driver_id']);
            $fieldsKeys['id'] = true;

        }
        $table = $orderKey === 'car_type_auto' ? '' : 'anketas.';

        if ($request->user()->hasRole('client')) {
            $anketas = $anketas->whereNotNull('date');

            if (in_array($validTypeAnkets, ['medic', 'pechat_pl', 'bdd', 'report_cart'])) {
                $anketas = $anketas->whereNotNull('driver_fio');
            } else if ($validTypeAnkets === 'tech') {
                $anketas = $anketas->whereNotNull('car_gos_number');
            }
        }

        if ($filter_activated || $typeAnkets === 'pak_queue') {
            if ($orderKey === 'result_dop') {
                $anketas = $anketas->orderBy($table . $orderKey, $orderBy)->orderBy($table . 'is_dop', $orderBy === 'ASC' ? 'DESC' : 'ASC');
            } else {
                $anketas = $anketas->orderBy($table . $orderKey, $orderBy);
            }

            $anketas = $anketas->paginate($take);
            $anketasCountResult = $anketas->total();
        } else {
            $anketas = [];
            $anketasCountResult = 0;
        }


        $anketsFields = array_keys($fieldsKeys);
        if (isset(Anketa::$fieldsKeysTable[$fieldsKeysTypeAnkets])) {
            $anketsFieldsTable = Anketa::$fieldsKeysTable[$fieldsKeysTypeAnkets];
        } else {
            $anketsFieldsTable = $anketsFields;
        }


        /**
         * Check VIEW
         */
        $_view = isset($_GET['getFormFilter']) ? 'home_filters' : 'home';

        $currentRole = $validTypeAnkets;

        if ($typeAnkets === 'pak_queue' && $user->hasRole('operator_sdpo')) {
            $currentRole = 'operator_sdpo';
        }

        $fieldPrompts = FieldPrompt::where('type', $validTypeAnkets)->get();

        if ($request->user()->hasRole('client')) {
            $exclude = config('fields.client_exclude.' . $validTypeAnkets) ?? [];
            $fieldPrompts = $fieldPrompts->filter(function ($field) use ($validTypeAnkets, $exclude) {
                if (in_array($field->field, $exclude)) {
                    return false;
                }

                return true;
            });
        }

        return view($_view, [
            'title'                 => Anketa::$anketsKeys[$validTypeAnkets],
            'name'                  => $user->name,
            'ankets'                => $anketas,
            'filter_activated'      => $filter_activated,
            'type_ankets'           => $validTypeAnkets,
            'anketsFields'          => $anketsFields,
            'anketsFieldsTable'     => $anketsFieldsTable,
            'fieldsKeys'            => $fieldsKeys,
            'fieldPrompts'          => $fieldPrompts,
            'fieldsGroupFirst'      => $fieldsGroupFirst,
            'blockedToExportFields' => $blockedToExportFields,

            'anketasCountResult' => $anketasCountResult,

            'typePrikaz' => $typePrikaz,

            'currentRole' => $currentRole,

            'take'        => $take,
            'orderBy'     => $orderBy,
            'orderKey'    => $orderKey,
            'queryString' => $queryString,
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
