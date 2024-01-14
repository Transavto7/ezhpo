<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use Illuminate\Support\Carbon;

class CreatePrintPlFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
    const FORM_TYPE = FormTypeEnum::PRINT_PL;

    protected function createForm(array $form)
    {
        $driverId = $form['driver_id'] ?? ($this->data['driver_id'] ?? 0);
        $driver = Driver::where('hash_id', $driverId)->first();

        $defaultData = [
            'date' => date('Y-m-d H:i:s'),
            'admitted' => 'Допущен',
            'realy' => 'нет',
            'created_at' => $this->time
        ];

        $form = $this->mergeFormData($form, $defaultData);

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

        if ($driver) {
            if ($driver->dismissed === 'Да') {
                $this->errors[] = 'Водитель уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';
            }

            if (!$driver->company_id) {
                $this->errors[] = 'У Водителя не найдена компания';

                return;
            }

            $driverCompany = Company::find($driver->company_id);

            if (!$driverCompany) {
                $this->errors[] = 'У Водителя не верно указано ID компании';

                return;
            }

            if ($driverCompany->dismissed === 'Да') {
                $this->errors[] = 'Компания в черном списке. Необходимо связаться с руководителем!';

                return;
            }

            if ($driver->year_birthday && $driver->year_birthday !== '0000-00-00') {
                $form['driver_year_birthday'] = $driver->year_birthday;
            }

            $form['driver_gender'] = $driver->gender ?? '';
            $form['driver_fio'] = $driver->fio;
            $form['driver_group_risk'] = $driver->group_risk;

            $form['company_id'] = $driverCompany->hash_id;
            $form['company_name'] = $driverCompany->name;
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
