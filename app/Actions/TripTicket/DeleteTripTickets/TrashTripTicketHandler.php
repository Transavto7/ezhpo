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

        if (! $action && $this->canRestore($tripTicket)) {
            $tripTicket->deleted_at = null;
        } else {
            $tripTicket->deleted_at = Carbon::now();
        }

        $tripTicket->save();
    }

    private function canRestore(TripTicket $tripTicket)
    {
        $medicForm = null;
        if ($tripTicket->medic_form_id) {
            $medicForm = TripTicket::query()
                ->where('medic_form_id', '=', $tripTicket->medic_form_id)
                ->first();
        }

        $techForm = null;
        if ($tripTicket->tech_form_id) {
            $techForm = TripTicket::query()
                ->where('tech_form_id', '=', $tripTicket->tech_form_id)
                ->first();
        }

        if ($medicForm || $techForm) {
            throw new \Exception('ПЛ невозможно восстановить из корзины т.к. связанные с ним осмотры уже используются в другом ПЛ');
        }

        return true;
    }
}
