<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Dto\NotifyParams;
use App\Enums\FormTypeEnum;
use App\Http\Controllers\SmsController;
use App\Settings;
use App\ValueObjects\PressureLimits;
use App\ValueObjects\Pulse;
use App\ValueObjects\PulseLimits;
use App\ValueObjects\Temperature;
use App\ValueObjects\Tonometer;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CreateMedicFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
    const FORM_TYPE = FormTypeEnum::MEDIC;

    protected function fetchExistForms()
    {
        $drivers = [$this->data['driver_id'] ?? 0];

        $this->existForms = Anketa::query()
            ->where('driver_id', $drivers)
            ->where('type_anketa', 'medic')
            ->where('in_cart', 0)
            ->whereNotNull('date')
            ->where(function (Builder $query) {
                $query
                    ->where('is_dop', '<>', 1)
                    ->orWhereNotNull('result_dop');
            })
            ->orderBy('date', 'desc')
            ->get();
    }

    protected function validateData()
    {
        if ($this->data['is_dop'] ?? 0 === 1) return;

        $driverId = $this->data['driver_id'] ?? null;
        if (!$driverId) {
            $this->errors[] = 'Не указан водитель.';
            return;
        }

        $driverExist =  Driver::where('hash_id', $driverId)->first();
        if (!$driverExist) {
            $this->errors[] = 'Не найден водитель.';
            return;
        }

        if ($driverExist->end_of_ban && $this->time < $driverExist->end_of_ban) {
            $this->errors[] = 'Водитель отстранен до '.Carbon::parse($driverExist->end_of_ban);
        }
    }

    protected function createForm(array $form)
    {
        $driverId = $this->data['driver_id'] ?? 0;
        $driver = Driver::where('hash_id', $driverId)->first();

        $defaultData = [
            'tonometer' => strval(Tonometer::random($driver)),
            't_people' => Temperature::random()->getTemperature(),
            'pulse' => Pulse::random()->getPulse(),
            'date' => date('Y-m-d H:i:s'),
            'test_narko' => 'Отрицательно',
            'proba_alko' => 'Отрицательно',
            'med_view' => 'В норме',
            'admitted' => 'Допущен',
            'realy' => 'нет',
            'created_at' => $this->time
        ];

        $form = $this->mergeFormData($form, $defaultData);
        $form['is_dop'] = $form['is_dop'] ?? 0;

        $company = null;
        /**
         * Компания
         */
        if (isset($form['company_id'])) {
            $companyDop = Company::where('hash_id', $form['company_id'])->first();

            if ($companyDop) {
                $form['company_id'] = $companyDop->hash_id;
                $form['company_name'] = $companyDop->name;
            }
        }

        /**
         * Водитель
         */
        if (isset($form['driver_id'])) {
            $driverDop = Driver::where('hash_id', $form['driver_id'])->first();

            if ($driverDop) {
                $form['driver_id'] = $driverDop->hash_id;
                $form['driver_fio'] = $driverDop->fio;

                $driver = $driverDop;
            }
        }

        /**
         * Проверка водителя по: тесту наркотиков, возрасту
         */
        if ($driver) {
            /** @var Driver $driver */
            $driver->checkGroupRisk(
                $form['tonometer'],
                $form['test_narko'],
                $form['proba_alko']
            );

            if ($driver->dismissed === 'Да') {
                $this->errors[] = 'Водитель уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';
            }

            if (!$driver->company_id) {
                $this->errors[] = 'У Водителя не найдена компания';

                return;
            }

            $company = Company::find($driver->company_id);

            if (!$company) {
                $this->errors[] = 'У Водителя не верно указано ID компании';

                return;
            }

            if ($company->dismissed === 'Да') {
                $this->errors[] = 'Компания в черном списке. Необходимо связаться с руководителем!';

                return;
            }

            if ($driver->year_birthday && $driver->year_birthday !== '0000-00-00') {
                $form['driver_year_birthday'] = $driver->year_birthday;
            }

            $form['driver_gender'] = $driver->gender ?? '';
            $form['driver_fio'] = $driver->fio;
            $form['driver_group_risk'] = $driver->group_risk;

            $form['company_id'] = $company->hash_id;
            $form['company_name'] = $company->name;

            $this->checkRedDates(
                date('Y-m-d', strtotime($form['date'])),
                $driver
            );
        }

        /**
         * ПРОВЕРЯЕМ статус для поля "Заключение"
         */
        if (!$this->admit($form, $driver)) {
            $form['admitted'] = 'Не допущен';
        }

        /**
         * Проверка на дубликат из ТЗ
         *
         * Мы должны дать техническую возможность внесение осмотров любой даты (год назад, месяц назад.
         * При внесении осмотра, система должна смотреть, есть ли подобный.
         *
         * Например, сегодня 13.02.21 в 09.00 до 10.00.
         */
        $isFormUnique = $this->findDuplicates($form);
        if (!$isFormUnique) {
            return;
        }

        /**
         * Diff Date (ОСМОТР РЕАЛЬНЫЙ ИЛИ НЕТ)
         */
        $date = $form['date'] ?? null;
        $diffDateCheck = Carbon::now()
            ->addHours($user->timezone ?? 3)
            ->diffInMinutes($date);
        if ($date && $diffDateCheck <= 60*12) {
            $form['realy'] = 'да';
        }

        $formModel = new Anketa($form);

        /**
         * ОТПРАВКА SMS
         */
        if ($form['admitted'] === 'Не допущен') {
            $this->notify(new NotifyParams(
                $company,
                $driver,
                null,
                $formModel
            ));
        }

        $formModel->save();
        $this->createdForms->push($formModel);
    }

    protected function notify(NotifyParams $notifyParams)
    {
        $company = $notifyParams->getCompany();
        if (empty($company)) {
            return;
        }

        $phoneToCall = Settings::setting('sms_text_phone');
        $whereCall = $company->where_call;
        $sms = new SmsController();

        $driver = $notifyParams->getDriver();
        if (isset($driver)) {
            $message = Settings::setting('sms_text_driver') . " $driver->fio. $phoneToCall";
            $sms->sms($whereCall, $message);

            return;
        }

        $car = $notifyParams->getCar();
        if (isset($car)) {
            $message = Settings::setting('sms_text_car') . " $car->gos_number. $phoneToCall";
            $sms->sms($whereCall, $message);

            return;
        }

        $form = $notifyParams->getForm();
        $message = Settings::setting('sms_text_default') . ' ' . $form . '.' . ' ' . $phoneToCall;
        $sms->sms($whereCall, $message);
    }

    protected function admit(array $form, Driver $driver = null): bool
    {
        if ($form['is_dop'] ?? 0 === 1) {
            return true;
        }

        $admitted = true;
        if ($form['med_view'] !== 'В норме') {
            $admitted = false;
        }

        $proba_alko = $form['proba_alko'];
        if ($proba_alko === "Положительно") {
            $admitted = false;
            $driver->end_of_ban = Carbon::parse($this->time)->addMinutes($driver->getTimeOfAlcoholBan());
            $driver->save();
        }

        $test_narko = $form['test_narko'];
        if ($test_narko !== 'Отрицательно' && $test_narko !== 'Не проводился') {
            $admitted = false;
        }

        if (!Tonometer::fromString($form['tonometer'])->isAdmitted(PressureLimits::create($driver))) {
            $admitted = false;
            $driver->end_of_ban = Carbon::parse($this->time)->addMinutes($driver->getTimeOfPressureBan());
            $driver->save();
        }

        $pulse = new Pulse(intval($form['pulse']));
        $pulseLimits = PulseLimits::create($driver);
        if (!$pulse->isAdmitted($pulseLimits)) {
            $admitted = false;
        }

        if (!(new Temperature(floatval($form['t_people'])))->isAdmitted()) {
            $admitted = false;
        }

        return $admitted;
    }
}
