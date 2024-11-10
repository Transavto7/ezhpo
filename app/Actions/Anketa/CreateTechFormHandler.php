<?php

namespace App\Actions\Anketa;

use App\Car;
use App\Company;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Models\Forms\Form;
use App\Models\Forms\TechForm;
use App\Services\DuplicatesCheckerService;
use App\Enums\FormTypeEnum;
use App\Events\Forms\DriverDismissed;
use App\Services\FormHash\FormHashGenerator;
use App\Services\FormHash\TechHashData;
use DateTimeImmutable;
use Illuminate\Support\Carbon;

class CreateTechFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
    protected function validateData()
    {
        if ($this->data['is_dop'] ?? 0 === 1) {
            return;
        }

        $carExist = Car::where('hash_id', $this->data['anketa'][0]['car_id'])->first();
        if (!$carExist) {
            $this->errors[] = 'Не найдена машина.';
            return;
        }

        $driverExist = Driver::where('hash_id', $this->data['driver_id'])->first();
        if (!$driverExist) {
            $this->errors[] = 'Не найден водитель.';
        }
    }

    protected function fetchExistForms()
    {
        $cars = [];
        foreach ($this->data['anketa'] ?? [] as $form) {
            $cars[] = $form['car_id'] ?? 0;
        }

        $cars = array_filter(array_unique($cars), function ($car) {
            return ($car !== null) && ($car !== 0);
        });

        $this->existForms = DuplicatesCheckerService::getExistTechForms($cars);
    }

    protected function createForm(array $form)
    {
        $carId = $form['car_id'] ?? 0;
        $car = Car::where('hash_id', $carId)->first();

        $driverId = $form['driver_id'] ?? ($this->data['driver_id'] ?? 0);
        $driver = Driver::where('hash_id', $driverId)->first();

        $defaultData = [
            'date' => date('Y-m-d H:i:s'),
            'realy' => 'нет',
            'created_at' => $this->time
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
            }
        }

        /**
         * Водитель
         */
        if (isset($form['driver_id'])) {
            $driverDop = Driver::where('hash_id', $form['driver_id'])->first();

            if ($driverDop) {
                $form['driver_id'] = $driverDop->hash_id;

                $driver = $driverDop;
            }
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
                $this->errors[] = BlockActionReasonsEnum::COMPANY_BLOCK;
                return;
            }

            $form['company_id'] = $company->hash_id;
        } else if ($car) {
            $carCompany = Company::find($car->company_id);

            if (!$carCompany) {
                $this->errors[] = 'У Автомобиля не найдена компания';
                return;
            }

            if ($carCompany->dismissed === 'Да') {
                $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
                return;
            }

            $form['company_id'] = $carCompany->hash_id;
        }

        if ($car) {
            if ($car->dismissed === 'Да') {
                $this->errors[] = 'Автомобиль уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';
            }

            $form['car_id'] = $car->hash_id;

            $this->checkRedDates(
                date('Y-m-d', strtotime($form['date'])),
                $car
            );
        }

        $isFormUnique = $this->findDuplicates($form);
        if (!$isFormUnique) {
            return;
        }

        /**
         * Генерация номера ПЛ
         */
        if (empty($form['number_list_road']) && !$form['is_dop']) {
            $form['number_list_road'] = $car->hash_id . '-' . date('d.m.Y', strtotime($form['date']));
        }

        if ($form['is_dop']) {
            $form['point_reys_control'] = 'Пройден';
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

        if ($form['driver_id'] && $form['car_id'] && $form['date'] && $form['type_view']) {
            $form['day_hash'] = FormHashGenerator::generate(
                new TechHashData(
                    $form['driver_id'],
                    $form['car_id'],
                    new DateTimeImmutable($form['date']),
                    $form['type_view']
                )
            );
        }

        $formModel = new Form($form);
        $formModel->save();

        $formDetailsModel = new TechForm($form);
        $formDetailsModel->setAttribute('forms_uuid', $formModel->uuid);
        $formDetailsModel->save();

        $this->createdForms->push($formModel);

        if ($form['point_reys_control'] === 'Не пройден') {
            event(new DriverDismissed($formModel));
        }
    }
}
