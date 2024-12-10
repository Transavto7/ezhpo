<?php

namespace App\Actions\TripTicket;

use App\GenerateHashIdTrait;
use App\Models\TripTicket;
use Exception;

abstract class TripTicketNumberGenerator
{
    use GenerateHashIdTrait;

    /**
     * @throws Exception
     */
    public function nextTicketNumber(): int
    {
        $validator = function (int $number) {
            if (TripTicket::withTrashed()->where('ticket_number', $number)->first()) {
                return false;
            }

            return true;
        };

        return $this->generateHashId(
            $validator,
            config('app.hash_generator.trip_ticket.min'),
            config('app.hash_generator.trip_ticket.max'),
            config('app.hash_generator.trip_ticket.tries')
        );
    }
}
