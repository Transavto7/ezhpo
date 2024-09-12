<?php

namespace App\Actions\Anketa;

use App\Company;
use App\Driver;
use App\Enums\BlockActionReasonsEnum;
use App\Models\Forms\Form;
use App\Models\Forms\PrintPlForm;
use Illuminate\Support\Carbon;

class CreatePrintPlFormHandler extends AbstractCreateFormHandler implements CreateFormHandlerInterface
{
    protected function createForm(array $form)
    {
        $driverId = $form['driver_id'] ?? ($this->data['driver_id'] ?? 0);
        $driver = Driver::where('hash_id', $driverId)->first();

        $defaultData = [
            'date' => date('Y-m-d H:i:s'),
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

        if (!$driver) {
            $this->errors[] = 'Водитель не найден';

            return;
        }

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

        $form['company_id'] = $company->hash_id;

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

        $formModel = new Form($form);
        $formModel->save();

        $formDetailsModel = new PrintPlForm($form);
        $formDetailsModel->setAttribute('forms_uuid', $formModel->uuid);
        $formDetailsModel->save();

        $this->createdForms->push($formModel);
    }
}
