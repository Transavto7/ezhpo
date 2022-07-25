<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Exports\AnketasExport;
use App\User;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
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
     * @return \Illuminate\Contracts\Support\Renderable
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

    public static function setSessionForField()
    {
        session([
            "fields_medic" => [
                'date'       => 'on',
                'driver_fio' => 'on',
                'period_pl' => 'on',
                'created_at' => 'on',
                'driver_group_risk' => 'on',
                'type_view' => 'on',
                'realy' => 'on',
                'proba_alko' => 'on',
                'test_narko' => 'on'
            ],
        ]);
        session([
            "fields_tech" => [
                'date'           => 'on',
                'car_gos_number' => 'on',
                'period_pl' => 'on',
                'created_at'     => 'on',
                'car_mark_model' => 'on',
                'type_view' => 'on',
                'realy' => 'on',
            ],
        ]);

        session([
            "fields_bdd" => [
                'date'           => 'on',
                'driver_fio'     => 'on',
                'type_briefing' => 'on',
                'company_id'    => 'on',
                'created_at'    => 'on',
                'user_name'     => 'on',
            ],
        ]);

        session([
            "fields_pechat_pl" => [
                'date'           => 'on',
                'driver_fio'     => 'on',
                'count_pl' => 'on',
                'company_id'    => 'on',
                'user_name'     => 'on',
                'pv_id'    => 'on',
            ],
        ]);

        session([
            "fields_report_cart" => [
                'date'           => 'on',
                'driver_fio'     => 'on',
                'company_id'    => 'on',
                'user_name'     => 'on',
            ],
        ]);
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

        $user = \Auth::user();

        $validTypeAnkets       = User::$userRolesKeys[$user->role];
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
        if ($typePrikaz === 'Dop' || ( !isset($_GET['getFormFilter']) && $request->get('exportPrikazPL'))) {
            $fieldsKeysTypeAnkets = 'Dop_prikaz';
        } else {
            $fieldsKeysTypeAnkets = $validTypeAnkets;
        }

        // Экспорты новые писать тут всё
        if ($request->get('export')) {
            if ($typeAnkets == 'tech') {
                if ($request->get('exportPrikaz')) {
                    $fieldsKeysTypeAnkets = 'tech_export_to'; // экспорт по приказу ТО
                } elseif ($request->get('exportPrikazPL')) {
                    $fieldsKeysTypeAnkets = 'tech_export_pl';// экспорт по приказу ПЛ
                } else {
                    $fieldsKeysTypeAnkets = 'tech';// экспорт просто
                }
            } elseif ($typeAnkets == 'bdd') {
                if ($request->get('exportPrikaz')) {
                    $fieldsKeysTypeAnkets = 'bdd_export_prikaz';// экспорт по приказу

                } else {
                    $fieldsKeysTypeAnkets = 'bdd';// экспорт просто
                }
            }
        }

//        dd($fieldsKeysTypeAnkets);
        $fieldsKeys       = Anketa::$fieldsKeys[$fieldsKeysTypeAnkets];
        $fieldsGroupFirst = isset(Anketa::$fieldsGroupFirst[$fieldsKeysTypeAnkets])
            ? Anketa::$fieldsGroupFirst[$fieldsKeysTypeAnkets] : [];

        if ($typePrikaz === 'Dop' || $request->get('export')) {
            $take = 10000;
        }

        /**
         * Очистка корзины в очереди на утверждение от СДПО
         */
        if (isset($_GET['clear']) && isset($_GET['type_anketa']) && $user->hasRole('admin', '==')) {
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
            foreach ($filter_params as $fk => $fv) {

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
                    $date_from = Carbon::parse($fv)->startOfDay() ?? Carbon::now()->subYears(2);
                    $date_to   = Carbon::parse($filter_params['TO_date'])->endOfDay() ?? Carbon::now();

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
                    $anketas = $anketas->where('created_at', '>=', Carbon::parse($fv)->startOfDay());
                    continue;
                }
                if ($fk == 'TO_created_at' && $fv) {
                    $anketas = $anketas->where('created_at', '<=', Carbon::parse($fv)->endOfDay());
                    continue;
                }

                if ((in_array($fk, $anketasModel->fillable))) {
                    // Поиск по дефолтным полям в таблице Anketas

                    // Проверяем пустые поля
                    if ( !empty($fv)) {

                        if ($fk !== 'date' && $fk !== 'created_at') {
                            $explodeData = is_array($fv) ? $fv : explode(',', $fv);
                            $explodeData = (count($explodeData) == 1) ? $explodeData[0] : $explodeData;

                            if (is_array($explodeData)) {

                                $anketas = $anketas->where(function ($q) use ($explodeData, $fk) {

                                    foreach ($explodeData as $fvItemKey => $fvItemValue) {
                                        $q = $q->orWhere($fk, $fvItemValue); // TODO: поправили Like
                                    }

                                    return $q;
                                });

                            } else {
                                /**
                                 * Проверяем что данные есть (повлияло на ФЛАГ СДПО)
                                 */

                                if ($explodeData) {
                                    // Для строгих значений
                                    if (in_array($fk, ['company_name', 'driver_fio']) || strpos($fk, '_id')
                                        || $fk === 'id') {
                                        $anketas = $anketas->where($fk, $explodeData);
                                    } // Для динамичных значений
                                    else {
                                        $anketas = $anketas->where($fk, 'LIKE', '%'.$explodeData.'%');
                                    }
                                } else {
                                    if ($explodeData === null) {
                                        $anketas = $anketas->where($fk, null);
                                    }
                                }
                            }
                        }
//                        elseif(){
//
//                        }
                    }
                } else {
                    if ( !empty($fv)) {
                        $anketas = $anketas->where($fk, 'LIKE', '%'.$fv.'%');
                    }
                }
            }

//            dd($anketas->get(), $anketas->dd());
        }
//        dump($fromToValues);
//        dd($anketas->toSql());
//        dd($anketas->limit(20)->get()->toArray());
//        $anketas->limit(20);

        if (auth()->user()->hasRole('client', '==')) {
            $company_id_client = User::getUserCompanyId('hash_id');

            $anketas = $anketas->where('company_id', $company_id_client);
        }

        $anketas = $anketas->where('type_anketa', $validTypeAnkets)->where('in_cart', $trash);

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

        /**
         * </Измеряем количество Авто и Водителей (уникальные ID)>
         */

        // Экспорт из техосмотров и БДД
        if ($request->get('export') && ($typeAnkets == 'tech' || $typeAnkets == 'bdd')) {
            $request->request->remove('exportPrikazPL');
            $request->request->remove('exportPrikaz');
            $request->request->remove('export');

            if ($typeAnkets == 'bdd' && $fieldsKeysTypeAnkets == 'bdd_export_prikaz') {
                // Тут надо получать должность
                $collection = $anketas->orderBy($orderKey, $orderBy)
                                      ->limit(50000)
                                      ->with([
                                          'user' => function ($q) {
                                              $q->select('id', 'role');
                                          },
                                      ])
                                      ->select(array_keys($fieldsKeys))
                                      ->get()
                                      ->map(function ($q) {
                                          $q->user_id = User::$userRolesText[$q->user['role']] ?? null;
                                          unset($q->user);

                                          return $q;
                                      });


            } else {
                $collection = $anketas->orderBy($orderKey, $orderBy)
                                      ->limit(50000)
                                      ->get(array_keys($fieldsKeys));
            }

            $collection->prepend(array_values($fieldsKeys));

            return (new AnketasExport($collection))->download('export-anketas.xlsx');
        }

//        dd($anketas->toSql(), $anketas->getBindings());

        $anketas = ($filter_activated || $typeAnkets === 'pak_queue')
            ? $anketas->orderBy($orderKey, $orderBy)->paginate($take) : [];

        $anketasCountResult = ($filter_activated || $typeAnkets === 'pak_queue')
            ? $anketas->total() : 0;

        $anketsFields = array_keys($fieldsKeys);

        if (auth()->user()->hasRole('client', '==')) {
            unset($fieldsKeys['created_at']);
            unset($fieldsKeys['is_pak']);
        }
        /**
         * Check Export
         */
        /*if($is_export) {
            return (new AnketasExport($anketasArray))->download('export-anketas.xlsx');
        }*/

        /**
         * Check VIEW
         */
        $_view = isset($_GET['getFormFilter']) ? 'home_filters' : 'home';

        $currentRole = $validTypeAnkets === 'Dop' ? 'medic' : $validTypeAnkets;

        if ($typeAnkets === 'pak_queue' && $user->hasRole('operator_pak', '==')) {
            $currentRole = 'operator_pak';
        }


        return view($_view, [
            'title'                 => Anketa::$anketsKeys[$validTypeAnkets],
            'name'                  => $user->name,
            'ankets'                => $anketas,
            'filter_activated'      => $filter_activated,
            'type_ankets'           => $validTypeAnkets,
            'anketsFields'          => $anketsFields,
            'fieldsKeys'            => $fieldsKeys,
            'fieldsGroupFirst'      => $fieldsGroupFirst,
            'blockedToExportFields' => $blockedToExportFields,

            'anketasCountResult' => $anketasCountResult,

            'isExport'   => $is_export,
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
        if ($typePrikaz === 'Dop' || ( !isset($_GET['getFormFilter']) && $request->get('exportPrikazPL'))) {
            $fieldsKeysTypeAnkets = 'Dop_prikaz';
        } else {
            $fieldsKeysTypeAnkets = $validTypeAnkets;
        }

        $fieldsKeys       = Anketa::$fieldsKeys[$fieldsKeysTypeAnkets];
        $fieldsGroupFirst = isset(Anketa::$fieldsGroupFirst[$fieldsKeysTypeAnkets])
            ? Anketa::$fieldsGroupFirst[$fieldsKeysTypeAnkets] : [];
        $anketsFields     = array_keys($fieldsKeys);

        if (auth()->user()->hasRole('client', '==')) {
            unset($fieldsKeys['created_at']);
            unset($fieldsKeys['is_pak']);
        }

        return view('home_filters', [
            'anketsFields'     => $anketsFields,
            'type_ankets'      => $validTypeAnkets,
            'fieldsKeys'       => $fieldsKeys,
            'fieldsGroupFirst' => $fieldsGroupFirst,
        ]);
    }
}
