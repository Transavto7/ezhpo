<?php

namespace App\Actions\TripTicket\UpdateTripTicketForms;

use App\Models\TripTicket;
use Carbon\Carbon;

final class UpdateTripTicketFormsHandler
{
    public function handle(UpdateTripTicketFormsAction $action): TripTicket
    {
        $tripTicket = $action->getTripTicket();
        $startDate = $tripTicket->start_date;
        $driver = $tripTicket->driver_id;
        $car = $tripTicket->car_id;

        if ($action->getMedicFormId() && $action->getMedicFormId()->date && $startDate === null) {
            $tripTicket->start_date = $action->getMedicFormId()->date;
            $tripTicket->period_pl = Carbon::parse($action->getMedicFormId()->date)->format('Y-m');
        }

        if ($action->getMedicFormId() && $action->getMedicFormId()->driver_id && $driver === null) {
            $tripTicket->driver_id = $action->getMedicFormId()->driver_id;
        }

        if ($action->getTechFormId() && $action->getTechFormId()->date && $startDate === null) {
            $tripTicket->start_date = $action->getTechFormId()->date;
        }

        if ($action->getTechFormId() && $action->getTechFormId()->driver_id && $driver === null) {
            $tripTicket->driver_id = $action->getTechFormId()->driver_id;
        }

        if ($action->getTechFormId() && $action->getTechFormId()->details->car_id && $car === null) {
            $tripTicket->car_id = $action->getTechFormId()->details->car_id;
        }

        $tripTicket->fill([
            'medic_form_id' => $action->getMedicFormId()
                ? $action->getMedicFormId()->id
                : null,
            'tech_form_id' => $action->getTechFormId()
                ? $action->getTechFormId()->id
                : null,
        ]);

        $tripTicket->save();

        return $tripTicket;
    }
}
