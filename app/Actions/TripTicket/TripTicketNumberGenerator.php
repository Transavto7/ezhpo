<?php

namespace App\Actions\TripTicket;

use Exception;

abstract class TripTicketNumberGenerator
{
    /**
     * @throws Exception
     */
    public function getTicketNumber(string $id): string
    {
        return strtoupper(substr($id, 0, 8));
    }
}
