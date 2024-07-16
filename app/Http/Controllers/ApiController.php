<?php

namespace App\Http\Controllers;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ApiController extends Controller
{
    public function modelList(Request $request, $model)
    {
        $mainContentFields = [
            "Company" => "name",
            "Driver" => "fio",
            "Car" => "gos_number",
            "Product" => "name",
            "Instr" => "name"
        ];

        $field = 'name';
        $key = 'id';
        $user = $request->user('api');

        if ($request->get('field')) {
            $field = $request->get('field');
        }

        $searchingIn = $mainContentFields[$model] ?? $field;

        if ($request->get('key')) {
            $key = $request->key;
        }

        $query = app("App\\" . $model)::query()
            ->where(function ($subQuery) use ($request, $searchingIn) {
                $subQuery
                    ->where($searchingIn, 'like', "%$request->search%")
                    //TODO: в контрактах нет HASH_ID
                    ->orWhere("hash_id", "like", "%$request->search%");
            })
            ->select([
                'id',
                'hash_id',
                $field,
                $key
            ]);

        if ($model === 'User') {
            $query = $query
                ->with(['roles'])
                ->whereHas('roles', function ($subQuery) use ($request) {
                    $subQuery->whereNotIn('roles.id', [3, 6, 9]);
                });
        }

        if ($user->hasRole('client') && ($model === 'Driver' || $model === 'Car')) {
            $query = $query->where('company_id', $user->company_id);
        }

        if ($request->get('trashed') === 'true') {
            $query = $query->withTrashed();
        }

        return $query->limit(100)->get();
    }

    public function companiesList(Request $request)
    {
        $company = Company::query()
            ->where('name', 'like', "%$request->search%")
            ->orWhere('hash_id', 'like', "%$request->search%")
            ->orWhere('inn', 'like', "%$request->search%")
            ->select([
                'hash_id',
                'name',
                'id',
                'inn'
            ])
            ->limit(100);

        if ($request->get('trashed') === 'true') {
            $company = $company->withTrashed();
        }

        return $company->get();
    }

    public function ResetAllPV()
    {
        $users = User::all();

        date_default_timezone_set('UTC');

        foreach ($users as $user) {
            $time = time();

            $timezone = $user->timezone ?: 3;

            $time += $timezone * 3600;
            $time = explode(':', date('H:i', $time));

            if ($time[0] === '00' && $time[1] === '00') {
                $user->update(['pv_id' => $user->pv_id_default]);
            }
        }
    }

    public function UpdateProperty(Request $request): JsonResponse
    {
        //TODO: обернуть в транзакцию
        $modelClass = app("App\\$request->item_model");
        if (!$modelClass) {
            return ApiController::r(['exists' => false, 'data' => [], 'message' => 'Значение не обновлено'], 0);
        }

        $user = $request->user();
        /**
         * Если это компания, у пользователя нет права ее обновления, а также у пользователя нет роли Медик или Тех
         * или это любое поле отличное от link_waybill
         */
        $deprecatedToUpdate =
            ($request->item_model === 'Company') &&
            !$user->access('company_update') &&
            (!$user->hasRole('medic') && !$user->hasRole('tech') || $request->item_field !== 'link_waybill');
        if ($deprecatedToUpdate) {
            return ApiController::r(['exists' => false, 'data' => [], 'message' => 'Значение не обновлено'], 0);
        }

        $model = $modelClass->find($request->item_id);
        if (!$model) {
            return ApiController::r(['exists' => false, 'data' => [], 'message' => 'Значение не обновлено'], 0);
        }

        $modelField = str_replace('[]', '', $request->item_field);
        $newValue = $request->get('new_value');
        $model[$modelField] = is_array($newValue) ? join(',', $newValue) : $newValue;

        if ($model->save()) {
            return ApiController::r(['exists' => true, 'data' => $model, 'message' => 'Значение обновлено']);
        }

        return ApiController::r(['exists' => true, 'data' => $model, 'message' => 'Значение не обновлено'], 0);
    }

    public static function r($data = [], $action = 1)
    {
        $response = [];

        switch (strtoupper(trim($action))) {
            case 1: // SUCCESS ROUTE
                $response = ['success' => 1, 'error' => 0, 'data' => $data];
                break;

            case 0: // FAIL ROUTE
                $response = ['success' => 0, 'error' => 1, 'data' => $data];
                break;
        }

        return response()->json($response);
    }

    public function CheckProperty(Request $request): JsonResponse
    {
        $prop = $request->prop;
        $model = $request->model;
        $val = $request->val;

        $modelsMap = [
            'Car' => [
                'model' => Car::class,
                'fields' => ['hash_id', 'mark_model', 'gos_number', 'company_id']
            ],
            'Driver' => [
                'model' => Driver::class,
                'fields' => ['hash_id', 'fio', 'company_id']
            ],
            'Company' => [
                'model' => Company::class,
                'fields' => ['id', 'name', 'inn', 'payment_form']
            ]
        ];

        if (!isset($modelsMap[$model]) || empty($val)) {
            return ApiController::r(['exists' => false, 'message' => '', 'model' => $model], 0);
        }

        $modelMap = $modelsMap[$model];
        $modelClass = app($modelMap['model']);

        $query = $modelClass::query();

        $attachServices = in_array($modelMap['model'], [
            Company::class,
            Driver::class,
            Car::class
        ]);

        if ($attachServices) {
            $query = $query->with(['contracts.services']);
        }

        $fields = $modelClass->fillable;
        $fields[] = 'id';
        $fieldInputs = config('elements')[$model]['fields'];

        if ($modelMap['model'] == Company::class) {
            if (!user()->access('companies_access_field_where_call')) {
                unset($fields['where_call']);
                unset($fieldInputs['where_call']);
            }
            if (!user()->access('companies_access_field_where_call_name')) {
                unset($fields['where_call_name']);
                unset($fieldInputs['where_call_name']);
            }
        }

        $existModel = $query->where($prop, $val)
            ->select($fields)
            ->first();

        if (!$existModel) {
            return ApiController::r([
                'exists' => false,
                'model' => $model,
                'blockedFields' => [],
                'message' => null,
                'fieldsValues' => $fieldInputs,
                'redDates' => []
            ]);
        }

        $existModel = $existModel->toArray();

        /**
         * Контроль дат
         */
        $redDates = [];
        if ($dateForm = $request->get('dateAnketa', '')) {
            $redDates = AnketsController::ddateCheck($dateForm, $model, $existModel['id']);
        }

        if ($company = Company::select('name', 'hash_id')->find($existModel['company_id'] ?? 0)) {
            $existModel['company_name'] = $company->name;
            $existModel['company_hash_id'] = $company->hash_id;
        }

        if (isset($existModel['date_of_employment'])) {
            $existModel['date_of_employment'] = Carbon::parse($existModel['date_of_employment'])->format('Y-m-d');
        }

        $blockedFields = [
            'old_id',
            'req_id',
            'inn',
            'date_bdd',
            'date_report_driver',
            'contracts',
            'contract_id'
        ];

        if (($modelMap['model'] === Company::class) && !user()->access('company_update_pressure_fields')) {
            $blockedFields[] = 'pressure_systolic';
            $blockedFields[] = 'pressure_diastolic';
        }

        if (($modelMap['model'] === Driver::class) && !user()->access('drivers_update_pressure_fields')) {
            $blockedFields[] = 'pressure_systolic';
            $blockedFields[] = 'pressure_diastolic';
        }

        return ApiController::r([
            'exists' => true,
            'model' => $model,
            'blockedFields' => $blockedFields,
            'message' => $existModel,
            'fieldsValues' => $fieldInputs,
            'redDates' => $redDates
        ]);
    }

    public function OneCheckProperty($prop, $model, $val)
    {
        $modelClass = app("App\\$model");

        if (!$modelClass) {
            return response([
                'status' => false,
            ]);
        }

        $existModel = $modelClass::query()
            ->where($prop, $val)
            ->first();

        if ($existModel) {
            return response([
                'status' => true,
                'name' => $existModel->fio ?? $existModel->name,
            ]);
        }

        return response([
            'status' => false,
        ]);
    }

    /**
     * Сохранение перечня фильтруемых полей у пользователя
     */
    public function saveFieldsVisible(Request $request)
    {
        $user = $request->user();
        $user->fields_visible = null;

        if ($request->params && $request->params !== 'null') {
            $user->fields_visible = json_encode($request->params);
        }

        $user->save();
    }

    public function getPreviousOdometer(Request $request): JsonResponse
    {
        //TODO: добавить валидацию
        $carHashId = $request->input('car_id');
        $date = $request->input('date');

        $nearestTechForm = Anketa::query()
            ->where('type_anketa', FormTypeEnum::TECH)
            ->where('car_id', $carHashId)
            ->whereDate('date', '<=', Carbon::parse($date))
            ->whereNotNull('odometer')
            ->orderBy('date', 'DESC')
            ->first();

        if (!$nearestTechForm) {
            return response()->json(['message' => 'Предыдущих осмотров с таким ID автомобиля - не найдено']);
        }

        $message = sprintf(
            '%s (%s)',
            $nearestTechForm->getAttribute('odometer'),
            $nearestTechForm->getAttribute('date')
        );

        return response()->json(['message' => $message]);
    }
}
