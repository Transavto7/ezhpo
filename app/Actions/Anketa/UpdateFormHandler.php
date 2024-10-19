<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Events\Forms\DriverDismissed;
use App\MedicFormNormalizedPressure;
use App\Models\Forms\Form;
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
    public function handle(Form $form, array $data, Authenticatable $user)
    {
        $isPakQueueForm = $form['type_anketa'] === FormTypeEnum::PAK_QUEUE;
        $isMedicForm = $form['type_anketa'] === FormTypeEnum::MEDIC;

        $point = Point::where('id', $data['pv_id'])->first();
        $data['point_id'] = $point->id;

        if (isset($data['anketa'])) {
            $this->findDuplicates($form, $data);

            foreach($data['anketa'][0] as $key => $value) {
                $data[$key] = $value;
            }
        }

        $driverId = $data['driver_id'] ?? 0;
        $driver = Driver::where('hash_id', $driverId)->first();
        if ($driver) {
            $data['driver_group_risk'] = $driver->group_risk;
            $companyId = $driver->company_id;
        }

        $carId = $data['car_id'] ?? 0;
        $car = Car::where('hash_id', $carId)->first();
        if ($car) {
            $companyId = $car->company_id;
        }

        $company = Company::where('id', $companyId ?? 0)->first();
        if ($company) {
            $data['company_id'] = $company->hash_id;
        }

        $timezone      = $user->timezone ?? 3;
        $diffDateCheck = Carbon::parse($form['created_at'])
            ->addHours($timezone)
            ->diffInMinutes($data['date'] ?? null);

        $data['realy'] = 'нет';
        if ($diffDateCheck <= 60 * 12 && $form['date'] ?? null) {
            $data['realy'] = 'да';
        }

        if ($data['driver_id'] && $data['date'] && ($data['type_view'] ?? null)) {
            $hashData = null;

            if ($data['type_anketa'] === FormTypeEnum::MEDIC) {
                $hashData = new MedicHashData(
                    $data['driver_id'],
                    new DateTimeImmutable($data['date']),
                    $data['type_view']
                );
            }
            if (($form['type_anketa'] === FormTypeEnum::TECH) && $data['car_id']) {
                $hashData = new TechHashData(
                    $data['driver_id'],
                    $data['car_id'],
                    new DateTimeImmutable($data['date']),
                    $data['type_view']
                );
            }

            if ($hashData) {
                $data['day_hash'] = FormHashGenerator::generate($hashData);
            }
        }

        $form->fill($data);
        $form->save();

        $form->details->fill($data);
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
            $form->comments = Settings::setting('not_identify_text') ?? 'Водитель не идентифицирован';
        }

        /** @var User $user */
        $form->user_id = $user->id;
        $form->details->operator_id = $user->id;
        $form->user_eds = $user->eds;
        $form->user_validity_eds_start = $user->validity_eds_start;
        $form->user_validity_eds_end = $user->validity_eds_end;

        $form->save();
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

        $mainFormTimestamp = Carbon::parse($data['anketa'][0]['date'])->timestamp;

        $formId = $form->id;
        $existForms = $this->getExistForms($form, $data)->reject(function ($existForm) use ($formId) {
            return $formId === $existForm->id;
        });

        DuplicatesCheckerService::checkExist($existForms, $mainFormTimestamp);
    }

    protected function getExistForms(Form $form, array $data): Collection
    {
        $formNewDate = $data['anketa'][0]['date'];
        $datesDiapason = [
            Carbon::parse($formNewDate)->subSeconds(Anketa::MIN_DIFF_BETWEEN_FORMS_IN_SECONDS),
            Carbon::parse($formNewDate)->addSeconds(Anketa::MIN_DIFF_BETWEEN_FORMS_IN_SECONDS)
        ];

        if ($form->type_anketa === FormTypeEnum::MEDIC) {
            return DuplicatesCheckerService::getExistMedicForms($data['driver_id'], $datesDiapason);
        }

        if ($form->type_anketa === FormTypeEnum::TECH) {
            return DuplicatesCheckerService::getExistTechForms([$data['anketa'][0]['car_id']], $datesDiapason);
        }

        return collect([]);
    }
}
