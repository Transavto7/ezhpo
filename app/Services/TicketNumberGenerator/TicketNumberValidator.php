<?php

namespace App\Services\TicketNumberGenerator;

use App\Models\TripTicket;
use Carbon\Carbon;

final class TicketNumberValidator
{
    /**
     * @param string $companyId
     * @return callable
     */
    public static function validate(string $companyId): callable
    {
        return function (int $number) use ($companyId) {
            $ticketNumber = TripTicket::withTrashed()
                ->where('ticket_number', '=', $number)
                ->where('company_id', '=', $companyId)
                ->where('created_at', '>=', Carbon::now()->subYear())
                ->first();

            if ($ticketNumber) {
                return false;
            }

            return true;
        };
    }
}
