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
     * @var Carbon|null
     */
    private $periodPl;
    /**
     * @var string|null
     */
    private $username;
    /**
     * @var int|null
     */
    private $odometer;

    public function __construct(
        ?Carbon $date,
        ?Carbon $periodPl,
        ?string $username,
        ?int    $odometer = null
    ) {
        $this->date = $date;
        $this->periodPl = $periodPl;
        $this->username = $username;
        $this->odometer = $odometer;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getPeriodPl(): ?Carbon
    {
        return $this->periodPl;
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
