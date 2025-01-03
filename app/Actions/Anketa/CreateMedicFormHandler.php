<?php

namespace App\Actions\Anketa;

use App\Company;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Enums\FlagPakEnum;
use App\Events\Forms\DriverDismissed;
use App\MedicFormNormalizedPressure;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\Services\DuplicatesCheckerService;
use App\Services\FormHash\FormHashGenerator;
use App\Services\FormHash\MedicHashData;
use App\ValueObjects\PressureLimits;
use App\ValueObjects\Pulse;
use App\ValueObjects\PulseLimits;
use App\ValueObjects\Temperature;
use App\ValueObjects\Tonometer;
use DateTimeImmutable;
use Exception;
use Illuminate\Support\Carbon;

class CreateMedicFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
    protected $needStoreNormalizedPressure = false;

    protected function fetchExistForms()
    {
        $driver = $this->data['driver_id'] ?? 0;

        $this->existForms = DuplicatesCheckerService::getExistMedicForms($driver);
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

    /**
     * @throws Exception
     */
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
            'alcometer_result' => '0',
            'med_view' => 'В норме',
            'admitted' => 'Допущен',
            'realy' => 'нет',
            'created_at' => $this->time,
            'flag_pak' => FlagPakEnum::INTERNAL,
        ];

        $form = $this->mergeFormData($form, $defaultData);
        $form['is_dop'] = $form['is_dop'] ?? 0;

        /**
         * Компания
         */
        if (isset($form['company_id'])) {
            $companyDop = Company::where('hash_id', $form['company_id'])->first();

            if ($companyDop) {
                $form['company_id'] = $companyDop->hash_id;
                $form['company_name'] = $companyDop->name;
            } elseif (! isset($form['driver_id'])) {
                $this->errors[] = 'Компания не найдена';

                return;
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

        if (!$driver && isset($form['driver_id'])) {
            $this->errors[] = 'Водитель не найден';
            return;
        }

        /**
         * Проверка водителя по: тесту наркотиков, возрасту
         */
        if ($driver) {
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
                $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
                return;
            }

            /** @var Driver $driver */
            $driver->checkGroupRisk(
                $form['tonometer'],
                $form['test_narko'],
                $form['proba_alko']
            );

            $form['driver_group_risk'] = $driver->group_risk;
            $form['company_id'] = $company->hash_id;

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

        if ($form['driver_id'] && $form['date'] && $form['type_view']) {
            $form['day_hash'] = FormHashGenerator::generate(
                new MedicHashData(
                    $form['driver_id'],
                    new DateTimeImmutable($form['date']),
                    $form['type_view']
                )
            );
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

        $formModel = new Form();
        $formModel->fill($form);
        $formModel->save();

        $formDetailsModel = new MedicForm();
        $formDetailsModel->fill($form);
        $formDetailsModel->setAttribute('forms_uuid', $formModel->uuid);
        $formDetailsModel->save();

        /**
         * ОТПРАВКА SMS
         */
        if ($form['admitted'] === 'Не допущен') {
            event(new DriverDismissed($formModel));
        }

        if ($this->needStoreNormalizedPressure) {
            MedicFormNormalizedPressure::store(
                $formModel->id,
                Tonometer::fromString($formDetailsModel->tonometer)->getNormalized()
            );
        }

        $this->createdForms->push($formModel);
    }

    protected function admit(array $form, Driver $driver = null): bool
    {
        if (($form['is_dop'] ?? 0) === 1) {
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
        if (($test_narko !== 'Отрицательно') && ($test_narko !== 'Не проводился')) {
            $admitted = false;
        }

        $pressure = Tonometer::fromString($form['tonometer']);
        $pressureLimits = PressureLimits::create($driver);
        if (!$pressure->isAdmitted($pressureLimits)) {
            $admitted = false;
            $driver->end_of_ban = Carbon::parse($this->time)->addMinutes($driver->getTimeOfPressureBan());
            $driver->save();
        }
        $this->needStoreNormalizedPressure = $pressure->needNormalize($pressureLimits);

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
