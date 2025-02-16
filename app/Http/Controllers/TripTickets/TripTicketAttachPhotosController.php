<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;

class TripTicketAttachPhotosController extends Controller
{
    public function __invoke()
    {
        return view('trip-tickets.attach-photos');
    }
}
