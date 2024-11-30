<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use App\Models\TripTicket;

class TripTicketEditPageController extends Controller
{
    public function __invoke(string $uuid)
    {
        $tripTicket = TripTicket::where('uuid', '=', $uuid)->first();

        return view('trip-tickets.edit', [
            'title' => 'Редактирование путевого листа',
            'tripTicket' => $tripTicket,
        ]);
    }
}
