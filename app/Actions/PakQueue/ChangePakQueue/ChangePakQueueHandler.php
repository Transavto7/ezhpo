<?php

namespace App\Actions\PakQueue\ChangePakQueue;

use App\Anketa;
use App\Company;
use App\Driver;
use App\Enums\FormTypeEnum;
use App\Http\Controllers\SmsController;
use App\Settings;
use Exception;

class ChangePakQueueHandler
{
    /**
     * @throws Exception
     */
    public function handle(ChangePakQueueAction $action)
    {
        $admitted = $action->getAdmitted();
        $id = $action->getId();

        $allowedAdmitted = ['Допущен', 'Не идентифицирован', 'Не допущен'];
        if (!in_array($admitted, $allowedAdmitted)) {
            throw new Exception('Недопустимый результат осмотра');
        }

        $form = Anketa::find($id);

        if (!$form) {
            throw new Exception('Осмотр не найден');
        }

        if ($form->type_anketa !== FormTypeEnum::PAK_QUEUE) {
            throw new Exception('Осмотр не находится в очереди утверждения');
        }

        //TODO: добавить проверку на права пользователя утверждать осмотры

        $this->updateForm($form, $action);

        /**
         * ОТПРАВКА SMS
         */
        if ($admitted !== 'Не допущен') {
            return;
        }

        $this->sendSms($form);
    }

    protected function updateForm(Anketa $form, ChangePakQueueAction $action)
    {
        $user = $action->getMedic();

        $form->type_anketa = 'medic';
        $form->flag_pak = 'СДПО Р';
        $form->admitted = $action->getAdmitted();

        if ($form->admitted === 'Не идентифицирован') {
            $form->comments = Settings::setting('not_identify_text') ?? 'Водитель не идентифицирован';
        }

        $form->user_id = $user->id;
        $form->user_name = $user->name;
        $form->operator_id = $user->id;
        $form->user_eds = $user->eds;
        $form->user_validity_eds_start = $user->validity_eds_start;
        $form->user_validity_eds_end = $user->validity_eds_end;

        $form->save();
    }

    protected function sendSms(Anketa $form)
    {
        $company = Company::query()->where('hash_id', $form->company_id)->first();
        $driver = Driver::query()->where('hash_id', $form->driver_id)->first();

        $phoneToCall = Settings::setting('sms_text_phone');
        $message = Settings::setting('sms_text_driver') . " $driver->fio . $phoneToCall";

        $sms = new SmsController();
        $sms->sms($company->where_call, $message);
    }
}
