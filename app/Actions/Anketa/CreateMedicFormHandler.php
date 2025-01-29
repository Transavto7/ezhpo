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

    /**
     * @throws Exception
     */
    protected function createForm(array $form)
    {
        $defaultData = [
            'tonometer' => strval(Tonometer::random(Driver::where('hash_id', $this->data['driver_id'] ?? 0)->first())),
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
        $formIsDop = $form['is_dop'] ?? 0;
        $form['is_dop'] = $formIsDop;

        $companyId = $form['company_id'] ?? null;
        if ($formIsDop && empty($companyId)) {
            $this->errors[] = 'Не указана компания.';
            return;
        }

        if (!empty($companyId)) {
            $company = Company::where('hash_id', $companyId)->first();
            if (!$company) {
                $this->errors[] = 'Компания не найдена.';
                return;
            }

            if ($company->dismissed === 'Да') {
                $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
                return;
            }
        }

        $driverId = $form['driver_id'] ?? null;
        if (!$formIsDop && empty($driverId)) {
            $this->errors[] = 'Не указан Водитель.';
            return;
        }

        if (!empty($driverId)) {
            $driver = Driver::where('hash_id', $driverId)->first();

            if (!$driver) {
                $this->errors[] = 'Водитель не найден.';
                return;
            }

            if ($driver->dismissed === 'Да') {
                $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::DRIVER_BLOCK);
                return;
            }

            if (!$driver->company_id || !$driver->company) {
                $this->errors[] = 'У Водителя не найдена Компания';
                return;
            }

            if (!empty($companyId) && ($driver->company->hash_id !== $companyId)) {
                $this->errors[] = 'Компания Водителя не совпадает с Компанией осмотра.';
                return;
            }

            if (empty($companyId)) {
                $companyId = $driver->company->hash_id;
                $form['company_id'] = $companyId;
            }

            if ($driver->company->dismissed === 'Да') {
                $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
                return;
            }

            if ($driver->end_of_ban && $this->time < $driver->end_of_ban) {
                $this->errors[] = 'Водитель отстранен до ' . Carbon::parse($driver->end_of_ban);
                return;
            }

            /** @var Driver $driver */
            $driver->checkGroupRisk(
                $form['tonometer'],
                $form['test_narko'],
                $form['proba_alko']
            );

            $form['driver_group_risk'] = $driver->group_risk;

            $this->checkRedDates(
                date('Y-m-d', strtotime($form['date'])),
                $driver
            );
        }

        $isFormUnique = $this->findDuplicates($form);
        if (!$isFormUnique) {
            return;
        }

        $date = $form['date'] ?? null;
        if (!$formIsDop && empty($date)) {
            $this->errors[] = 'Не указана дата осмотра!';

            return;
        }

        $periodPl = $form['period_pl'] ?? null;
        if ($formIsDop && empty($date) && empty($periodPl)) {
            $this->errors[] = 'Не указан ни период, ни дата осмотра!';

            return;
        }

        if ($formIsDop && $date && $periodPl) {
            $dateFrom = Carbon::createFromFormat('!Y-m', $periodPl)->startOfMonth();
            $dateTo = Carbon::createFromFormat('!Y-m', $periodPl)->endOfMonth();
            $dateCarbon = Carbon::parse($date);
            if ($dateCarbon->lessThan($dateFrom) || $dateCarbon->greaterThan($dateTo)) {
                $this->errors[] = 'Дата осмотра находится вне периода выдачи ПЛ!';

                return;
            }
        }

        if ($formIsDop && $date && empty($periodPl)) {
            $form['period_pl'] = date('Y-m', strtotime($date));
        }

        /**
         * ПРОВЕРЯЕМ статус для поля "Заключение"
         */
        if (!$formIsDop && !$this->admit($form, $driver ?? null)) {
            $form['admitted'] = 'Не допущен';
        }

        /**
         * Diff Date (ОСМОТР РЕАЛЬНЫЙ ИЛИ НЕТ)
         */
        $diffDateCheck = Carbon::now()
            ->addHours($user->timezone ?? 3)
            ->diffInMinutes($date);
        if ($date && $diffDateCheck <= 60*12) {
            $form['realy'] = 'да';
        }

        if ($driverId && $date) {
            $form['day_hash'] = FormHashGenerator::generate(
                new MedicHashData(
                    $driverId,
                    new DateTimeImmutable($date),
                    $form['type_view']
                )
            );
        }

        $formModel = new Form($form);
        $formModel->save();

        $formDetailsModel = new MedicForm($form);
        $formDetailsModel->setAttribute('forms_uuid', $formModel->uuid);
        $formDetailsModel->save();

        if ($this->needStoreNormalizedPressure) {
            MedicFormNormalizedPressure::store(
                $formModel->id,
                Tonometer::fromString($formDetailsModel->tonometer)->getNormalized()
            );
        }

        $this->createdForms->push($formModel);

        if ($form['admitted'] === 'Не допущен') {
            event(new DriverDismissed($formModel));
        }
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
