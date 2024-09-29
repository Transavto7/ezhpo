<?php

namespace App\Services\FormHash;

use DateTimeImmutable;

class TechHashData implements HashData
{
    /**
     * @var string
     */
    private $driverId;

    /**
     * @var string
     */
    private $carId;

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
     * @param string $carId
     * @param DateTimeImmutable $date
     * @param string $type
     */
    public function __construct(string $driverId, string $carId, DateTimeImmutable $date, string $type)
    {
        $this->driverId = $driverId;
        $this->carId = $carId;
        $this->date = $date;
        $this->type = $type;
    }

    public function toHashString() : string
    {
        return $this->getType()
            .$this->getDate()->format('Y-m-d')
            .$this->getDriverId()
            .$this->getCarId();
    }

    public function getDriverId(): string
    {
        return $this->driverId;
    }

    public function getCarId(): string
    {
        return $this->carId;
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
