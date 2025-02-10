<?php

namespace App\Actions\TripTicket\UpdateTripTicketForms;

use App\Enums\FormLogActionTypesEnum;
use App\Enums\TripTicketActionType;
use App\Events\Forms\FormAction;
use App\Events\TripTickets\TripTicketAction;
use App\Models\Forms\Form;
use App\Models\TripTicket;
use Auth;
use Carbon\Carbon;

final class UpdateTripTicketFormsHandler
{
    public function handle(UpdateTripTicketFormsAction $action): TripTicket
    {
        $tripTicket = $action->getTripTicket();
        $oldMedicForm = $tripTicket->medic_form_id;
        $oldTechForm = $tripTicket->tech_form_id;
        $startDate = $tripTicket->start_date;
        $driver = $tripTicket->driver_id;
        $car = $tripTicket->car_id;

        if ($action->getMedicForm() && $action->getMedicForm()->date && $startDate === null) {
            $tripTicket->start_date = $action->getMedicForm()->date;
            $tripTicket->period_pl = Carbon::parse($action->getMedicForm()->date)->format('Y-m');
        }

        if ($action->getMedicForm() && $action->getMedicForm()->driver_id && $driver === null) {
            $tripTicket->driver_id = $action->getMedicForm()->driver_id;
        }

        $tripTicket->fill([
            'medic_form_id' => $action->getMedicForm()
                ? $action->getMedicForm()->id
                : null,
        ]);

        if ($oldMedicForm && $action->getMedicForm() === null) {
            event(new TripTicketAction(Auth::user(), $tripTicket, TripTicketActionType::detachMedicForm()));
        }

        if ($action->getMedicForm() !== null && $action->getMedicForm() !== $oldMedicForm) {
            event(new TripTicketAction(Auth::user(), $tripTicket, TripTicketActionType::attachMedicForm()));
        }

        $tripTicket->save();

        if ($action->getTechForm() && $action->getTechForm()->date && $startDate === null) {
            $tripTicket->start_date = $action->getTechForm()->date;
            $tripTicket->period_pl = Carbon::parse($action->getTechForm()->date)->format('Y-m');
        }

        if ($action->getTechForm() && $action->getTechForm()->driver_id && $driver === null) {
            $tripTicket->driver_id = $action->getTechForm()->driver_id;
        }

        if ($action->getTechForm() && $action->getTechForm()->details->car_id && $car === null) {
            $tripTicket->car_id = $action->getTechForm()->details->car_id;
        }

        $tripTicket->fill([
            'tech_form_id' => $action->getTechForm()
                ? $action->getTechForm()->id
                : null,
        ]);

        if ($oldTechForm && $action->getTechForm() === null) {
            event(new TripTicketAction(Auth::user(), $tripTicket, TripTicketActionType::detachTechForm()));
        }

        if ($action->getTechForm() !== null && $action->getTechForm() !== $oldTechForm) {
            event(new TripTicketAction(Auth::user(), $tripTicket, TripTicketActionType::attachTechForm()));
        }

        $tripTicket->save();

        return $tripTicket;
    }
}
