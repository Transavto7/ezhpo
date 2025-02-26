<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\ChangeTripTicketStatus\ChangeTripTicketStatusAction;
use App\Actions\TripTicket\ChangeTripTicketStatus\ChangeTripTicketStatusHandler;
use App\Enums\TripTicketStatus;
use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Http\Request;

class ChangeTripTicketStatusController extends Controller
{
    public function __invoke(Request $request, ChangeTripTicketStatusHandler $handler)
    {
        $tripTicket = TripTicket::where('uuid', '=', $request->input('id'))->firstOrFail();

        $handler->handle(new ChangeTripTicketStatusAction(
            $tripTicket,
            TripTicketStatus::fromString($request->input('status'))
        ));

        return response()->noContent();
    }
}
