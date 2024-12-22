<?php

namespace App\Services\TripTicketExporter\ViewModels;

use App\Services\TripTicketExporter\ValueObjects\PeriodPl;
use Carbon\Carbon;

class FormViewModel
{
    /**
     * @var Carbon|null
     */
    private $date;
    /**
     * @var PeriodPl|null
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

    /**
     * @param Carbon|null $date
     * @param PeriodPl|null $periodPl
     * @param string|null $username
     * @param int|null $odometer
     */
    public function __construct(
        ?Carbon   $date,
        ?PeriodPl $periodPl,
        ?string   $username,
        ?int      $odometer = null
    )
    {
        $this->date = $date;
        $this->periodPl = $periodPl;
        $this->username = $username;
        $this->odometer = $odometer;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getPeriodPl(): ?PeriodPl
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
