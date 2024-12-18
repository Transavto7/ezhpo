<?php

namespace App\Http\Controllers\TripTickets;

use App\Http\Controllers\Controller;
use App\Models\TripTicket;
use Illuminate\Support\Facades\Auth;

class TripTicketCreateMedicFormPageController extends Controller
{
    public function __invoke(string $id)
    {
        $tripTicket = TripTicket::where('uuid', '=', $id)->first();

        date_default_timezone_set('UTC');
        $time = time();
        $user = Auth::user();
        $timezone = $user->timezone ?: 3;
        $time += $timezone * 3600;
        $time = date('Y-m-d', $time);

        if (session()->exists('anketa_pv_id') && ((date('d.m') > session('anketa_pv_id')['expired']))) {
            session()->remove('anketa_pv_id');
        }

        return view('trip-tickets.create-medic-form', [
            'tripTicket' => $tripTicket,
            'title' => 'Добавление медосмотра по данным путевого листа',
            'default_current_date' => $time,
            'default_pv_id' => $user->pv_id,
        ]);
    }
}
