<?php

namespace App\Actions\Anketa;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use Illuminate\Support\Carbon;

class CreateReportCardFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
    const FORM_TYPE = FormTypeEnum::REPORT_CARD;

    protected function validateData()
    {
        $driverId = $this->data['driver_id'] ?? null;
        if (!$driverId) {
            $this->errors[] = 'Не указан водитель.';

            return;
        }

        $driver = Driver::where('hash_id', $driverId)->first();
        if (!$driver){
            $this->errors[] = 'Не найден водитель.';
        }
    }

    protected function createForm(array $form)
    {
        $driverId = $form['driver_id'];
        $driver = Driver::where('hash_id', $driverId)->first();

        $defaultData = [
            'date' => date('Y-m-d H:i:s'),
            'admitted' => 'Допущен',
            'realy' => 'нет'
        ];

        $form = $this->mergeFormData($form, $defaultData);

        /**
         * Водитель
         */
        if (isset($form['driver_id'])) {
            $driverDop = Driver::where('hash_id', $form['driver_id'])->first();

            if ($driverDop) {
                $form['driver_id'] = $driverDop->hash_id;
                $form['driver_fio'] = $driverDop->fio;
            }
        }

        /**
         * Проверка водителя по: тесту наркотиков, возрасту
         */
        if ($driver) {
            if($driver->dismissed === 'Да') {
                $this->errors[] = 'Водитель уволен. Осмотр зарегистрирован. Обратитесь к менеджеру';
            }

            if(!$driver->company_id) {
                $this->errors[] = 'У Водителя не найдена компания';

                return;
            }

            $company = Company::where('id', $driver->company_id)->first();

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

            $driver->date_report_driver = $form['date'];
            $driver->save();
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
