<?php

namespace App\Http\Controllers;

use App\Actions\Element\CreateElementHandlerFactory;
use App\Actions\Element\SyncFieldsHandler;
use App\Actions\Element\Update\UpdateElementHandlerFactory;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\LogActionTypesEnum;
use App\FieldPrompt;
use App\Imports\CarImport;
use App\Imports\CompanyImport;
use App\Imports\DriverImport;
use App\Point;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class IndexController extends Controller
{
    public $elements = [];

    private $ankets = [
        'medic' => [
            'title' => 'Медицинский осмотр',
            'anketa_view' => 'profile.ankets.medic',
        ],
        'tech' => [
            'title' => 'Технический осмотр',
            'anketa_view' => 'profile.ankets.tech',
        ],
        'pechat_pl' => [
            'title' => 'Журнал печати путевых листов',
            'anketa_view' => 'profile.ankets.pechat_pl',
        ],
        'pak' => [
            'title' => 'СДПО',
            'anketa_view' => 'profile.ankets.pak',
        ],
        'pak_queue' => [
            'title' => 'Очередь на утверждение',
            'anketa_view' => 'profile.ankets.pak_queue',
        ],
        'vid_pl' => [
            'title' => 'Реестр выданных путевых листов',
            'anketa_view' => 'profile.ankets.vid_pl',
        ],
        'bdd' => [
            'title' => 'Журнал инструктажей по БДД',
            'anketa_view' => 'profile.ankets.bdd',
        ],
        'report_cart' => [
            'title' => 'Журнал снятия отчетов с карт',
            'anketa_view' => 'profile.ankets.report_cart',
        ],
    ];

    public function __construct()
    {
        $this->elements = config('elements');
    }

    public function deprecated(Request $request): JsonResponse
    {
        Log::channel('deprecated-api')->info(json_encode(
            [
                'request' => $request->all(),
                'headers' => $request->headers->all(),
                'user' => Auth::user(),
                'ip' => $request->getClientIp() ?? null,
            ]
        ));

        return response()->json(
            ['message' => 'Метод не поддерживается, воспользуйтесь консольной командой'],
            Response::HTTP_METHOD_NOT_ALLOWED
        );
    }

    public function showVideo(): View
    {
        //TODO: это гет параметр?
        return view('showVideo', [
            'video' => $_GET['url'] ?? ''
        ]);
    }

    public function GetFieldHTML(Request $request)
    {
        try {
            $model = $request->model;
            $fieldKey = $request->field;

            if ($model === 'Point' && $fieldKey === 'pv_id') {
                return response()->json(Point::getAll());
            }

            $field = $this->elements[$model]['fields'][$fieldKey] ?? null;
            if (!$field) {
                return 'Поле не найдено';
            }

            return view('templates.elements_field', [
                'k'             => $fieldKey,
                'v'             => $field,
                'is_required'   => '',
                'model'         => $model,
                'default_value' => $request->default_value ?? 'Не установлено',
            ]);
        } catch (Throwable $exception) {
            Log::channel('deprecated-api')->info(json_encode(
                [
                    'request' => $request->all(),
                    'headers' => $request->headers->all(),
                    'user-web' => Auth::guard('web')->user(),
                    'user-api' => Auth::guard('api')->user(),
                    'user' => Auth::user(),
                    'url' => $request->url(),
                    'full-url' => $request->fullUrl(),
                    'ip' => $request->getClientIp() ?? null,
                ]
            ));

            return 'Ошибка получения поля';
        }
    }

    public function SyncDataElement(Request $request, SyncFieldsHandler $syncFieldsHandler)
    {
        $modelName = $request->model;
        $eloquentModel = app("App\\$modelName");
        $isApi = $request->get('api', 0);

        if (!$eloquentModel && $isApi) {
            return 0;
        }

        if (!$eloquentModel) {
            abort(500, 'Не найдена модель');
        }

        try {
            DB::beginTransaction();

            $updatedModelsCount = $syncFieldsHandler->handle([
                'model'          => $modelName,
                'fieldFind'      => $request->fieldFind,
                'fieldFindId'    => $request->fieldFindId,
                'fieldSync'      => $request->fieldSync,
                'fieldSyncValue' => $request->fieldSyncValue ?? '',
            ]);

            if (($updatedModelsCount ?? 0) === 0) {
                throw new Exception("Нет моделей $modelName для синхронизации");
            }

            DB::commit();

            return view('pages.success', [
                'text' => "Поля успешно синхронизированы. Кол-во элементов: $updatedModelsCount",
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            if ($isApi) {
                return 0;
            }

            return view('pages.warning', [
                'text' => $exception->getMessage(),
            ]);
        }
    }

    public function getElements()
    {
        return $this->elements;
    }

    /**
     * POST-запросы
     */
    public function ImportElements(Request $request)
    {
        $model_type = $request->type;
        $file       = $request->file('file');

        $objs = [
            'Company' => CompanyImport::class,
            'Driver'  => DriverImport::class,
            'Car'     => CarImport::class,
            'Town'    => '',
        ];

        if ($request->hasFile('file')) {
            //$file = $file->getRealPath();
            //print_r($file);

            $path1 = $request->file('file')->store('temp');
            $path  = storage_path('app').'/'.$path1;

            $data = \Maatwebsite\Excel\Facades\Excel::import(new $objs[$model_type], $path);
        }

        return redirect($_SERVER['HTTP_REFERER']);
    }

    public function AddElement(Request $request, CreateElementHandlerFactory $factory)
    {
        try {
            DB::beginTransaction();

            $data = $request->all();

            $data['files_from_request'] = $request->allFiles();

            unset($data['_token']);

            if ($request->user()->hasRole('client')) {
                $data['company_id'] = $request->user()->company_id;
            }

            $factory->make($request->type)->handle($data);

            DB::commit();

            return redirect($request->headers->get('referer'));
        } catch (Throwable $exception) {
            DB::rollBack();

            return back()->withErrors([
                'errors' => $exception->getMessage()
            ]);
        }
    }

    public function RemoveElement (Request $request)
    {
        $model = $request->type;
        $id = $request->id;
        $model = app("App\\$model");

        if ($model) {
//            if($model instanceof Company){
//                Car::where('company_id', $model->id)->update(['contract_id' => null]);
//                Driver::where('company_id', $model->id)->update(['contract_id' => null]);
//            }

            if ($request->get('undo')) {
                $model::withTrashed()->find($id)->restore();

                return redirect($_SERVER['HTTP_REFERER']);
            }
            if ($model::find($id)->delete()) {
                return redirect($_SERVER['HTTP_REFERER']);
            }
        }
    }

    public function syncElement(Request $request): RedirectResponse
    {
        try {
            $userId = Auth::id();

            if ($request->type !== 'Company') {
                return back();
            }

            $id = $request->id;
            $company = Company::find($id);

            if (!$company) {
                throw new Exception('Компания с таким ID не найдена');
            }

            $productIds = $company->products_id;
            if (!$productIds){
                return back();
            }

            DB::beginTransaction();

            foreach ([Car::class, Driver::class] as $class) {
                app($class)::query()
                    ->select([
                        'id',
                        'products_id'
                    ])
                    ->where('company_id', $id)
                    ->get()
                    ->each(function ($model) use ($productIds, $userId) {
                        $oldValue = $model->products_id;

                        if ($oldValue == $productIds) return;

                        /** @var \App\Log $log */
                        $log = Log::create([
                            'user_id' => $userId,
                            'type' => LogActionTypesEnum::UPDATING
                        ]);

                        $log->setAttribute('data', [
                            [
                                'name' => 'products_id',
                                'oldValue' => $oldValue,
                                'newValue' => $productIds
                            ]
                        ]);

                        $log->model()->associate($model);

                        $log->save();
                    });

                app($class)::where('company_id', $id)->update(['products_id' => $company->products_id]);
            }

            DB::commit();

            return back();
        } catch (Throwable $exception) {
            DB::rollBack();

            return back()->withErrors([
                'errors' => $exception->getMessage()
            ]);
        }
    }

    public function DeleteFileElement (Request $request)
    {
        $id = $request->id;
        $field = $request->field;
        $model_text = $request->model;

        $model = app("App\\$model_text");

        if($model) {
            $model = $model->find($id);

            Storage::disk('public')->delete($model->$field);

            $model->$field = '';
            $model->save();
        }

        return redirect( $_SERVER['HTTP_REFERER'] );
    }

    public function UpdateElement(Request $request, UpdateElementHandlerFactory $factory)
    {
        try {
            DB::beginTransaction();

            $handler = $factory->make($request->type);

            $data = $request->all();
            $data['files_from_request'] = $request->allFiles();

            unset($data['_token']);

            if ($request->user()->hasRole('client')) {
                $data['company_id'] = $request->user()->company_id;
            }

            $handler->handle($request->id, $data);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();

            return back()->withErrors([
                'errors' => $exception->getMessage()
            ]);
        }

        return redirect($request->headers->get('referer'));
    }

    public function showEditModal($model, $id)
    {
        $page = $this->elements[$model];

        $page['model'] = $model;
        $page['id']    = $id;


        if($model == 'Company' ||
           $model == 'Driver' ||
           $model == 'Car'){
            $el = app("App\\$model")
                ->with(['contracts.services'])
                ->find($id);
        }else{
            $el = app("App\\$model")->find($id);
        }

        $page['el'] = $el;

        echo view('showEditElementModal', $page);
    }

    /**
     * Рендеры страниц
     */
    public function RenderIndex(Request $request)
    {
        $user = Auth::user();

        if ( !$user) {
            return view('auth.login');
        }

        return redirect()->route('forms');
    }

    /**
     * Рендер просмотра вкладок CRM
     */
    public function RenderElements (Request $request)
    {
        $user = Auth::user();
        $type = $request->type;

        $queryString = '';
        $oKey = 'orderKey';
        $oBy = 'orderBy';

        // ОПЕРАТОР ПАК & КОМПАНИИ

        foreach($_GET as $getK => $getV) {
            if($getK !== $oKey && $getK !== $oBy) {
                if(is_array($getV)) {
                    foreach($getV as $getVkey => $getVvalue) {
                        $queryString .= '&' . $getK . "[$getVkey]" . '=' . $getVvalue;
                    }
                } else {
                    $queryString .= '&' . $getK . '=' . $getV;
                }

            }
        }

        /**
         * Сортировка
         */
        $orderKey = $request->get($oKey, 'created_at');
        $orderBy = $request->get($oBy, 'DESC');
        $filter = $request->get('filter', 0);

        $take = $request->get('take', 500);

        if(isset($this->elements[$type])) {
            $element = $this->elements[$type];

            $model = $element['model'];
            $MODEL_ELEMENTS = app("App\\$model");

            $element['elements_count_all'] = $MODEL_ELEMENTS->count();

            if ($model == 'Company') {
                $MODEL_ELEMENTS = $MODEL_ELEMENTS->with(['contracts.services']);
            } elseif ($model == 'Car' || $model == 'Driver') {
                $MODEL_ELEMENTS = $MODEL_ELEMENTS->with(['contracts.services']);
            }

            $element['elements'] = $MODEL_ELEMENTS;

            $element['type'] = $type;
            $element['orderBy'] = $orderBy;
            $element['orderKey'] = $orderKey;
            $element['take'] = $take;

            if($request->get('deleted')){
                $element['elements'] = $element['elements']->onlyTrashed();
            }
            if($filter) {
                $allFilters = $request->all();
                unset($allFilters['filter']);
                unset($allFilters['take']);
                unset($allFilters['orderBy']);
                unset($allFilters['orderKey']);
                unset($allFilters['page']);
                unset($allFilters['deleted']);

                foreach($allFilters as $aFk => $aFv) {
                    if(!empty($aFv)) {
                        if(is_array($aFv)) {
                            if(count($aFv) > 0) {
                                $element['elements'] = $element['elements']->where(function ($q) use ($aFv, $aFk) {
                                    $isId = strpos($aFk, '_id');

                                    foreach($aFv as $aFvItemKey => $aFvItemValue) {
                                        if ($isId && ($aFk === 'town_id' || $aFk === 'products_id')) {
                                            $q = $q->orWhere($aFk, $aFvItemValue)
                                                   ->orWhere($aFk, 'like', "%,$aFvItemValue,%")
                                                   ->orWhere($aFk, 'like', "%,$aFvItemValue")
                                                   ->orWhere($aFk, 'like', "$aFvItemValue,%");
                                        } else {
                                            if ($isId) {
                                                $q = $q->orWhere($aFk, $aFvItemValue);
                                            } else {
                                                if (strlen($aFvItemValue) === 0) {
                                                    $q = $q->orWhere($aFk, $aFvItemValue);
                                                } else {
                                                    $q = $q->orWhere($aFk, 'LIKE', '%'.trim($aFvItemValue).'%');
                                                }
                                            }
                                        }
                                    }

                                    return $q;
                                });
                            }
                        } else {
                            if ($aFk == 'date_of_employment') {
                                $element['elements'] = $element['elements']->whereBetween($aFk, [
                                    Carbon::parse($aFv)->startOfDay(),
                                    Carbon::parse($aFv)->endOfDay()
                                ]);
                            } else {
                                $element['elements'] = $element['elements']->where($aFk, 'LIKE', '%' . trim($aFv) . '%');
                            }
                        }
                    }
                }
            }

            if(auth()->user()->hasRole('client')) {
                if($model == 'Driver' || $model == 'Car' || $model == 'Company') {
                    $element['elements'] = $element['elements']->where('company_id', auth()->user()->company_id);
                } else {
                    $element['elements'] = $element['elements']->where('id', auth()->user()->company_id);
                }
            }


            $element['max'] = isset($element['max']) ? $element['max'] : null;
            $element['elements_count_all'] = $MODEL_ELEMENTS->count();
            $element['elements'] = $element['elements']->orderBy($orderKey, $orderBy);

            // Автоматическая загрузка справочников
            $excludeElementTypes = [
                'Settings',
                'Discount',
                'DDates',
                'DDate',
                'Product',
                'Instr',
                'Town',
                'Point',
                'Req',
            ];


            if ($filter || in_array($type, $excludeElementTypes)
                || ($user->hasRole('client')
                    && ($type === 'Driver'
                        || $type === 'Car'))) {
                if ($element['max']) {
                    $element['elements'] = $element['elements']->take($element['max'])->get();
                } else {
                    $element['elements'] = $element['elements']->paginate($take);
                }
            } else {
                $element['elements'] = [];
            }

            // Проверка прав доступа
            $roles = ['manager', 'admin'];
            if (isset($element['otherRoles'])) {
                foreach ($roles as $roleOther) {
                    array_push($element['otherRoles'], $roleOther);
                }
            } else {
                $element['otherRoles'] = $roles;
            }

            $element['queryString'] = $queryString;
            $fieldsQuery = FieldPrompt::where('type', strtolower($model));
            $element['fieldPrompts'] = $fieldsQuery->get();
            return view('elements', $element);
        } else {
            return redirect( route('home') );
        }
    }

    /**
     * Рендер последовательного добавления Клиента
     */
    public function RenderAddClient (Request $request)
    {
        return view('pages.add_client', [
            'title' => 'Добавление клиента'
        ]);
    }

    public function RenderHome()
    {
        return view('index');
    }

    /**
     * Рендер анкет
     */
    public function RenderForms (Request $request)
    {
        $user = Auth::user();

        if(!($type = $request->get('type'))){
            if(user()->hasRole('tech')){
                $type = 'tech';
            }
            if(user()->hasRole('medic')){
                $type = 'medic';
            }
            if(user()->hasRole('manager') || user()->hasRole('engineer_bdd')){
                return redirect()->route('renderElements', 'Company');
            }
            if(user()->hasRole('operator_sdpo')){
                return redirect()->route('home', 'pak_queue');
            }

            if(user()->hasRole('client')){
                return redirect()->route('home', ['type_ankets' => 'medic']);
            }
            if(!$type){
                return redirect()->route('index');
            }
        }

        $company_fields = $this->elements['Driver']['fields']['company_id'];
        $company_fields['getFieldKey'] = 'name';

        $anketa_key = $type;

        // Отображаем данные
        $anketa = $this->ankets[$anketa_key];
        $points = Point::getAll();

        // Конвертация текущего времени Юзера
        date_default_timezone_set('UTC');

        $time = time();

        $timezone = $user->timezone ? $user->timezone : 3;

        $time += $timezone * 3600;
        $time = date('Y-m-d\TH:i', $time);

        // Дефолтные значения
        $anketa['default_current_date'] = $time;
        $anketa['points'] = $points;
        $anketa['type_anketa'] = $anketa_key;
        $anketa['default_pv_id'] = $user->pv_id;
        $anketa['company_fields'] = $company_fields;

        $anketa['Driver'] = Driver::class;
        $anketa['Car'] = Car::class;

        // Проверяем выставленный ПВ
        if(session()->exists('anketa_pv_id')) {
            $session_pv_id = session('anketa_pv_id');

            if(date('d.m') > $session_pv_id['expired']) {
                session()->remove('anketa_pv_id');
            }
        }

        return view('profile.anketa', $anketa);
    }

    public function agreement(Request $request) {
        return view('agreement.index');
    }

    public function acceptAgreement(Request $request) {
        $request->user()->update([
            'accepted_agreement' => true
        ]);

        return back();
    }
}
