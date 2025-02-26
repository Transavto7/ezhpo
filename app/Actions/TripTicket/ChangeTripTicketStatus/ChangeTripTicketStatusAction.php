<?php

namespace App\Actions\TripTicket\ChangeTripTicketStatus;

use App\Enums\TripTicketStatus;
use App\Models\TripTicket;

final class ChangeTripTicketStatusAction
{
    /**
     * @var TripTicket
     */
    private $tripTicket;

    /**
     * @var TripTicketStatus
     */
    private $status;

    /**
     * @param TripTicket $tripTicket
     * @param TripTicketStatus $status
     */
    public function __construct(TripTicket $tripTicket, TripTicketStatus $status)
    {
        $this->tripTicket = $tripTicket;
        $this->status = $status;
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getStatus(): TripTicketStatus
    {
        return $this->status;
    }
}
