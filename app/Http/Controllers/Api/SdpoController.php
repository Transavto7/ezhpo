<?php

namespace App\Http\Controllers\Api;

use App\Anketa;
use App\Driver;
use App\Http\Controllers\SmsController;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Notify;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class SdpoController extends Controller
{
    /*
     * Creating anketa by sdpo request
     */
    public function createAnketa(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('api');
        $apiClient = $user;
        if ($user->blocked) {
            return response()->json(['message' => 'Этот терминал заблокирован!'], 400);
        }

        if ($request->user_id) {
            $user = User::find($request->user_id);
            if (!$user) {
                return response()->json(['message' => 'Пользователь с таким ID не найден!'], 400);
            }
        }

        /** @var Driver $driver */
        $driver = Driver::where('hash_id', $request->driver_id)->first();
        if (!$driver) {
            return response()->json(['message' => 'Указанный водитель не найден!'], 400);
        }

        $company = $driver->company;
        if ($company->dismissed === 'Да') {
            return response()->json(['message' => 'Компания в черном списке. Необходимо связаться с руководителем!', 401]);
        }

        if ($request->tonometer) {
            $tonometer = $request->tonometer;
        } else {
            $systolic = rand(100, 139);
            $diastolic = rand(60, 89);

            if ($systolic >= intval($driver->getPressureSystolic())) {
                $systolic = intval($driver->getPressureSystolic()) - rand(1, 10);
            }

            if ($diastolic >= intval($driver->getPressureDiastolic())) {
                $diastolic = intval($driver->getPressureDiastolic()) - rand(1, 10);
            }

            $tonometer = $systolic . '/' . $diastolic;
        }

        $medic = [];
        $medic['type_anketa'] = $request->type_anketa ?? 'medic';
        $medic['user_id'] = $user->id;
        $medic['operator_id'] = $user->id;
        $medic['user_name'] = $user->name;
        $medic['user_eds'] = $user->eds;
        $medic['pulse'] = $request->pulse ?? mt_rand(60, 80);
        $medic['pv_id'] = $apiClient->pv->name;
        $medic['point_id'] = $apiClient->pv->id;
        $medic['tonometer'] = $tonometer;
        $medic['driver_id'] = $driver->hash_id;
        $medic['driver_fio'] = $driver->fio;
        $medic['driver_gender'] = $driver->gender ?? '';
        $medic['company_id'] = $company->hash_id;
        $medic['company_name'] = $company->name;
        $medic['med_view'] = $request->med_view ?? 'В норме';
        $medic['t_people'] = $request->t_people ?? 36.6;
        $medic['type_view'] = $request->type_view ?? 'Предрейсовый/Предсменный';
        $medic['flag_pak'] = $request->type_anketa === 'pak_queue' ? 'СДПО Р' : 'СДПО А';
        $medic['terminal_id'] = $apiClient->id;
        $medic['realy'] = "да";

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
        $timezone = $apiClient->timezone ?? 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d H:i:s', $time);

        $medic['created_at'] = $request->created_at ?? $time;
        $medic['date'] = $request->date ?? $medic['created_at'];

        $test_narko = $request->test_narko ?? 'Отрицательно';
        $proba_alko = $request->proba_alko ?? 'Отрицательно';
        $driver->checkGroupRisk($tonometer, $test_narko, $proba_alko);
        $driver->date_prmo = $medic['created_at'];

        $admitted = 'Допущен';
        $notAdmittedReasons = [];

        if ($request->sleep_status === 'Нет') {
            $notAdmittedReasons[] = ['sleep_status - нет'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if ( $request->people_status === 'Нет') {
            $notAdmittedReasons[] = ['people_status - нет'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if (doubleval($request->alcometer_result) > 0) {
            $notAdmittedReasons[] = ['Алкоголь в крови'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
            $medic['proba_alko'] = 'Положительно';
        }

        if ($proba_alko === 'Положительно') {
            $notAdmittedReasons[] = ['Положительный тест на алкоголь'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
            $driver->end_of_ban = Carbon::parse($time)->addMinutes($driver->getTimeOfAlcoholBan());
        }

        if ($test_narko === 'Положительно') {
            $notAdmittedReasons[] = ['Положительный тест на наркотики'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if ($medic['med_view'] !== 'В норме') {
            $notAdmittedReasons[] = ['med_view не в норме'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if (doubleval($medic['t_people']) >= 38) {
            $notAdmittedReasons[] = ['Высокая температура'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        if (intval($medic['pulse']) <= $driver->getPulseLower() || intval($medic['pulse']) >= $driver->getPulseUpper())
        {
            $notAdmittedReasons[] = ['Слишком высокий или низкий пульс'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
        }

        $ton = explode('/', $tonometer);
        if (intval($ton[1]) >= $driver->getPressureDiastolic() || intval($ton[0]) >= $driver->getPressureSystolic()) {
            $notAdmittedReasons[] = ['Высокое давление'];
            $admitted = 'Не допущен';
            $medic['med_view'] = 'Отстранение';
            $driver->end_of_ban = Carbon::parse($time)->addMinutes($driver->getTimeOfPressureBan());
        }

        $driver->save();

        $medic['admitted'] = $admitted;
        try {
            $anketa = Anketa::create($medic);
        } catch (Throwable $e) {
            abort(500);
        }

        // ОТПРАВКА SMS
        if ($anketa['admitted'] === 'Не допущен') {
            $phone_to_call = Settings::setting('sms_text_phone');
            $sms = new SmsController();
            $sms->sms($company->where_call, Settings::setting('sms_text_driver') . " $driver->fio . $phone_to_call");
        }

        $anketa['timeout'] = Settings::setting('timeout') ?? 20;

        $stamp = $apiClient->stamp;
        if ($stamp) {
            $anketa['stamp_head'] = $stamp->company_name;
            $anketa['stamp_licence'] = $stamp->licence;
        }

        if ($user->validity_eds_start && $user->validity_eds_end) {
            $anketa['validity'] = 'Срок действия: c ' . Carbon::parse($user->validity_eds_start)->format('d.m.Y')
                 .' по ' . Carbon::parse($user->validity_eds_end)->format('d.m.Y');
        }

        /** @var Anketa $anketa */
        if ($anketa['admitted'] === 'Не допущен') {
            Log::channel('admitting')->info(json_encode(
                [
                    'id' => $anketa->id,
                    'anketa' => $anketa->toArray(),
                    'request' => $request->all(),
                    'source' => 'SdpoController',
                    'reasons' => $notAdmittedReasons,
                ]
            ));
        }

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
            })
            ->select(['id', 'name', 'eds', 'pv_id'])
            ->get()
            ->groupBy(['pv.town.name', 'pv.name']);

        return response()->json($users);
    }

    /*
    * return driver by id
    */
    public function getDriver(Request $request, $id): JsonResponse
    {
        $apiClient = $request->user('api');

        if ($apiClient->blocked) {
            return response()->json(['message' => 'Этот терминал заблокирован!'], 400);
        }

        $driver = Driver::where('hash_id', $id)
            ->with('company')
            ->select([
                'hash_id',
                'fio',
                'dismissed',
                'company_id',
                'end_of_ban',
                'photo'
            ])
            ->first();

        if (!$driver) {
            return response()->json(['message' => 'Водитель с указанным ID не найден!'], 400);
        }

        date_default_timezone_set('UTC');
        $time = time();
        $timezone = $apiClient->timezone ?? Auth::user()->timezone;
        $time += $timezone * 3600;
        $time = date('Y-m-d H:i:s', $time);

        if ($driver->end_of_ban && (Carbon::parse($time) < Carbon::parse($driver->end_of_ban))) {
            return response()->json(
                ['message' => 'Указанный водитель отстранен до ' . Carbon::parse($driver->end_of_ban) . "!"],
                400
            );
        }

        if ($driver->dismissed === 'Да') {
            return response()->json(['message' => 'Водитель с указанным ID уволен!'], 303);
        }

        if ($driver->company->dismissed === 'Да') {
            return response()->json(['message' => 'Компания указанного водителя заблокирована!'], 303);
        }

        return response()->json($driver);
    }

    /*
    * return all drivers
    */
    public function getDrivers(): JsonResponse
    {
        $drivers = Driver::select('hash_id', 'fio')->get();

        return response()->json($drivers);
    }

    /*
    * Return inspection info
    */
    public function getInspection(Request $request, $id): JsonResponse
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
            return;
        }

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
