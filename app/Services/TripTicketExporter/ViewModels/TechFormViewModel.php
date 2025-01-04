<?php

namespace App\Services\TripTicketExporter\ViewModels;

use Carbon\Carbon;

final class TechFormViewModel
{
    /**
     * @var Carbon|null
     */
    private $date;
    /**
     * @var string|null
     */
    private $username;
    /**
     * @var int|null
     */
    private $odometer;

    /**
     * @param Carbon|null $date
     * @param string|null $username
     * @param int|null $odometer
     */
    public function __construct(
        ?Carbon   $date,
        ?string   $username,
        ?int      $odometer = null
    ) {
        $this->date = $date;
        $this->username = $username;
        $this->odometer = $odometer;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getOdometer(): ?int
    {
        return $this->odometer;
    }
}
