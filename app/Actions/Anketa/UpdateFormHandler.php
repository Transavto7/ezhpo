<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Http\Controllers\SmsController;
use App\MedicFormNormalizedPressure;
use App\Point;
use App\Settings;
use App\User;
use App\ValueObjects\PressureLimits;
use App\ValueObjects\Tonometer;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;

class UpdateFormHandler
{
    public function handle(Anketa $form, array $data, Authenticatable $user)
    {
        $isPakQueueForm = $form['type_anketa'] === FormTypeEnum::PAK_QUEUE;
        $isMedicForm = $form['type_anketa'] === FormTypeEnum::MEDIC;

        $point = Point::where('id', $data['pv_id'])->first();
        $data['pv_id'] = $point->name;
        $data['point_id'] = $point->id;

        if (isset($data['anketa'])) {
            $this->findDuplicates($form, $data);

            foreach($data['anketa'][0] as $key => $value) {
                $data[$key] = $value;
            }
        }

        unset($data['REFERER']);
        unset($data['anketa']);
        unset($data['_token']);

        foreach($data as $key => $value) {
            $form[$key] = $value;
        }

        $companyId = null;
        $form['company_id'] = '';
        $form['company_name'] = '';

        $driverId = $data['driver_id'] ?? null;
        $driver = Driver::where('hash_id', $driverId)->first();
        if ($driver) {
            $form['driver_fio'] = $driver->fio;
            $form['driver_group_risk'] = $driver->group_risk;
            $form['driver_gender'] = $driver->gender;
            $form['driver_year_birthday'] = $driver->year_birthday;
            $companyId = $driver->company_id;
        }

        $carId = $data['car_id'] ?? null;
        $car = Car::where('hash_id', $carId)->first();
        if ($car) {
            $form['car_mark_model'] = $car->mark_model;
            $form['car_gos_number'] = $car->gos_number;
            $companyId = $car->company_id;
        }

        $company = Company::where('id', $companyId)->first();
        if ($company) {
            $form['company_id'] = $company->hash_id;
            $form['company_name'] = $company->name;
        }

        $timezone      = $user->timezone ?? 3;
        $diffDateCheck = Carbon::parse($form['created_at'])
            ->addHours($timezone)
            ->diffInMinutes($data['date'] ?? null);

        $form['realy'] = 'нет';
        if ($diffDateCheck <= 60 * 12 && $form['date'] ?? null) {
            $form['realy'] = 'да';
        }

        $form->save();

        $this->updateConnectedForm($form);

        if ($isPakQueueForm) {
            $this->updatePakQueueForm($form, $user);
            $this->notifyCancel($form);
        }

        if ($isMedicForm) {
            $this->normalizeMedicPressure($form);
        }
    }

    protected function normalizeMedicPressure(Anketa $form)
    {
        $driver = Driver::where('hash_id', $form->driver_id)->first();
        $pressure = Tonometer::fromString($form->tonometer);
        $pressureLimits = PressureLimits::create($driver);

        if ($pressure->needNormalize($pressureLimits)) {
            MedicFormNormalizedPressure::store(
                $form->id,
                $pressure->getNormalized()
            );
        } else {
            MedicFormNormalizedPressure::reset($form->id);
        }
    }

    protected function updatePakQueueForm(Anketa $form, Authenticatable $user)
    {
        if ($form->admitted === 'Не идентифицирован') {
            $form->comments = Settings::setting('not_identify_text') ?? 'Водитель не идентифицирован';
        }

        /** @var User $user */
        $form->user_id = $user->id;
        $form->user_name = $user->name;
        $form->operator_id = $user->id;
        $form->user_eds = $user->eds;
        $form->user_validity_eds_start = $user->validity_eds_start;
        $form->user_validity_eds_end = $user->validity_eds_end;

        $form->save();
    }

    protected function notifyCancel(Anketa $form)
    {
        if ($form->admitted !== 'Не допущен') {
            return;
        }

        $company = Company::query()->where('hash_id', $form->company_id)->first();
        $driver = Driver::query()->where('hash_id', $form->driver_id)->first();

        $phoneToCall = Settings::setting('sms_text_phone');
        $message = Settings::setting('sms_text_driver') . " $driver->fio . $phoneToCall";

        $sms = new SmsController();
        $sms->sms($company->where_call, $message);
    }

    /**
     * @throws Exception
     */
    protected function findDuplicates(Anketa $form, array $data)
    {
        if ($form->is_dop && $form->result_dop == null) {
            return;
        }

        $mainFormTimestamp = Carbon::parse($data['anketa'][0]['date'])->timestamp;

        foreach($this->getExistForms($form, $data) as $existForm) {
            if (!$existForm->date || $existForm->id === $form->id || ($existForm->is_dop && $existForm->result_dop == null)) {
                continue;
            }

            $hourDiffCheck = round(($mainFormTimestamp - Carbon::parse($existForm->date)->timestamp)/60, 1);

            if ($hourDiffCheck < 1 && $hourDiffCheck >= 0) {
                throw new Exception("Найден дубликат осмотра (ID: $existForm->id, Дата: $existForm->date)");
            }
        }
    }

    protected function getExistForms(Anketa $form, array $data)
    {
        if ($form->type_anketa === 'medic') {
            return Anketa::where('driver_id', $data['driver_id'])
                ->where('type_anketa', 'medic')
                ->where('type_view', $data['anketa'][0]['type_view'])
                ->where('in_cart', 0)
                ->orderBy('date', 'desc')
                ->get();
        }

        if ($form->type_anketa === 'tech') {
            return Anketa::where('car_id', $data['anketa'][0]['car_id'])
                ->where('type_anketa', 'tech')
                ->where('type_view', $data['anketa'][0]['type_view'] ?? '')
                ->where('in_cart', 0)
                ->orderBy('date', 'desc')
                ->get();
        }

        return collect([]);
    }

    protected function updateConnectedForm(Anketa $form)
    {
        if (!$form->connected_hash) {
            return;
        }

        $formCopy = Anketa::where('connected_hash', $form->connected_hash)
            ->where('type_anketa', '!=', $form->type_anketa)
            ->first();

        if (!$formCopy) {
            return;
        }

        $protectedAttributes = [
            'type_anketa',
            'id',
            'created_at',
            'updated_at'
        ];

        foreach ($form->fillable as $key) {
            if (in_array($key, $protectedAttributes)) {
                continue;
            }

            $formCopy->$key = $form[$key];
        }

        $formCopy->save();
    }
}
