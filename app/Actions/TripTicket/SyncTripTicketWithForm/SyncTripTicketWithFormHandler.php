<?php

namespace App\Actions\TripTicket\SyncTripTicketWithForm;

use App\Enums\FormTypeEnum;
use App\Models\TripTicket;

final class SyncTripTicketWithFormHandler
{
    public function handle(SyncTripTicketWithFormAction $action): TripTicket
    {
        if ($action->getTripTicket()->{$action->getFormType().'_form_id'} !== null) {
            $type = $action->getFormType() === FormTypeEnum::MEDIC ? 'медицинским' : 'техническим';

            throw new \Exception("Путевой лист уже имеет связь с $type осмотром");
        }

        $action->getTripTicket()->update([
            $action->getFormType().'_form_id' => $action->getForm()->id
        ]);

        return $action->getTripTicket();
    }
}
