<?php

namespace App\Http\Controllers;

use App\Actions\Element\CreateElementHandlerFactory;
use App\Actions\Element\Remove\RemoveElementHandlerFactory;
use App\Actions\Element\SyncFieldsHandler;
use App\Actions\Element\Update\UpdateElementHandlerFactory;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\LogActionTypesEnum;
use App\FieldPrompt;
use App\Point;
use App\User;
use App\ValueObjects\CompanyReqs;
use Carbon\Carbon;
use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Throwable;

class IndexController extends Controller
{
    public $elements = [];

    public function __construct()
    {
        $this->elements = config('elements');
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

            $noRequired = filter_var($field['noRequired'] ?? 0, FILTER_VALIDATE_BOOLEAN);

            return view('templates.elements_field', [
                'k' => $fieldKey,
                'v' => $field,
                'is_required' => $noRequired ? '' : 'required',
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

    public function RemoveElement(Request $request, RemoveElementHandlerFactory $factory): RedirectResponse
    {
        try {
            $handler = $factory->make($request->type);

            DB::beginTransaction();

            $handler->handle($request->id, !$request->undo);

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

        $element = $query->find($id);

        $page = $this->elements[$model];
        $page['model'] = $model;
        $page['id'] = $id;
        $page['el'] = $element;

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

        /** @var Model|null $element */
        if (($model === 'Company') && $element->getAttribute('reqs_validated')) {
            $disabledFields[] = 'inn';
            $disabledFields[] = 'kpp';
            $disabledFields[] = 'ogrn';

            $companyReqs = new CompanyReqs(
                $element->getAttribute('inn'),
            $element->getAttribute('kpp') ?? '',
                $element->getAttribute('ogrn') ?? '',
            );

            if ($companyReqs->isOrganizationFormat()) {
                $disabledFields[] = 'official_name';
            }
        }

        /** @var Model|null $element */
        if (($model === 'Company') && !user()->access('companies_access_field_note')) {
            $disabledFields[] = 'note';
        }

        $page['disabledFields'] = $disabledFields;

        $fieldsToSkip = [
            'essence',
            'hash_id',
            'id',
            'reqs_validated',
            'one_c_synced'
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

        $dateConditions = [];
        if (in_array($type, ['Driver', 'Car', 'Company'])) {
            $dateFields = array_keys(array_filter($data['fields'], function ($item) {
                return isset($item['type']) && $item['type'] === 'date';
            }));

            $dateConditions = array_reduce($dateFields, function (array $carry, string $fieldName) {
                $carry[$fieldName . '_start'] = [$fieldName, '>='];
                $carry[$fieldName . '_end'] = [$fieldName, '<='];

                return $carry;
            }, []);
        }

        $pressureConditions = [
            'pressure_systolic_min' => ['pressure_systolic', '>='],
            'pressure_systolic_max' => ['pressure_systolic', '<='],
            'pressure_diastolic_min' => ['pressure_diastolic', '>='],
            'pressure_diastolic_max' => ['pressure_diastolic', '<='],
        ];

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
                    if (isset($dateConditions[$filterKey])) {
                        $conditions = $dateConditions[$filterKey];

                        $value = Carbon::parse($filterValue);

                        if ($conditions[1] === '>=') {
                            $value = $value->startOfDay();
                        } else if ($conditions[1] === '<=') {
                            $value = $value->endOfDay();
                        }

                        $query->whereNotNull($conditions[0]);
                        $query->where($conditions[0], $conditions[1], $value);

                        continue;
                    }

                    if (isset($pressureConditions[$filterKey])) {
                        $conditions = $pressureConditions[$filterKey];

                        $query->whereNotNull($conditions[0]);
                        $query->where($conditions[0], $conditions[1], $filterValue);
                        continue;
                    }

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
            $companyIdField = null;

            if ($model == 'Company') {
                $companyIdField = 'id';
            }

            if ($model == 'Driver' || $model == 'Car') {
                $companyIdField = 'company_id';
            }

            if ($companyIdField) {
                $query = $query->where($companyIdField, $user->company_id);
            }
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
        $data['queryString'] = Arr::query(array_filter($request->except([$oKey, $oBy])));
        $data['fieldPrompts'] = FieldPrompt::query()
            ->where('type', strtolower($model))
            ->orderBy('sort')
            ->orderBy('id')
            ->get();
        $data['isAdminOrClient'] = $isAdminOrClient;

        return view('pages.elements.index', $data);
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

    public function index()
    {
        /** @var User $user */
        $user = Auth::user();

        if ($user && $user->hasRole('driver')) {
            return redirect(route('driver.index'));
        }

        return view('index');
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
