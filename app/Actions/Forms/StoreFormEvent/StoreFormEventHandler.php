<?php

namespace App\Actions\Forms\StoreFormEvent;

use App\Anketa;
use App\Models\FormEvent;
use DomainException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class StoreFormEventHandler
{
    public function handle(StoreFormEventCommand $command)
    {
        $form = Anketa::find($command->getFormId());

        if (!$form) {
            throw new NotFoundHttpException('Осмотр не найден');
        }

        if (!$form->uuid) {
            throw new DomainException('У осмотра не указан uuid');
        }

        FormEvent::create([
            'form_uuid' => $form->uuid,
            'event_type' => $command->getEventType()->value(),
            'payload' => $command->getPayload(),
            'user_id' => $command->getUserId(),
        ]);
    }
}
