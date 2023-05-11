<?php

namespace App\Http\Controllers\Api;

use App\Anketa;
use App\Driver;
use App\Http\Controllers\SmsController;
use Illuminate\Support\Facades\Auth;
use App\Notify;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Matrix\Exception;

class SdpoController extends Controller
{

    /*
     * Creating anketa by sdpo request
     */
    public function createAnketa(Request $request)
    {
        $driver = Driver::where('hash_id', $request->driver_id)->first();
        $user = $request->user('api');
        $sms = new SmsController();

        if (!$driver) {
            return response()->json(['message' => 'Указанный водитель не найден!'], 400);
        }

        if(!is_null($driver->end_of_ban) && (Carbon::now("GMT") < $driver->end_of_ban)){
            return response()->json(['message' => 'Указанный водитель остранен до '.Carbon::parse($driver->end_of_ban)->addHours(Auth::user()->timezone)."!"], 400);
        }

        if ($request->user('api')->blocked) {
            return response()->json(['message' => 'Этот терминал заблокирован!'], 400);
        }

        if ($request->user_id) {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['message' => 'Пользователь с таким ID не найден!'], 400);
            }
        }

        $tonometer = $request->tonometer ?? rand(118, 129) . '/' . rand(70, 90);
        $test_narko = $request->test_narko ?? 'Отрицательно';
        $proba_alko = $request->proba_alko ?? 'Отрицательно';
        $company = $driver->company;
        $medic = [];

        if ($company->dismissed === 'Да') {
            return response()->json(['message' => 'Компания в черном списке. Необходимо связаться с руководителем!', 401]);
        }

        $medic['type_anketa'] = $request->type_anketa ?? 'medic';
        $medic['user_id'] = $request->user_id ?? $user->id;
        $medic['user_name'] = $user->name;
        $medic['user_eds'] = $user->eds;
        $medic['pulse'] = $request->pulse ?? mt_rand(60, 80);
        $medic['pv_id'] = $request->user('api')->pv->name;
        $medic['point_id'] = $request->user('api')->pv->id;
        $medic['tonometer'] = $tonometer;
        $medic['driver_id'] = $driver->hash_id;
        $medic['driver_fio'] = $driver->fio;
        $medic['driver_gender'] = $driver->gender ?? '';
        $medic['company_id'] = $company->hash_id;
        $medic['company_name'] = $company->name;
        $medic['med_view'] = $request->med_view ?? 'В норме';
        $medic['t_people'] = $request->t_people ?? 36.6;
        $medic['type_view'] = $request->type_view ?? 'Предрейсовый/Предсменный';
        $medic['flag_pak'] = 'СДПО А';
        $medic['terminal_id'] = $request->user('api')->id;

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
        $timezone = $request->user('api')->timezone ? $request->user('api')->timezone : 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d H:i:s', $time);

        if ($driver->end_of_ban && (Carbon::parse($time) < $driver->end_of_ban)) {
            return response()->json(
                ['message' => 'Указанный водитель остранен до ' . Carbon::parse($driver->end_of_ban)->addHours(Auth::user()->timezone) . "!"],
                400
            );
        }

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
        $driver->date_prmo = Carbon::now();

        if ($request->sleep_status && $request->sleep_status === 'Нет') {
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if ($request->people_status && $request->people_status === 'Нет') {
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if ($request->people_status && $request->people_status === 'Нет') {
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if (doubleval($request->alcometer_result) > 0) {
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
            $medic['proba_alko'] = 'Положительно';
        }

        //ПРОВЕРЯЕМ статус для поля "Заключение"
        $ton = explode('/', $tonometer);
        if ($proba_alko === 'Положительно' || $test_narko === 'Положительно'
            || intval($ton[1]) >= $driver->getPressureDiastolic() || intval($ton[0]) >= $driver->getPressureSystolic()
            || $medic['med_view'] !== 'В норме' || doubleval($medic['t_people']) >= 38) {
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';

            if (intval($ton[1]) >= $driver->getPressureDiastolic() || intval($ton[0]) >= $driver->getPressureSystolic()) {
                $driver->end_of_ban = Carbon::parse($time)->addMinutes($driver->getTimeOfPressureBan());
            }

            if ($proba_alko === 'Положительно') {
                $driver->end_of_ban = Carbon::parse($time)->addMinutes($driver->getTimeOfAlcoholBan());
            }
        }

        $driver->save();

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

        $timeout = Settings::setting('timeout');
        $anketa['timeout'] = $timeout ?? 20;

        return response()->json($anketa);
    }

    /*
     * Check connection sdpo
     */
    public function checkConnaction(Request $request)
    {
        $user = $request->user('api');
        $user->last_connection_at = Carbon::now();
        $user->save();
        return response()->json(true);
    }

    /*
     * Get PV name by user
     */
    public function getPoint(Request $request)
    {
        $user = $request->user('api');
        return response()->json($user->pv->name);
    }

    /*
     * return all medics
     */
    public function getMedics(Request $request)
    {
        $users = User::with(['roles', 'pv:id,name,pv_id', 'pv.town:id,name'])
            ->whereHas('roles', function ($q) use ($request) {
                $q->where('roles.id', 2);
            })->select('id', 'name', 'eds', 'pv_id')->get();

        $users = $users->groupBy(['pv.town.name', 'pv.name']);

        return response()->json($users);
    }

    /*
    * return driver by id
    */
    public function getDriver(Request $request, $id)
    {
        if ($request->user('api')->blocked) {
            return response()->json(['message' => 'Этот терминал заблокирован!'], 400);
        }

        $driver = Driver::where('hash_id', $id)
            ->with('company')
            ->select('hash_id', 'fio', 'dismissed', 'company_id', 'end_of_ban', 'photo')->first();

        //return response()->json(['message' => Carbon::now("GMT")->addMinutes($driver->getTimeOfAlcoholBan())], 400);

        if (!$driver) {
            return response()->json(['message' => 'Водитель с указанным ID не найден!'], 400);
        }

        date_default_timezone_set('UTC');
        $time = time();
        $timezone = $request->user('api')->timezone ? $request->user('api')->timezone : Auth::user()->timezone;
        $time += $timezone * 3600;
        $time = date('Y-m-d H:i:s', $time);

        if ($driver->end_of_ban && (Carbon::parse($time) < $driver->end_of_ban)) {
            return response()->json(
                ['message' => 'Указанный водитель остранен до ' . Carbon::parse($driver->end_of_ban) . "!"],
                400
            );
        }

        if ($driver->dismissed === 'Да') {
            return response()->json(['message' => 'Водитель с указанным ID уволен!'], 303);
        }

        if ($driver->company->dismissed === 'Да') {
            return response()->json(['message' => 'Комания указанного водителя заблокирована!'], 303);
        }

        return response()->json($driver);
    }

    /*
    * return all drivers
    */
    public function getDrivers(Request $request)
    {
        $drivers = Driver::select('hash_id', 'fio')->get();
        return response()->json($drivers);
    }


    /*
    * Return inspection info
    */
    public function getInspection(Request $request, $id)
    {
        $inspection = Anketa::find($id);
        return response()->json($inspection);
    }

    /*
    * Inspection update type
    */
    public function changeType(Request $request, $id)
    {
        $inspection = Anketa::find($id);
        if ($inspection) {
            $inspection->update(['type_anketa' => 'medic']);
        }
    }

    /*
    * Driver update photo
    */
    public function setDriverPhoto(Request $request, $id)
    {
        if ($request->photo) {
            $driver = Driver::where('hash_id', $id)->first();
            if (!$driver) {
                return;
            }

            $image = base64_decode($request->photo);
            $path = "elements/driver_photo_" . $id . ".png";
            Storage::disk('public')->put($path, $image);

            $driver->update([
                'photo' => $path
            ]);
        }
    }
}
