<?php

namespace App\Http\Controllers;

use App\Car;
use App\Company;
use App\Driver;
use App\Req;
use App\User;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Psy\Util\Json;

class ApiController extends Controller
{
    public function modelList(Request $request, $model) {
        $mainContentFields = [
            "Company" => "name",
            "Driver"  => "fio",
            "Car"     => "gos_number",
            "Product" => "name",
            "Instr"   => "name"
        ];

        $field = 'name';
        $key = 'id';
        $user = $request->user('api');

        if ($request->get('field')) {
            $field = $request->get('field');
            $searchingIn = $mainContentFields[$model] ?? $field;
        }

        if ($request->get('key')) {
            $key = $request->key;
        }

        if ($model === 'User') {
            $query = User::with('roles')->whereHas('roles', function ($q) use ($request) {
                $q->whereNotIn('roles.id', [3, 6, 9]);
            })->where($searchingIn,
                      'like', '%' . $request->search . '%')
                         ->orWhere("hash_id", "like", "%" . $request->search . "%");
        } else {
            $query = app("App\\" . $model)::where(function ($query) use ($request, $searchingIn) {
                $query->where($searchingIn, 'like', '%' . $request->search . '%')
                ->orWhere("hash_id", "like", "%" . $request->search . "%");
            });

            if ($user->hasRole('client') && ($model === 'Driver' || $model === 'Car')) {
                $query = $query->where('company_id', $user->company_id);
            }
        }

        if ($request->get('trashed') === 'true') {
            $query = $query->withTrashed();
        }

        return $query->select('id', 'hash_id', 'company_id', $field, $key)->limit(100)->get();
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
        $company = Company::where('name', 'like', '%' . $request->search . '%')
                        ->orWhere('hash_id', 'like', '%' . $request->search . '%')
                        ->orWhere('inn', 'like', "%$request->search%")
                        ->select('hash_id', 'name', 'id', 'inn')->limit(100);

        if ($request->get('trashed') === 'true') {
            $company = $company->withTrashed();
        }

        return $company->get();
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
            if ($request->item_model === 'Company' && !$request->user()->access('company_update')) {
                if ((!$request->user()->hasRole('medic') && !$request->user()->hasRole('tech')) || $item_field !== 'link_waybill') {
                    return ApiController::r(['exists' => false, 'data' => [], 'message' => 'Значение не обновлено',0]);
                }
            }

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
            'contracts',
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

            if ($_model['model'] == Company::class){
                $data = $data::with(['contracts.services']);
            }elseif($_model['model'] == Driver::class || $_model['model'] == Car::class){
                $data = $data::with(['contracts.services']);
            }
//            array_push($fields, 'id');

            /**
             * Контроль дат
             */
            $redDates = [];

            /**
             * Фильтрация полей
             */
//            $fields = array_filter($fields, function ($item) use ($deleteImportantFields) {
//                return !in_array($item, $deleteImportantFields);
//            });


            $fieldsValues = new IndexController();
            $fieldsValues = $fieldsValues->elements[$model]['fields'];

//            $data = $data->where($prop, $val)->get()->first();
            if ($_model['model'] == Company::class){
                if(!user()->access('companies_access_field_where_call')){   //'Кому отправлять СМС при отстранении'
                    unset($fields['where_call']);
                    unset($fieldsValues['where_call']);
                }
                if(!user()->access('companies_access_field_where_call_name')){  //'Кому звонить при отстранении (имя, должность)
                    unset($fields['where_call_name']);
                    unset($fieldsValues['where_call_name']);
                }
            }

            $data = $data->where($prop, $val)
                         ->get($fields)
                         ->first();

            if($data) {
                $data_exists = $data->count() > 0;
                $data = $data->toArray();
            } else {
                $data_exists = $data;
                return ApiController::r(['exists' => $data_exists, 'model' => $model, 'blockedFields' => $blockedFields, 'message' => $data, 'fieldsValues' => $fieldsValues, 'redDates' => $redDates], 1);
            }

            if ($_model['model'] == Company::class && isset($data['dismissed']) && isset($data['name'])) {
                $data = [ 'name' => $data['name'], 'dismissed' => $data['dismissed'] ] + Arr::except($data, ['name', 'dismissed']);
            }

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
