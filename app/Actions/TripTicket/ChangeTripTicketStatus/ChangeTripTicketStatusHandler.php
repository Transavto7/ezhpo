<?php

namespace App\Actions\TripTicket\ChangeTripTicketStatus;

use App\Enums\TripTicketActionType;
use App\Enums\TripTicketStatus;
use App\Events\TripTickets\ChangeTripTicketStatus;
use App\Events\TripTickets\LogTripTicket;
use App\Models\TripTicketLog;
use Auth;

final class ChangeTripTicketStatusHandler
{
    public function handle(ChangeTripTicketStatusAction $action)
    {
        if ($action->getStatus()->value() === TripTicketStatus::ACTIVATED) {
            $tripTicketAction = TripTicketActionType::activate();
            $tripTicketStatus = $action->getStatus();
        } else {
            $tripTicketAction = TripTicketActionType::deactivate();
            $tripTicketStatus = TripTicketLog::where('trip_ticket_id', $action->getTripTicket()->uuid)
                ->where('type', '=', TripTicketActionType::activate())
                ->orderByDesc('created_at')
                ->first()
                ->payload[0]['oldValue'];
            $tripTicketStatus = TripTicketStatus::fromString($tripTicketStatus);
        }

        event(new ChangeTripTicketStatus($action->getTripTicket(), $tripTicketStatus));
        event(new LogTripTicket(Auth::user(), $action->getTripTicket(), $tripTicketAction));

        $action->getTripTicket()->save();
    }
}
