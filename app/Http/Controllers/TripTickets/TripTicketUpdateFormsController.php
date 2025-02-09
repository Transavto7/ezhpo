<?php

namespace App\Http\Controllers\TripTickets;

use App\Actions\TripTicket\UpdateTripTicketForms\UpdateTripTicketFormsAction;
use App\Actions\TripTicket\UpdateTripTicketForms\UpdateTripTicketFormsHandler;
use App\Http\Controllers\Controller;
use App\Models\Forms\Form;
use App\Models\TripTicket;
use Illuminate\Http\Request;

class TripTicketUpdateFormsController extends Controller
{
    public function __invoke(Request $request, UpdateTripTicketFormsHandler $handler)
    {
        $tripTicket = TripTicket::where('uuid', '=', $request->input('id'))->first();
        $medic = Form::find($request->input('medic'));
        $tech = Form::find($request->input('tech'));

        $handler->handle(new UpdateTripTicketFormsAction(
            $tripTicket,
            $medic,
            $tech
        ));

        return response()->json('Связанные с путевым листом осмотры успешно обновлены');
    }
}
