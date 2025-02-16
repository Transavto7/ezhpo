<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;

class TripTicketAttachPhotosPageController extends Controller
{
    public function __invoke(string $tripTicketId)
    {
        return view('trip-tickets.attach-photos', ['id' => $tripTicketId]);
    }
}
