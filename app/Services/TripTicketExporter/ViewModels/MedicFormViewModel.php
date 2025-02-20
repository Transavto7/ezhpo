<?php

namespace App\Services\TripTicketExporter\ViewModels;

use Carbon\Carbon;

final class MedicFormViewModel
{
    /**
     * @var string
     */
    private $uuid;
    /**
     * @var Carbon|null
     */
    private $date;
    /**
     * @var string|null
     */
    private $username;
    /**
     * @var string|null
     */
    private $stamp;

    public function __construct(
        string    $uuid,
        ?Carbon   $date,
        ?string   $username,
        ?StampViewModel   $stamp
    ) {
        $this->uuid = $uuid;
        $this->date = $date;
        $this->username = $username;
        $this->stamp = $stamp ?? StampViewModel::default();
    }

    public function getUuid(): string
    {
        return $this->uuid;
    }

    public function getDate(): ?Carbon
    {
        return $this->date;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function getStamp(): StampViewModel
    {
        return $this->stamp;
    }
}
