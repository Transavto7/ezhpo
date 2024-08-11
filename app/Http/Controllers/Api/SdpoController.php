<?php

namespace App\Http\Controllers\Api;

use App\Anketa;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Enums\FormTypeEnum;
use App\Http\Controllers\SmsController;
use App\Http\Requests\StoreSdpoCrashRequest;
use App\MedicFormNormalizedPressure;
use App\SdpoCrashLog;
use App\Stamp;
use App\Traits\UserEdsTrait;
use App\ValueObjects\Phone;
use App\ValueObjects\PressureLimits;
use App\ValueObjects\Tonometer;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Settings;
use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Throwable;

class SdpoController extends Controller
{
    use UserEdsTrait;

    public function getPrints(Request $request, $id): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('api');

        if ($user->blocked) {
            return response()->json(['message' => 'Этот терминал заблокирован!'], 400);
        }

        $driver = Driver::where('hash_id', $id)->first();
        if (!$driver) {
            return response()->json(['message' => 'Водитель с указанным ID не найден!'], 400);
        }

        $forms = Anketa::query()
            ->select([
                'anketas.driver_fio',
                'anketas.admitted',
                'anketas.date as created_at',
                'anketas.user_eds',
                'anketas.user_name',
                'anketas.type_view',
                'anketas.user_validity_eds_start',
                'anketas.user_validity_eds_end',
                'stamps.company_name as stamp_head',
                'stamps.licence as stamp_licence'
            ])
            ->leftJoin('users', 'anketas.terminal_id', '=', 'users.id')
            ->leftJoin('stamps', 'users.stamp_id', '=', 'stamps.id')
            ->where('driver_id', $id)
            ->where('type_anketa', FormTypeEnum::MEDIC)
            ->where('admitted', 'Допущен')
            ->whereNotNull('flag_pak')
            ->whereDate('date', '<=', Carbon::now()->toDateTime())
            ->whereDate('date', '>=', Carbon::now()->startOfMonth()->subMonth()->toDateString())
            ->get()
            ->toArray();

        $forms = array_map(function ($form) {
            $validity = UserEdsTrait::getValidityString(
                $form['user_validity_eds_start'] ?? null,
                $form['user_validity_eds_end'] ?? null
            );
            if ($validity) {
                $form['validity'] = $validity;
            }

            return $form;
        }, $forms);

        return response()->json($forms);
    }

    /*
     * Creating anketa by sdpo request
     */
    public function createAnketa(Request $request): JsonResponse
    {
        try {
            DB::beginTransaction();

            /** @var User $user */
            $user = $request->user('api');
            $apiClient = $user;
            if ($user->blocked) {
                throw new Exception('Этот терминал заблокирован!', 400);
            }

            if ($request->user_id) {
                $user = User::find($request->user_id);
            }

            if (!$user) {
                throw new Exception('Пользователь с таким ID не найден!', 400);
            }

            /** @var Driver $driver */
            $driver = Driver::where('hash_id', $request->driver_id)->first();
            if (!$driver) {
                throw new Exception('Указанный водитель не найден!', 400);
            }

            date_default_timezone_set('UTC');
            $time = time();
            $timezone = $apiClient->timezone ?: 3;
            $time += $timezone * 3600;
            $time = date('Y-m-d H:i:s', $time);

            if ($driver->end_of_ban && (Carbon::parse($time) < Carbon::parse($driver->end_of_ban))) {
                $message = sprintf("Указанный водитель отстранен до %s!", Carbon::parse($driver->end_of_ban));
                throw new Exception($message, 400);
            }

            if ($driver->dismissed === 'Да') {
                throw new Exception('Водитель с указанным ID уволен!', 303);
            }

            $company = $driver->company;
            if ($company->dismissed === 'Да') {
                $message = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
                throw new Exception($message, 401);
            }

            if ($driver->only_offline_medic_inspections) {
                $message = 'Водителю ограничен дистанционный выпуск, обратитесь к медицинскому сотруднику на Пункте Выпуска!';
                throw new Exception($message, 400);
            }

            //TODO: добавить валидацию
            $tonometer = $request->tonometer;
            if (!$tonometer) {
                $tonometer = strval(Tonometer::random($driver));
            }

            $medic = [];
            $medic['type_anketa'] = $request->type_anketa ?? 'medic';
            $medic['user_id'] = $user->id;
            $medic['user_name'] = $user->name;
            $medic['user_validity_eds_start'] = $user->validity_eds_start;
            $medic['user_validity_eds_end'] = $user->validity_eds_end;
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

            $medic['created_at'] = $request->created_at ?? $time;
            $medic['date'] = $request->date ?? $medic['created_at'];

            $test_narko = $request->test_narko ?? 'Отрицательно';
            $proba_alko = $request->proba_alko ?? 'Отрицательно';
            $driver->date_prmo = $medic['created_at'];

            $admitted = 'Допущен';
            $notAdmittedReasons = [];

            if ($request->filled('alcometer_result')) {
                $medic['alcometer_result'] = doubleval($request->input('alcometer_result'));
            }

            if (($medic['alcometer_result'] ?? 0) == 1) {
                $medic['alcometer_result'] = 0.1;
            }

            if ($request->filled('alcometer_mode')) {
                $medic['alcometer_mode'] = $request->input('alcometer_mode');
            }

            if (($medic['alcometer_result'] ?? 0) > 0) {
                $notAdmittedReasons[] = ['Алкоголь в крови'];
                $admitted = 'Не допущен';
                $medic['med_view'] = 'Отстранение';
                $medic['proba_alko'] = 'Положительно';
                $proba_alko = $request->proba_alko ?? 'Положительно';
            }

            $driver->checkGroupRisk($tonometer, $test_narko, $proba_alko);

            if ($request->sleep_status === 'Нет') {
                $notAdmittedReasons[] = ['sleep_status - нет'];
                $admitted = 'Не допущен';
                $medic['med_view'] = 'Отстранение';
            }

            if ($request->people_status === 'Нет') {
                $notAdmittedReasons[] = ['people_status - нет'];
                $admitted = 'Не допущен';
                $medic['med_view'] = 'Отстранение';
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

            if (doubleval($medic['t_people']) >= 37) {
                $notAdmittedReasons[] = ['Высокая температура'];
                $admitted = 'Не допущен';
                $medic['med_view'] = 'Отстранение';
            }

            if (intval($medic['pulse']) <= $driver->getPulseLower() || intval($medic['pulse']) >= $driver->getPulseUpper()) {
                $notAdmittedReasons[] = ['Слишком высокий или низкий пульс'];
                $admitted = 'Не допущен';
                $medic['med_view'] = 'Отстранение';
            }

            $pressure = Tonometer::fromString($tonometer);
            $pressureLimits = PressureLimits::create($driver);
            if (!$pressure->isAdmitted($pressureLimits)) {
                $notAdmittedReasons[] = ['Высокое давление'];
                $admitted = 'Не допущен';
                $medic['med_view'] = 'Отстранение';
                $driver->end_of_ban = Carbon::parse($time)->addMinutes($driver->getTimeOfPressureBan());
            }

            $driver->save();

            $medic['admitted'] = $admitted;
            $form = Anketa::create($medic);

            if ($pressure->needNormalize($pressureLimits)) {
                MedicFormNormalizedPressure::store(
                    $form->id,
                    $pressure->getNormalized()
                );
            }

            /**
             * ОТПРАВКА SMS
             */
            $needNotify = $form['admitted'] === 'Не допущен' && $form['flag_pak'] !== 'СДПО Р';
            if ($needNotify) {
                $phoneToCall = Settings::setting('sms_text_phone');
                $message = Settings::setting('sms_text_driver') . " $driver->fio . $phoneToCall";

                $sms = new SmsController();
                $sms->sms($company->where_call, $message);
            }

            $form['timeout'] = Settings::setting('timeout') ?? 20;

            $stamp = $apiClient->stamp;
            if ($stamp) {
                $form['stamp_head'] = $stamp->company_name;
                $form['stamp_licence'] = $stamp->licence;
            }

            $validity = UserEdsTrait::getValidityString($user->validity_eds_start, $user->validity_eds_end);
            if ($validity) {
                $form['validity'] = $validity;
            }

            $form['sleep_status'] = $request->sleep_status;
            $form['people_status'] = $request->people_status;

            /** @var Anketa $form */
            if ($form['admitted'] === 'Не допущен') {
                $form['reasons'] = $notAdmittedReasons;
            }

            //TODO: вынести в мидлвар или ивент позже
            if ($request->has('logs')) {
                Log::channel('sdpo')->info(json_encode(
                    [
                        'id' => $form->id,
                        'request' => $request->all(),
                        'ip' => $request->getClientIp() ?? null
                    ]
                ));
            }

            DB::commit();

            return response()->json($form);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response()->json([
                'message' => $exception->getMessage()
            ], $exception->getCode() ?? Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    /**
     * @deprecated
     * Check connection sdpo
     */
    public function checkConnection(): JsonResponse
    {
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

    public function getStamp(Request $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('api');

        $stamp = $user->stamp;

        $data = [
            'stamp_head' => null,
            'stamp_licence' => null
        ];

        if ($user->stamp) {
            $data = [
                'stamp_head' => $stamp->company_name,
                'stamp_licence' => $stamp->licence
            ];
        }

        return response()->json($data);
    }

    public function getTerminalVerification(Request $request)
    {
        $user = $request->user('api');

        if (!$user->terminalCheck) {
            return response()->json([
                'serial_number' => null,
                'date_check' => null,
            ]);
        }

        return response()->json([
            'serial_number' => $user->terminalCheck->serial_number,
            'date_check' => $user->terminalCheck->date_check->format('Y-m-d'),
        ]);
    }

    /*
     * return all medics
     */
    public function getMedics(): JsonResponse
    {
        $users = User::query()
            ->with([
                'roles',
                'pv:id,name,pv_id',
                'pv.town:id,name'
            ])
            ->whereHas('roles', function ($q) {
                $q->where('roles.id', 2);
            })
            ->select([
                'id',
                'name',
                'eds',
                'pv_id',
                'validity_eds_start',
                'validity_eds_end'
            ])
            ->get()
            ->groupBy([
                'pv.town.name',
                'pv.name'
            ]);

        return response()->json($users);
    }

    public function getStamps(): JsonResponse
    {
        $stamps = Stamp::query()
            ->select([
                'id',
                'company_name as stamp_head',
                'licence as stamp_licence'
            ])
            ->get()
            ->groupBy('id');

        return response()->json($stamps);
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
                'photo',
                'phone',
                'only_offline_medic_inspections'
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

        if ($driver->only_offline_medic_inspections) {
            return response()->json(['message' => 'Водителю ограничен дистанционный выпуск, обратитесь к медицинскому сотруднику на Пункте Выпуска!'], 400);
        }

        return response()->json($driver);
    }

    public function setDriverPhone(Request $request, $id): JsonResponse
    {
        $apiClient = $request->user('api');

        if ($apiClient->blocked) {
            return response()->json(['message' => 'Этот терминал заблокирован!'], 400);
        }

        /** @var Driver $driver */
        $driver = Driver::query()
            ->with(['company'])
            ->where('hash_id', $id)
            ->first();

        if (!$driver) {
            return response()->json(['message' => 'Водитель с указанным ID не найден!'], 400);
        }

        if ($driver->dismissed === 'Да') {
            return response()->json(['message' => 'Водитель с указанным ID уволен!'], 303);
        }

        if ($driver->company->dismissed === 'Да') {
            return response()->json(['message' => 'Компания указанного водителя заблокирована!'], 303);
        }

        if ($driver->only_offline_medic_inspections) {
            return response()->json(['message' => 'Водителю ограничен дистанционный выпуск, обратитесь к медицинскому сотруднику на Пункте Выпуска!'], 400);
        }

        $phone = new Phone($request->input('phone'));
        if (!$phone->isValid()) {
            return response()->json(['message' => 'Некорректный номер телефона!', 422]);
        }

        $driver->setAttribute('phone', $phone->getSanitized());
        $driver->save();

        return response()->json([
            'message' => 'Номер телефона водителя успешно обновлен!'
        ]);
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

        $data = $inspection->toArray();

        $stamp = optional(optional($inspection->terminal))->stamp;
        if ($stamp) {
            $data['stamp_head'] = $stamp->company_name;
            $data['stamp_licence'] = $stamp->licence;
        }

        $validity = UserEdsTrait::getValidityString(
            $inspection['user_validity_eds_start'] ?? null,
            $inspection['user_validity_eds_end'] ?? null
        );
        if ($validity) {
            $data['validity'] = $validity;
        }

        return response()->json($data);
    }

    /*
    * Inspection update type
    */
    public function changeType(Request $request, $id)
    {
        $inspection = Anketa::find($id);
        if ($inspection) {
            $inspection->update([
                'type_anketa' => 'medic',
                'flag_pak' => 'СДПО-А'
            ]);
        }
    }

    /*
    * Driver update photo
    */
    public function setDriverPhoto(Request $request, $id)
    {
        if (!$request->photo) {
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

    public function storeCrash(StoreSdpoCrashRequest $request): JsonResponse
    {
        /** @var User $user */
        $user = $request->user('api');

        if ($user->blocked) {
            return response()->json(['message' => 'Этот терминал заблокирован!'], 400);
        }

        try {
            DB::beginTransaction();

            SdpoCrashLog::create(
                $request->all() +
                [
                    'terminal_id' => $user->getAttribute('id'),
                    'point_id' => $user->getAttribute('pv_id')
                ]
            );

            DB::commit();

            return response()->json([
                'message' => 'Ошибка успешно передана на сервер!'
            ]);
        } catch (Throwable $exception) {
            DB::rollBack();

            return response()->json([
                'message' => "Ошибка не была передана на сервер! " . $exception->getMessage()
            ]);
        }
    }
}
