<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\ChangeTripTicketStatus\ChangeTripTicketStatusAction;
use App\Actions\TripTicket\ChangeTripTicketStatus\ChangeTripTicketStatusHandler;
use App\Enums\TripTicket\TripTicketStatus;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use App\Services\TripTicket\TripTicketPermissions;
use Illuminate\Http\Request;

class MassApproveTripTicketController extends Controller
{
    public function __invoke(Request $request, ChangeTripTicketStatusHandler $handler)
    {
        $ids = $request->input('ids') ?? [];
        $tripTickets = TripTicket::whereIn('uuid', $ids)->get();
        $notApproved = [];

        foreach ($tripTickets as $tripTicket) {
            if (! TripTicketPermissions::canApprove($tripTicket->type, $tripTicket->status)) {
                $notApproved[] = $tripTicket->ticket_number;
                continue;
            }

            $handler->handle(new ChangeTripTicketStatusAction(
                $tripTicket,
                TripTicketStatus::approved()
            ));
        }

        if (count($notApproved)) {
            session()->flash('not_approved_items', $notApproved);
        }

        return response()->noContent();
    }
}
