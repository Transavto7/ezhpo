<?php

namespace App\Http\Controllers;

use App\Actions\Element\CreateElementHandlerFactory;
use App\Actions\Element\SyncFieldsHandler;
use App\Actions\Element\Update\UpdateElementHandlerFactory;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\LogActionTypesEnum;
use App\Enums\QRCodeLinkParameter;
use App\FieldPrompt;
use App\Point;
use App\Services\QRCode\QRCodeLinkGenerator;
use App\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
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

    public function showVideo(Request $request): View
    {
        return view('showVideo', [
            'video' => $request->input('url', '')
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
                'k' => $fieldKey,
                'v' => $field,
                'is_required' => '',
                'model' => $model,
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
                'model' => $modelName,
                'fieldFind' => $request->fieldFind,
                'fieldFindId' => $request->fieldFindId,
                'fieldSync' => $request->fieldSync,
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

    public function RemoveElement(Request $request): RedirectResponse
    {
        try {
            $model = $request->type;
            $id = $request->id;

            $modelClass = app("App\\$model");
            if (!$modelClass) {
                throw new Exception("Модель $model не найдена");
            }

            $existModel = $modelClass::withTrashed()->find($id);
            if (!$existModel) {
                throw new Exception("Модель $model с ID $id не найдена");
            }

            DB::beginTransaction();

            if ($request->get('undo')) {
                $existModel->restore();
            } else {
                $existModel->delete();
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
            if (!$productIds) {
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

    public function DeleteFileElement(Request $request): RedirectResponse
    {
        $id = $request->id;
        $field = $request->field;
        $model_text = $request->model;

        $model = app("App\\$model_text");

        if ($model) {
            $model = $model->find($id);

            Storage::disk('public')->delete($model->$field);

            $model->$field = '';
            $model->save();
        }

        return back();
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
        $modelClass = app("App\\$model");
        $query = $modelClass::query();

        $attachServices = in_array($modelClass, [
            Company::class,
            Driver::class,
            Car::class
        ]);
        if ($attachServices) {
            $query = $query->with(['contracts.services']);
        }

        $page = $this->elements[$model];
        $page['model'] = $model;
        $page['id'] = $id;
        $page['el'] = $query->find($id);

        $disabledFields = [];
        if (($model === 'Company') && (user()->hasRole('client') || !user()->access('company_update_pressure_fields'))) {
            $disabledFields[] = 'pressure_systolic';
            $disabledFields[] = 'pressure_diastolic';
        }
        if (($model === 'Driver') && (user()->hasRole('client') || !user()->access('drivers_update_pressure_fields'))) {
            $disabledFields[] = 'pressure_systolic';
            $disabledFields[] = 'pressure_diastolic';
        }
        if (($model === 'Car') && user()->hasRole('client')) {
            $disabledFields[] = 'note';
            $disabledFields[] = 'procedure_pv';
        }

        if (($model === 'Driver') && user()->hasRole('client')) {
            $disabledFields[] = 'note';
            $disabledFields[] = 'procedure_pv';
            $disabledFields[] = 'group_risk';
            $disabledFields[] = 'only_offline_medic_inspections';
            $disabledFields[] = 'pressure_systolic';
            $disabledFields[] = 'pressure_diastolic';
        }
        $page['disabledFields'] = $disabledFields;

        $fieldsToSkip = [
            'essence',
            'hash_id',
            'id'
        ];
        if (user()->hasRole('client')) {
            $fieldsToSkip[] = 'products_id';
        }
        if (!user()->access('companies_access_field_where_call_name')) {
            $fieldsToSkip[] = 'where_call_name';
        }
        if (!user()->access('companies_access_field_where_call')) {
            $fieldsToSkip[] = 'where_call';
        }
        if ($model === 'Instr') {
            $fieldsToSkip[] = 'signature';
        }
        $page['fieldsToSkip'] = $fieldsToSkip;

        echo view('showEditElementModal', $page);
    }

    /**
     * Рендеры страниц
     */
    public function RenderIndex(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return view('auth.login');
        }

        return redirect()->route('forms');
    }

    /**
     * Рендер просмотра вкладок CRM
     */
    public function RenderElements(Request $request)
    {
        $isAdminOrClient = (Auth::user()->hasRole('admin') || Auth::user()->hasRole('client'));
        $type = $request->type;
        if (!isset($this->elements[$type])) {
            return redirect(route('home'));
        }

        $oKey = 'orderKey';
        $oBy = 'orderBy';


        $data = $this->elements[$type];

        $model = $data['model'];
        $modelClass = app("App\\$model");

        $query = $modelClass::query();

        $attachServices = in_array($modelClass, [
            Company::class,
            Driver::class,
            Car::class
        ]);
        if ($attachServices) {
            $query = $query->with(['contracts.services']);
        }

        if ($request->get('deleted')) {
            $query = $query->onlyTrashed();
        }

        $filter = $request->get('filter', 0);
        if ($filter) {
            $filters = $request->except([
                'filter',
                'take',
                'orderBy',
                'orderKey',
                'page',
                'deleted'
            ]);

            foreach ($filters as $filterKey => $filterValue) {
                if (empty($filterValue)) {
                    continue;
                }

                if (!is_array($filterValue)) {
                    if ($filterKey == 'date_of_employment') {
                        $query = $query->whereBetween($filterKey, [
                            Carbon::parse($filterValue)->startOfDay(),
                            Carbon::parse($filterValue)->endOfDay()
                        ]);

                        continue;
                    }

                    $query = $query->where($filterKey, 'LIKE', '%' . trim($filterValue) . '%');

                    continue;
                }

                if (count($filterValue) === 0) {
                    continue;
                }

                $query = $query->where(function ($subQuery) use ($filterValue, $filterKey) {
                    foreach ($filterValue as $filterValueItem) {
                        if ($filterKey === 'town_id' || $filterKey === 'products_id') {
                            $subQuery = $subQuery->orWhere($filterKey, $filterValueItem)
                                ->orWhere($filterKey, 'like', "%,$filterValueItem,%")
                                ->orWhere($filterKey, 'like', "%,$filterValueItem")
                                ->orWhere($filterKey, 'like', "$filterValueItem,%");
                        } else if (strpos($filterKey, '_id')) {
                            $subQuery = $subQuery->orWhere($filterKey, $filterValueItem);
                        } else if (strlen($filterValueItem) === 0) {
                            //TODO: странный фильтр только на пустую строку
                            $subQuery = $subQuery->orWhere($filterKey, $filterValueItem);
                        } else {
                            $subQuery = $subQuery->orWhere($filterKey, 'LIKE', '%' . trim($filterValueItem) . '%');
                        }
                    }

                    return $subQuery;
                });
            }
        }

        /** @var User $user */
        $user = Auth::user();
        if ($user->hasRole('client')) {
            $companyIdField = 'id';

            //TODO: может странно работать на компании
            if ($model == 'Driver' || $model == 'Car' || $model == 'Company') {
                $companyIdField = 'company_id';
            }

            $query = $query->where($companyIdField, $user->company_id);
        }

        $orderKey = $request->get($oKey, 'created_at');
        $orderBy = $request->get($oBy, 'DESC');
        $query = $query->orderBy($orderKey, $orderBy);

        $loadWithoutFiltersElementTypes = [
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

        $loadWithoutFiltersElementTypesForClients = [
            'Driver',
            'Car'
        ];

        $take = $request->get('take', 500);

        $elements = [];
        $loadElements = $filter
            || in_array($type, $loadWithoutFiltersElementTypes)
            || $user->hasRole('client') && in_array($type, $loadWithoutFiltersElementTypesForClients);
        if ($loadElements) {
            if (isset($data['max'])) {
                $elements = $query->take($data['max'])->get();
            } else {
                $elements = $query->paginate($take);
            }
        }

        $data['elements'] = $elements;
        $data['type'] = $type;
        $data['orderBy'] = $orderBy;
        $data['orderKey'] = $orderKey;
        $data['take'] = $take;
        $data['max'] = $data['max'] ?? null;
        $data['elements_count_all'] = $query->count();
        $data['otherRoles'] = $data['otherRoles'] ?? [];
        $data['otherRoles'][] = 'manager';
        $data['otherRoles'][] = 'admin';
        $data['queryString'] = Arr::query($request->except([$oKey, $oBy]));;
        $data['fieldPrompts'] = FieldPrompt::where('type', strtolower($model))->get();
        $data['isAdminOrClient'] = $isAdminOrClient;

        return view('elements', $data);
    }

    /**
     * Рендер последовательного добавления Клиента
     */
    public function RenderAddClient()
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
    public function RenderForms(Request $request)
    {
        $user = Auth::user();

        $type = $request->get('type');
        if (!$type) {
            if ($user->hasRole('tech')) {
                $type = 'tech';
            }
            if ($user->hasRole('medic')) {
                $type = 'medic';
            }
            if ($user->hasRole('manager') || $user->hasRole('engineer_bdd')) {
                return redirect()->route('renderElements', 'Company');
            }
            if ($user->hasRole('operator_sdpo')) {
                return redirect()->route('home', 'pak_queue');
            }

            if ($user->hasRole('client')) {
                return redirect()->route('home', ['type_ankets' => 'medic']);
            }
            if (!$type) {
                return redirect()->route('index');
            }
        }

        $companyFields = $this->elements['Driver']['fields']['company_id'];
        $companyFields['getFieldKey'] = 'name';

        // Отображаем данные
        $data = $this->ankets[$type];

        // Конвертация текущего времени Юзера
        date_default_timezone_set('UTC');
        $time = time();
        $timezone = $user->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d\TH:i', $time);

        // Дефолтные значения
        $data['default_current_date'] = $time;
        $data['points'] = Point::getAll();
        $data['type_anketa'] = $type;
        $data['default_pv_id'] = $user->pv_id;
        $data['company_fields'] = $companyFields;
        $data['Driver'] = Driver::class;
        $data['Car'] = Car::class;
        $data['params'] = [
            'carId' => $request->input('carId'),
            'driverId' => $request->input('driverId'),
        ];

        // Проверяем выставленный ПВ
        if (session()->exists('anketa_pv_id')) {
            if (date('d.m') > session('anketa_pv_id')['expired']) {
                session()->remove('anketa_pv_id');
            }
        }

        return view('profile.anketa', $data);
    }

    public function agreement()
    {
        return view('agreement.index');
    }

    public function acceptAgreement(Request $request): RedirectResponse
    {
        try {
            DB::beginTransaction();

            $request->user()->update([
                'accepted_agreement' => true
            ]);

            DB::commit();
        } catch (Throwable $exception) {
            DB::rollBack();
        } finally {
            return back();
        }
    }
}
