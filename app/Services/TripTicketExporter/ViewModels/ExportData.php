<?php

namespace App\Services\TripTicketExporter\ViewModels;

use App\Enums\TripTicketTemplateEnum;

class ExportData
{
    /**
     * @var TripTicketTemplateEnum
     */
    private $templateCode;
    /**
     * @var TripTicket
     */
    private $tripTicket;
    /**
     * @var Company|null
     */
    private $company;
    /**
     * @var Driver|null
     */
    private $driver;
    /**
     * @var Car|null
     */
    private $car;

    /**
     * @param TripTicketTemplateEnum $templateCode
     * @param TripTicket $tripTicket
     * @param Company|null $company
     * @param Driver|null $driver
     * @param Car|null $car
     */
    public function __construct(
        TripTicketTemplateEnum $templateCode,
        TripTicket             $tripTicket,
        ?Company               $company,
        ?Driver                $driver,
        ?Car                   $car
    )
    {
        $this->templateCode = $templateCode;
        $this->tripTicket = $tripTicket;
        $this->company = $company;
        $this->driver = $driver;
        $this->car = $car;
    }

    public function getTemplateCode(): TripTicketTemplateEnum
    {
        return $this->templateCode;
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getCompany(): ?Company
    {
        return $this->company;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function getCar(): ?Car
    {
        return $this->car;
    }


}
