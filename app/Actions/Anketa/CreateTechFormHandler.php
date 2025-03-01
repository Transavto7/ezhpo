<?php

namespace App\Actions\Anketa;

use App\Car;
use App\Company;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Models\Forms\Form;
use App\Models\Forms\TechForm;
use App\Services\DuplicatesCheckerService;
use App\Events\Forms\DriverDismissed;
use App\Services\FormHash\FormHashGenerator;
use App\Services\FormHash\TechHashData;
use DateTimeImmutable;
use Illuminate\Support\Carbon;

class CreateTechFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
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
        $defaultData = [
            'date' => date('Y-m-d H:i:s'),
            'realy' => 'нет',
            'created_at' => $this->time
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

            //TODO: не нужна ли проверка блокировки временная?
        }

        $carId = $form['car_id'] ?? null;
        if (!$formIsDop && empty($carId)) {
            $this->errors[] = 'Не указан Автомобиль.';
            return;
        }

        if (!empty($carId)) {
            $car = Car::where('hash_id', $carId)->first();

            if (!$car) {
                $this->errors[] = 'Автомобиль не найдено.';
                return;
            }

            if ($car->dismissed === 'Да') {
                $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::CAR_BLOCK);
                return;
            }

            if (!$car->company_id || !$car->company) {
                $this->errors[] = 'У Автомобиля не найдена Компания';
                return;
            }

            if (!empty($companyId) && ($car->company->hash_id !== $companyId)) {
                $this->errors[] = 'Компания Автомобиля не совпадает с Компанией осмотра / Водителя.';
                return;
            }

            if (empty($companyId)) {
                $companyId = $car->company->hash_id;
                $form['company_id'] = $companyId;
            }

            if ($car->company->dismissed === 'Да') {
                $this->errors[] = BlockActionReasonsEnum::getLabel(BlockActionReasonsEnum::COMPANY_BLOCK);
                return;
            }

            if ($formIsDop && ($car->type_auto !== $form['car_type_auto'])) {
                $this->errors[] = 'Категория ТС не совпадает с категорией Автомобиля!';

                return;
            }

            $this->checkRedDates(
                date('Y-m-d', strtotime($form['date'])),
                $car
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
            if ($dateCarbon->lessThan($dateFrom->startOfMonth()) || $dateCarbon->greaterThan($dateTo->endOfMonth())) {
                $this->errors[] = 'Дата осмотра находится вне периода выдачи ПЛ!';

                return;
            }
        }

        if ($formIsDop && $date && empty($periodPl)) {
            $form['period_pl'] = date('Y-m', strtotime($date));
        }

        /**
         * Генерация номера ПЛ
         */
        if (empty($form['number_list_road']) && !$formIsDop && !empty($carId) && $date) {
            $form['number_list_road'] = $carId . '-' . date('d.m.Y', strtotime($date));
        }

        if ($formIsDop) {
            $form['point_reys_control'] = 'Пройден';
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

        if ($driverId && $carId && $date) {
            $form['day_hash'] = FormHashGenerator::generate(
                new TechHashData(
                    $driverId,
                    $carId,
                    new DateTimeImmutable($date),
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
