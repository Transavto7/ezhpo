<?php

namespace App\Actions\TripTicket\UpdateTripTicket;


use App\Models\TripTicket;

final class UpdateTripTicketHandler
{
    public function handle(UpdateTripTicketAction $action): TripTicket
    {
        $action->getTripTicket()->update([
            'start_date' => $action->getStartDate(),
            'validity_period' => $action->getValidityPeriod(),
            'driver_id' => $action->getTripTicket()->driver_id ?: $action->getDriverId(),
            'car_id' => $action->getTripTicket()->car_id ?: $action->getCarId(),
            'logistics_method' => $action->getLogisticsMethod(),
            'transportation_type' => $action->getTransportationType(),
            'template_code' => $action->getTemplateCode(),
        ]);

        return $action->getTripTicket();
    }
}
