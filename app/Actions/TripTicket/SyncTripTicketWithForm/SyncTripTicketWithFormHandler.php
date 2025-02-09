<?php

namespace App\Actions\TripTicket\SyncTripTicketWithForm;

use App\Enums\FormTypeEnum;
use App\Models\TripTicket;
use Illuminate\Database\Eloquent\Builder;

final class SyncTripTicketWithFormHandler
{
    public function handle(SyncTripTicketWithFormAction $action): TripTicket
    {
        if ($action->getTripTicket()->{$action->getFormType().'_form_id'} !== null) {
            $type = $action->getFormType() === FormTypeEnum::MEDIC ? 'медицинским' : 'техническим';

            throw new \Exception("Путевой лист уже имеет связь с $type осмотром");
        }

        if ($this->formHasRelation($action)) {
            $type = $action->getFormType() === FormTypeEnum::MEDIC ? 'медицинский' : 'технический';

            throw new \Exception("Данный $type осмотр уже связан с другим путевым листом");
        }

        $updates = [$action->getFormType().'_form_id' => $action->getForm()->id];

        if ($action->getTripTicket()->driver_id === null && $action->getDriverId() !== null) {
            $updates['driver_id'] = $action->getDriverId();
        }

        if ($action->getTripTicket()->car_id === null && $action->getCarId() !== null) {
            $updates['car_id'] = $action->getCarId();
        }

        if ($action->getTripTicket()->start_date === null && $action->getStartDate()) {
            $updates['start_date'] = $action->getStartDate();
        }

        $action->getTripTicket()->update($updates);

        return $action->getTripTicket();
    }

    private function formHasRelation(SyncTripTicketWithFormAction $action): bool
    {
        $tripTicket = TripTicket::query()
            ->when($action->getFormType() === FormTypeEnum::MEDIC, function (Builder $query) use ($action) {
                $query->where('medic_form_id', '=', $action->getForm()->id);
            })
            ->when($action->getFormType() === FormTypeEnum::TECH, function (Builder $query) use ($action) {
                $query->where('tech_form_id', '=', $action->getForm()->id);
            })
            ->first();

        return $tripTicket !== null;
    }
}
