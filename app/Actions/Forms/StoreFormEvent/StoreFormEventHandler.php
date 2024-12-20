<?php

namespace App\Actions\Forms\StoreFormEvent;

use App\Enums\FormLogActionTypesEnum;
use App\Models\Forms\Form;
use App\Models\FormEvent;
use DomainException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class StoreFormEventHandler
{
    public function handle(StoreFormEventCommand $command)
    {
        $form = Form::find($command->getFormId());

        if (!$form) {
            throw new NotFoundHttpException('Осмотр не найден');
        }

        if (!$form->uuid) {
            throw new DomainException('У осмотра не указан uuid');
        }

        FormEvent::create([
            'form_uuid' => $form->uuid,
            'event_type' => FormLogActionTypesEnum::SET_FEEDBACK,
            'payload' => $command->getPayload(),
            'user_id' => $command->getUserId(),
            'model_type' => get_class($form->details),
        ]);
    }
}
