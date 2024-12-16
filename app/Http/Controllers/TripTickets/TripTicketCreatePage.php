<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;

final class TripTicketCreatePage extends Controller
{
    public function __invoke()
    {
        date_default_timezone_set('UTC');
        $time = time();
        $user = Auth::user();
        $timezone = $user->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d', $time);

        return view('trip-tickets.create', [
            'title' => 'Создание путевого листа',
            'default_current_date' => $time
        ]);
    }
}
