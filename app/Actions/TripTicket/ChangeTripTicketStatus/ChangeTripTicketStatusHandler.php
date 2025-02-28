<?php

namespace App\Actions\TripTicket\ChangeTripTicketStatus;

use App\Enums\TripTicket\TripTicketActionType;
use App\Enums\TripTicket\TripTicketStatus;
use App\Events\TripTickets\ChangeTripTicketStatus;
use App\Events\TripTickets\LogTripTicket;
use Auth;

final class ChangeTripTicketStatusHandler
{
    public function handle(ChangeTripTicketStatusAction $action)
    {
        if ($action->getStatus()->value() === TripTicketStatus::APPROVED) {
            $tripTicketAction = TripTicketActionType::approval();
            $tripTicketStatus = $action->getStatus();
        } else {
            $tripTicketAction = TripTicketActionType::cancelOfApproval();
            $tripTicketStatus = TripTicketStatus::created();
        }

        event(new ChangeTripTicketStatus($action->getTripTicket(), $tripTicketStatus));
        event(new LogTripTicket(Auth::user(), $action->getTripTicket(), $tripTicketAction));
    }
}
