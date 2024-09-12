<?php

namespace App\Services\FormHash;

use DateTimeImmutable;

class HashData
{
    /**
     * @var string
     */
    private $driverId;

    /**
     * @var DateTimeImmutable
     */
    private $date;

    /**
     * @var string
     */
    private $type;

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

    public function toHashString() : string
    {
        return $this->getType()
            .$this->getDate()->format('Y-m-d')
            .$this->getDriverId();
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
