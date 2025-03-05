<?php

namespace App\Actions\TripTicket\ChangeTripTicketStatus;

use App\Enums\TripTicket\TripTicketActionType;
use App\Enums\TripTicket\TripTicketStatus;
use App\Enums\TripTicket\TripTicketType;
use App\Events\TripTickets\ChangeTripTicketStatus;
use App\Events\TripTickets\LogTripTicket;
use Auth;

final class ChangeTripTicketStatusHandler
{
    public function handle(ChangeTripTicketStatusAction $action)
    {
        $tripTicketAction = $action->getStatus()->value() === TripTicketStatus::APPROVED
            ? TripTicketActionType::approval()
            : TripTicketActionType::cancelOfApproval();

        $tripTicketStatus = $action->getStatus()->value() === TripTicketStatus::APPROVED
            ? TripTicketStatus::approved()
            : $this->getStatus($action->getTripTicket()->type, $action->getTripTicket()->status);

        event(new ChangeTripTicketStatus($action->getTripTicket(), $tripTicketStatus));
        event(new LogTripTicket(Auth::user(), $action->getTripTicket(), $tripTicketAction));
    }

    private function getStatus(string $type, string $status): TripTicketStatus
    {
        switch (true) {
            case $type === TripTicketType::IN_ADVANCE && $status === TripTicketStatus::APPROVED:
                return TripTicketStatus::activated();
            default:
                return TripTicketStatus::created();
        }
    }
}
