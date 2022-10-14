<?php

namespace App\Http\Controllers;

use App\Company;
use App\Req;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApiController extends Controller
{

    public function modelList(Request $request, $model) {
        $field = 'name';
        $key = 'id';

        if ($request->get('field')) {
            $field = $request->field;
        }

        if ($request->get('key')) {
            $key = $request->key;
        }

        $query = app("App\\" . $model)::where(DB::raw("($field)"),
            'like', '%' . $request->search . '%');

        if ($model === 'User') {
            $query = $query->whereNotIn('role', [3, 12]);
        }

        return $query->select($field, $key)->limit(100)->get();
    }

    // Response
    public static function r($data = [], $action = 1) {
        $response = [];

        switch(strtoupper(trim($action))) {
            case 1: // SUCCESS ROUTE
                $response = ['success' => 1, 'error' => 0, 'data' => $data];
                break;

            case 0: // FAIL ROUTE
                $response = ['success' => 0, 'error' => 1, 'data' => $data];
                break;
        }

        return response()->json($response);
    }

    public function companiesList(Request $request) {
        return Company::where('name', 'like', '%' . $request->search . '%')
            ->select('hash_id', 'name')->limit(100)->get();
    }

    // Обновляем все пункты выпуска
    public function ResetAllPV ()
    {
        $users = User::all();

        date_default_timezone_set('UTC');

        foreach($users as $user) {
            $time = time();

            $timezone = $user->timezone ? $user->timezone : 3;

            $time += $timezone * 3600;
            // [0] => Hours, [1] => Minutes
            $time = explode(':', date('H:i', $time));

            if($time[0] === '00' && $time[1] === '00') {
                $user->update(['pv_id' => $user->pv_id_default]);
            }
        }
    }

    public function UpdateProperty (Request $request) {
        $item_model = $request->item_model;
        $item_id = $request->item_id;
        $item_field = $request->item_field;

        $new_value = $request->get('new_value');
        $item_model = app("App\\$item_model");
        $item_model = $item_model->find($item_id);

        if($item_model) {
            $item_field = str_replace('[]', '', $item_field);

            $item_model[$item_field] = is_array($new_value) ? join(',', $new_value) : $new_value;

            if($item_model->save()) {
                return ApiController::r(['exists' => true, 'data' => $item_model, 'message' => 'Значение обновлено',0]);
            }
        }

        return ApiController::r(['exists' => false, 'data' => [], 'message' => 'Значение не обновлено',0]);
    }

    // Проверка свойства
    public function CheckProperty (Request $request)
    {
        $prop = $request->prop;
        $model = $request->model;
        $val = $request->val;
        $user = $request->user();

        $dateAnketa = $request->get('dateAnketa', '');

        $models = [
            'Car' => [
                'model' => 'App\Car',
                'fields' => ['hash_id', 'mark_model', 'gos_number', 'company_id']
            ],
            'Driver' => [
                'model' => 'App\Driver',
                'fields' => ['hash_id', 'fio', 'company_id']
            ],
            'Company' => [
                'model' => 'App\Company',
                'fields' => ['id', 'name', 'inn', 'payment_form']
            ]
        ];

        $blockedFields = [
            'old_id', 'req_id', 'inn',
            'date_bdd',
            'date_report_driver',
            'contract_id'
        ];

        $deleteImportantFields = [
            'inn', 'old_id', 'autosync_fields'
        ];

        if(isset($models[$model]) && !empty($val)) {
            $_model = $models[$model];
            $data = app($_model['model']);
            $fields = $data->fillable;
            array_push($fields, 'id');

            /**
             * Контроль дат
             */
            $redDates = [];

            /**
             * Фильтрация полей
             */
            $fields = array_filter($fields, function ($item) use ($deleteImportantFields) {
                return !in_array($item, $deleteImportantFields);
            });

            $fieldsValues = new IndexController();
            $fieldsValues = $fieldsValues->elements[$model]['fields'];

            $data = $data->where($prop, $val)->get($fields)->first();

            if(isset($data)) {
                $data_exists = $data->count() > 0;
            } else {
                $data_exists = $data;
            }

            $data = $data->toArray();

            if($dateAnketa) {
                if(isset($data['id'])) {
                    $redDates = AnketsController::ddateCheck($dateAnketa, $model, $data['id']);
                }
            }


            if (isset($data['company_id'])) {
                if($company = Company::select('name', 'hash_id')->find($data['company_id'])){
                    $data['company_name'] = $company->name;
                    $data['company_hash_id'] = $company->hash_id;
                }
            }

            if (isset($data['date_of_employment'])) {
                $data['date_of_employment'] = Carbon::parse($data['date_of_employment'])->format('Y-m-d');
            }

            return ApiController::r(['exists' => $data_exists, 'model' => $model, 'blockedFields' => $blockedFields, 'message' => $data, 'fieldsValues' => $fieldsValues, 'redDates' => $redDates], 1);
        }

        return ApiController::r(['exists' => false, 'message' => '', 'model' => $model], 0);
    }

    public function OneCheckProperty($prop, $model, $val, Request $request)
    {
        if ($model = app("App\\$request->model")->where($prop, $val)->first()) {
            return response([
                'status' => true,
                'name'   => $model->fio ?? $model->name,
            ]);
        } else {
            return response([
                'status' => false,
            ]);
        }
    }

    public function saveFieldsVisible(Request $request) {
        if ($request->params && $request->params !== 'null') {
            $request->user()->fields_visible = json_encode($request->params);
        } else {
            $request->user()->fields_visible = null;
        }
        $request->user()->save();
    }
}
