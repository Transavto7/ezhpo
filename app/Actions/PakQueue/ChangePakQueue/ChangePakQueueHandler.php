<?php

namespace App\Actions\PakQueue\ChangePakQueue;

use App\Enums\FormTypeEnum;
use App\Events\Forms\DriverDismissed;
use App\Models\Forms\Form;
use App\Models\Forms\MedicForm;
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

        $form = Form::find($id);

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

        event(new DriverDismissed($form));
    }

    protected function updateForm(Form $form, ChangePakQueueAction $action)
    {
        $user = $action->getMedic();

        $form->type_anketa = FormTypeEnum::MEDIC;

        /** @var MedicForm $details */
        $details = $form->details;

        $details->flag_pak = 'СДПО Р';
        $details->admitted = $action->getAdmitted();

        if ($details->admitted === 'Не идентифицирован') {
            $details->comments = Settings::setting('not_identify_text') ?? 'Водитель не идентифицирован';
        }

        $form->user_id = $user->id;
        $details->operator_id = $user->id;
        $form->user_eds = $user->eds;
        $form->user_validity_eds_start = $user->validity_eds_start;
        $form->user_validity_eds_end = $user->validity_eds_end;

        $form->save();
        $details->save();
    }
}
