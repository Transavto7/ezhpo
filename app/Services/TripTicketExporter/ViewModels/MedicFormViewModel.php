<?php

namespace App\Services\TripTicketExporter\ViewModels;

use Carbon\Carbon;

class MedicFormViewModel
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
     * @param Carbon|null $date
     * @param string|null $username
     */
    public function __construct(
        ?Carbon   $date,
        ?string   $username
    ) {
        $this->date = $date;
        $this->username = $username;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }
}
