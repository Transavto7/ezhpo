<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Car;
use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Carbon;

class CreateTechFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
    const FORM_TYPE = FormTypeEnum::MEDIC;

    protected function validateData()
    {
        if ($this->data['is_dop'] ?? 0 === 1) {
            return;
        }

        $carExist = Car::where('hash_id', $this->data['anketa'][0]['car_id'])->first();
        if (!$carExist) {
            $this->errors[] = 'Не найдена машина.';
        }

        $driverExist = Driver::where('hash_id', $this->data['driver_id'])->first();
        if (!$driverExist) {
            $this->errors[] = 'Не найден водитель.';
        }
    }

    protected function fetchExistForms()
    {
        $cars = [$this->data['car_id'] ?? 0];
        foreach ($this->data['anketa'] ?? [] as $form) {
            $cars[] = $form['car_id'] ?? 0;
        }

        $this->existForms = Anketa::query()
            ->whereIn('car_id', $cars)
            ->where('type_anketa', 'tech')
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

    protected function createForm(array $form)
    {
        $carId = $form['car_id'] ?? 0;
        $car = Car::where('hash_id', $carId)->first();

        $driverId = $form['driver_id'] ?? ($this->data['driver_id'] ?? 0);
        $driver = Driver::where('hash_id', $driverId)->first();

        $company = null;

        $defaultData = [
            'date' => date('Y-m-d H:i:s'),
            'admitted' => 'Допущен',
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
                $form['company_name'] = $companyDop->name;

                $company = $companyDop;
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
            if ($driver->dismissed === 'Да') {
                $this->errors[] = 'Водитель уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';
            }

            if (!$driver->company_id) {
                $message = 'У Водителя не найдена компания';

                $this->errors[] = $message;

                $this->saveSdpoFormWithError($form, $message);

                return;
            }

            $company = Company::find($driver->company_id);

            if (!$company) {
                $message = 'У Водителя не верно указано ID компании';

                $this->errors[] = $message;

                $this->saveSdpoFormWithError($form, $message);

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
        } else if ($car) {
            $carCompany = Company::find($car->company_id);

            if (!$carCompany) {
                $message = 'У Автомобиля не найдена компания';

                $this->errors[] = $message;

                $this->saveSdpoFormWithError($form, $message);

                return;
            }

            if ($carCompany->dismissed === 'Да') {
                $this->errors[] = 'Компания в черном списке. Необходимо связаться с руководителем!';

                return;
            }

            $form['company_id'] = $carCompany->hash_id;
            $form['company_name'] = $carCompany->name;

            $company = $carCompany;
        }

        if ($car) {
            if ($car->dismissed === 'Да') {
                $this->errors[] = 'Автомобиль уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';
            }

            $form['car_id'] = $car->hash_id;
            $form['car_mark_model'] = $car->mark_model;
            $form['car_gos_number'] = $car->gos_number;

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
            $form['number_list_road'] = $company->id . '-' . date('d.m.Y', strtotime($form['date']));
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

        $formModel = new Anketa($form);

        $formModel->save();
        $this->createdForms->push($formModel);
    }
}
