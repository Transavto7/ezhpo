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
     * @var string|null
     */
    private $medicFormUserName;
    /**
     * @var string|null
     */
    private $techFormUserName;

    /**
     * @param TripTicketTemplateEnum $templateCode
     * @param TripTicket $tripTicket
     * @param Company|null $company
     * @param Driver|null $driver
     * @param Car|null $car
     * @param string|null $medicFormUserName
     * @param string|null $techFormUserName
     */
    public function __construct(
        TripTicketTemplateEnum $templateCode,
        TripTicket             $tripTicket,
        ?Company               $company,
        ?Driver                $driver,
        ?Car                   $car,
        ?string                $medicFormUserName,
        ?string                $techFormUserName
    )
    {
        $this->templateCode = $templateCode;
        $this->tripTicket = $tripTicket;
        $this->company = $company;
        $this->driver = $driver;
        $this->car = $car;
        $this->medicFormUserName = $medicFormUserName;
        $this->techFormUserName = $techFormUserName;
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

    public function getMedicFormUserName(): ?string
    {
        return $this->medicFormUserName;
    }

    public function getTechFormUserName(): ?string
    {
        return $this->techFormUserName;
    }
}
