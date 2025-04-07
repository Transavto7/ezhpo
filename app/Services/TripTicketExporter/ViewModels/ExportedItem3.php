<?php

namespace App\Services\TripTicketExporter\ViewModels;

final class ExportedItem3 extends ExportedItem
{
    /**
     * @var ExportedItem
     */
    private $leftTripTicket;
    /**
     * @var ExportedItem|null
     */
    private $rightTripTicket;

    public function __construct(ExportedItem $leftTripTicket, ?ExportedItem $rightTripTicket = null)
    {
        $this->leftTripTicket = $leftTripTicket;
        $this->rightTripTicket = $rightTripTicket;
    }

    public function getLeftTripTicket(): ExportedItem
    {
        return $this->leftTripTicket;
    }

    public function getRightTripTicket(): ?ExportedItem
    {
        return $this->rightTripTicket;
    }
}
