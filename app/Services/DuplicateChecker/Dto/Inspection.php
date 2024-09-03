<?php

namespace App\Services\DuplicateChecker\Dto;

use DateTimeImmutable;

class Inspection
{
    /**
     * @var string
     */
    protected $driverId;

    /**
     * @var DateTimeImmutable
     */
    protected $date;

    /**
     * @var string
     */
    protected $type;

    /**
     * @param string $driverId
     * @param DateTimeImmutable $date
     * @param string $type
     */
    public function __construct(string $driverId, DateTimeImmutable $date, string $type)
    {
        $this->driverId = $driverId;
        $this->date = $date;
        $this->type = $type;
    }

    public function getDriverId(): string
    {
        return $this->driverId;
    }

    public function getDate(): DateTimeImmutable
    {
        return $this->date;
    }

    public function getType(): string
    {
        return $this->type;
    }
}
