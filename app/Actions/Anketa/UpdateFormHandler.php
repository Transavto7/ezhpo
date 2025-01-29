<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Car;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Enums\FormLogActionTypesEnum;
use App\Enums\FormTypeEnum;
use App\Events\Forms\DriverDismissed;
use App\Events\Forms\FormAction;
use App\Exceptions\InvalidCarTypeAutoForIsDopTechForm;
use App\MedicFormNormalizedPressure;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
use App\Models\Forms\TechForm;
use App\Point;
use App\Services\DuplicatesCheckerService;
use App\Services\FormHash\FormHashGenerator;
use App\Services\FormHash\MedicHashData;
use App\Services\FormHash\TechHashData;
use App\Settings;
use App\User;
use App\ValueObjects\PressureLimits;
use App\ValueObjects\Tonometer;
use DateTimeImmutable;
use Exception;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class UpdateFormHandler
{
    /**
     * @throws InvalidCarTypeAutoForIsDopTechForm
     * @throws Exception
     */
    public function handle(Form $form, array $data, Authenticatable $user)
    {
        $formType = $form['type_anketa'];
        $isPakQueueForm = $formType === FormTypeEnum::PAK_QUEUE;
        $isMedicForm = $formType === FormTypeEnum::MEDIC;
        $isTechForm = $formType === FormTypeEnum::TECH;

        if (isset($data['anketa'])) {
            $this->findDuplicates($form, $data);

            foreach($data['anketa'][0] as $key => $value) {
                $data[$key] = $value;
            }
        }

        $pointId = $data['pv_id'] ?? null;
        if ($pointId) {
            $point = Point::where('id', $data['pv_id'])->first();
            if (empty($point)) {
                throw new Exception('ПВ не найден.');
            }

            $data['point_id'] = $pointId;
        }

        $driverId = $data['driver_id'] ?? null;
        if ($driverId) {
            $driver = Driver::where('hash_id', $driverId)->first();
            if (empty($driver)) {
                throw new Exception('Водитель не найден.');
            }

            if ($driver->dismissed === 'Да') {
                throw new Exception(BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::DRIVER_BLOCK));
            }

            if (!$driver->company_id || !$driver->company) {
                throw new Exception('У Водителя не найдена Компания');
            }

            if ($driver->company->hash_id !== $form->company_id) {
                throw new Exception('Компания Водителя не совпадает с Компанией осмотра.');
            }

            if ($driver->company->dismissed === 'Да') {
                throw new Exception(BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK));
            }

            //TODO: не нужна ли проверка блокировки временная?

            $data['driver_group_risk'] = $driver->group_risk;
        }

        $carId = $data['car_id'] ?? null;
        if ($carId) {
            $car = Car::where('hash_id', $carId)->first();

            if (empty($car)) {
                throw new Exception('Автомобиль не найден.');
            }

            if ($car->dismissed === 'Да') {
                throw new Exception(BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::CAR_BLOCK));
            }

            if (!$car->company_id || !$car->company) {
                throw new Exception('У Автомобиля не найдена Компания');
            }

            if ($car->company->hash_id !== $form->company_id) {
                throw new Exception('Компания Автомобиля не совпадает с Компанией осмотра.');
            }

            if ($car->company->dismissed === 'Да') {
                throw new Exception(BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK));
            }

            if ($isTechForm) {
                /** @var TechForm $details */
                $details = $form->details;

                if ($details->is_dop && $details->car_type_auto && ($details->car_type_auto !== $car->type_auto)) {
                    throw new InvalidCarTypeAutoForIsDopTechForm();
                }
            }
        }

        $date = $data['date'] ?? null;
        $periodPl = null;
        if ($isMedicForm || $isTechForm) {
            /** @var TechForm|MedicForm $details */
            $details = $form->details;

            $periodPl = $data['period_pl'] ?? $details->period_pl;
        }

        if ($date && $periodPl) {
            $dateFrom = Carbon::createFromFormat('!Y-m', $periodPl)->startOfMonth();
            $dateTo = Carbon::createFromFormat('!Y-m', $periodPl)->endOfMonth();
            $dateCarbon = Carbon::parse($date);
            if ($dateCarbon->lessThan($dateFrom->startOfMonth()) || $dateCarbon->greaterThan($dateTo->endOfMonth())) {
                throw new Exception('Дата осмотра находится вне периода выдачи ПЛ!');
            }
        }

        if ($date) {
            $timezone = $user->timezone ?? 3;
            $diffDateCheck = Carbon::parse($form['created_at'])
                ->addHours($timezone)
                ->diffInMinutes($date);

            $data['realy'] = $diffDateCheck <= 60 * 12 ? 'да' : 'нет';
        }

        //TODO: перерасчет хэша вынести в ивент
        $dateForHash = $date ?? $form->date ?? null;
        if ($dateForHash && ($driverId || $carId || $date)) {
            if ($isMedicForm) {
                $driverIdForHash = $driverId ?? $form->driver_id;
                if ($driverIdForHash) {
                    $hashData = new MedicHashData(
                        $driverIdForHash,
                        new DateTimeImmutable($dateForHash),
                        $form->details->type_view
                    );

                    $data['day_hash'] = FormHashGenerator::generate($hashData);
                }
            }

            if ($isTechForm) {
                $carIdForHash = $carId ?? $form->details->car_id;
                $driverIdForHash = $driverId ?? $form->driver_id;
                if ($carIdForHash && $driverIdForHash) {
                    $hashData = new TechHashData(
                        $driverIdForHash,
                        $carIdForHash,
                        new DateTimeImmutable($dateForHash),
                        $form->details->type_view
                    );

                    $data['day_hash'] = FormHashGenerator::generate($hashData);
                }
            }
        }

        $form->fill($data);
        $form->details->fill($data);

        /** @var User $user */
        event(new FormAction($user, $form, FormLogActionTypesEnum::UPDATING));

        $form->save();
        $form->details->save();

        if ($isPakQueueForm) {
            $this->updatePakQueueForm($form, $user);
            $this->notifyCancel($form);
        }

        if ($isMedicForm) {
            $this->normalizeMedicPressure($form);
        }
    }

    protected function normalizeMedicPressure(Form $form)
    {
        $driver = Driver::where('hash_id', $form->driver_id)->first();
        $pressure = Tonometer::fromString($form->details->tonometer);
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

    protected function updatePakQueueForm(Form $form, Authenticatable $user)
    {
        if ($form->details->admitted === 'Не идентифицирован') {
            $form->details->comments = Settings::setting('not_identify_text') ?? 'Водитель не идентифицирован';
        }

        /** @var User $user */
        $form->user_id = $user->id;
        $form->details->operator_id = $user->id;
        $form->user_eds = $user->eds;
        $form->user_validity_eds_start = $user->validity_eds_start;
        $form->user_validity_eds_end = $user->validity_eds_end;

        $form->save();
        $form->details->save();
    }

    protected function notifyCancel(Form $form)
    {
        if ($form->details->admitted !== 'Не допущен') {
            return;
        }

        event(new DriverDismissed($form));
    }

    /**
     * @throws Exception
     */
    protected function findDuplicates(Form $form, array $data)
    {
        $details = $form->details;

        if ($details->is_dop && ($details->result_dop == null)) {
            return;
        }

        $mainFormDate = $data['anketa'][0]['date'] ?? $form->date ?? null;
        if (empty($mainFormDate)) {
            return;
        }

        $formId = $form->id;
        $existForms = $this->getExistForms($form, $data)->reject(function ($existForm) use ($formId) {
            return $formId === $existForm->id;
        });

        $mainFormTimestamp = Carbon::parse($mainFormDate)->timestamp;
        DuplicatesCheckerService::checkExist($existForms, $mainFormTimestamp);
    }

    protected function getExistForms(Form $form, array $data): Collection
    {
        $formNewDate = $data['anketa'][0]['date'] ?? $form->date ?? null;
        if (empty($formNewDate)) {
            return collect([]);
        }

        $datesDiapason = [
            Carbon::parse($formNewDate)->subSeconds(Anketa::MIN_DIFF_BETWEEN_FORMS_IN_SECONDS),
            Carbon::parse($formNewDate)->addSeconds(Anketa::MIN_DIFF_BETWEEN_FORMS_IN_SECONDS)
        ];

        if ($form->type_anketa === FormTypeEnum::MEDIC) {
            $driverId = $data['driver_id'] ?? $form->driver_id ?? null;

            if (empty($driverId)) {
                return collect([]);
            }

            return DuplicatesCheckerService::getExistMedicForms($driverId, $datesDiapason);
        }

        if ($form->type_anketa === FormTypeEnum::TECH) {
            $carId = $data['anketa'][0]['car_id'] ?? $form->details->car_id ?? null;

            if (empty($carId)) {
                return collect([]);
            }

            return DuplicatesCheckerService::getExistTechForms([$carId], $datesDiapason);
        }

        return collect([]);
    }
}
