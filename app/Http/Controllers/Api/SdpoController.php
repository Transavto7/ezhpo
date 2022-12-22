<?php

namespace App\Http\Controllers\Api;

use App\Anketa;
use App\Driver;
use App\Http\Controllers\SmsController;
use App\Notify;
use App\Point;
use App\Settings;
use App\User;
use http\Env\Response;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Matrix\Exception;

class SdpoController extends Controller
{
    /*
     * Creating anketa by sdpo request
     */
    public function createAnketa(Request $request) {
        $driver = Driver::where('hash_id', $request->driver_id)->first();
        $user = $request->user('api');
        $sms = new SmsController();

        if (!$driver) {
            return response()->json(['message' => 'Указанный водитель не найден!'], 401);
        }

        if ($request->user_id) {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['message' => 'Пользователь с таким ID не найден!'], 401);
            }
        }

        $tonometer = $request->tonometer ?? rand(118,129) .'/'. rand(70,90);
        $test_narko = $request->test_narko ?? 'Отрицательно';
        $proba_alko = $request->proba_alko ?? 'Отрицательно';
        $company = $driver->company;
        $medic = [];

        if ($company->dismissed  === 'Да') {
            return response()->json(['message' => 'Компания в черном списке. Необходимо связаться с руководителем!', 401]);
        }

        $medic['type_anketa'] = $request->type_anketa ?? 'medic';
        $medic['user_id'] = $request->user_id ?? $user->id;
        $medic['user_name'] = $user->name;
        $medic['user_eds'] = $user->eds;
        $medic['pulse'] = $request->pulse ?? mt_rand(60,80);
        $medic['pv_id'] = $user->pv->name;
        $medic['tonometer'] = $tonometer;
        $medic['driver_id'] = $driver->hash_id;
        $medic['driver_fio'] = $driver->fio;
        $medic['driver_gender'] = $driver->gender ?? '';
        $medic['company_id'] = $company->hash_id;
        $medic['company_name'] = $company->name;
        $medic['med_view'] = $request->med_view ?? 'В норме';
        $medic['t_people'] = $request->t_people ?? 36.6;
        $medic['type_view'] = $request->type_view ?? 'Послерейсовый/Послесменный';
        $medic['flag_pak'] = 'СДПО А';

        if ($driver->year_birthday !== '' && $driver->year_birthday !== '0000-00-00') {
            $medic['driver_year_birthday'] = $driver->year_birthday;
        }

        if ($request->photo) {
            $medic['photos'] = $request->photo;
        }

        if ($request->video) {
            $medic['videos'] = $request->video;
        }

        date_default_timezone_set('UTC');
        $time = time();
        $timezone = $user->timezone ? $user->timezone : 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d H:i:s', $time);

        $medic['created_at'] = $request->created_at ?? $time;
        $medic['date'] = $request->date ?? $medic['created_at'];

        if ($request->number_list_road) {
            $medic['number_list_road'] = $request->number_list_road;
        } else {
            $findCurrentPL = Anketa::where('created_at', '>=', Carbon::today())->where('in_cart', 0)->count();
            $suffix_anketa = $findCurrentPL > 0 ? '/' . ($findCurrentPL + 1) : '';
            $medic['number_list_road'] = $driver->hash_id . '-' . date('d.m.Y', strtotime($medic['date'])) . $suffix_anketa;
        }

        $driver->checkGroupRisk($tonometer, $test_narko, $proba_alko);

        $admitted = null;

        //ПРОВЕРЯЕМ статус для поля "Заключение"
        $ton = explode('/', $tonometer);
        if ($proba_alko === 'Положительно' || $test_narko === 'Положительно'
            || $medic['med_view'] !== 'В норме' || $medic['t_people'] >= 38 || $ton[0] >= 150) {
            $admitted = 'Не допущен';
        }
        if ($request->sleep_status && $request->people_status && $request->alcometer_result) {
            if($request->sleep_status === 'Нет' && $request->people_status === 'Нет' && $request->alcometer_result > 0) {
                $admitted = 'Не допущен';
            }
        }

        if ($request->type_anketa === 'pak_queue') {
            $notifyTo = new Notify();
            $notifyTo->sendMsgToUsersFrom('role', '4', 'Новый осмотр в очереди СДПО');
            $medic['flag_pak'] = 'СДПО Р';
        }

        $medic['admitted'] = $admitted ?? 'Допущен';

        $anketa = Anketa::create($medic);

        // ОТПРАВКА SMS
        if ($anketa['admitted'] == 'Не допущен') {
            $phone_to_call = Settings::setting('sms_text_phone');
            $sms->sms($company->where_call, Settings::setting('sms_text_driver') . " $driver->fio . $phone_to_call");
        }

        return response()->json($anketa);
    }

    /*
     * Check connection sdpo
     */
    public function checkConnaction(Request $request) {
        return "true";
    }

    /*
     * Get PV name by user
     */
    public function getPoint(Request $request) {
        $user = $request->user('api');
        return $user->pv->name;
    }

    /*
     * return all medics
     */
    public function getMedics(Request $request) {
        $users = User::with('roles', 'pv:id,name,pv_id', 'pv.town:id,name')->whereHas('roles', function ($q) use ($request) {
            $q->where('roles.id', 2);
        })->select('id', 'name', 'pv_id')->get();

        $users = $users->groupBy(['pv.town.name', 'pv.name']);

        return $users;
    }
}
