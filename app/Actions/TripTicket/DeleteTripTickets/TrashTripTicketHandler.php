<?php

namespace App\Actions\TripTicket\DeleteTripTickets;

use App\Models\TripTicket;
use App\User;
use Illuminate\Support\Carbon;

final class TrashTripTicketHandler
{
    public function handle(TripTicket $tripTicket, $action, User $user)
    {
        $tripTicket->deleted_id = $user->id;

        if (!$action) {
            $tripTicket->deleted_at = null;
        } else {
            $tripTicket->deleted_at = Carbon::now();
        }

        $tripTicket->save();
    }
}
