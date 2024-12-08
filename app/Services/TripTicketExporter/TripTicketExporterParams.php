<?php

namespace App\Services\TripTicketExporter;


use App\Services\TripTicketExporter\ValueObjects\Car;
use App\Services\TripTicketExporter\ValueObjects\Company;
use App\Services\TripTicketExporter\ValueObjects\Driver;
use App\Services\TripTicketExporter\ValueObjects\TripTicket;

class TripTicketExporterParams
{
    /**
     * @var TripTicket
     */
    private $tripTicket;
    /**
     * @var Company|null
     */
    private $company;
    /**
     * @var string|null
     */
    private $driverId;
    /**
     * @var Driver|null
     */
    private $driver;
    /**
     * @var string|null
     */
    private $carId;
    /**
     * @var Car|null
     */
    private $car;

    /**
     * @param TripTicket $tripTicket
     * @param Company|null $company
     * @param string|null $driverId
     * @param Driver|null $driver
     * @param string|null $carId
     * @param Car|null $car
     */
    public function __construct(
        TripTicket $tripTicket,
        ?Company   $company,
        ?string    $driverId,
        ?Driver    $driver,
        ?string    $carId,
        ?Car       $car
    )
    {
        $this->tripTicket = $tripTicket;
        $this->company = $company;
        $this->driverId = $driverId;
        $this->driver = $driver;
        $this->carId = $carId;
        $this->car = $car;
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function getDriverId(): ?string
    {
        return $this->driverId;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function getCarId(): ?string
    {
        return $this->carId;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }
}
