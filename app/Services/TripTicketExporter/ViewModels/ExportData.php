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
     * @var TripTicketViewModel
     */
    private $tripTicket;
    /**
     * @var CompanyViewModel|null
     */
    private $company;
    /**
     * @var DriverViewModel|null
     */
    private $driver;
    /**
     * @var CarViewModel|null
     */
    private $car;
    /**
     * @var MedicFormViewModel|null
     */
    private $medicForm;
    /**
     * @var TechFormViewModel|null
     */
    private $techForm;

    /**
     * @param TripTicketTemplateEnum $templateCode
     * @param TripTicketViewModel $tripTicket
     * @param CompanyViewModel|null $company
     * @param DriverViewModel|null $driver
     * @param CarViewModel|null $car
     * @param MedicFormViewModel|null $medicForm
     * @param TechFormViewModel|null $techForm
     */
    public function __construct(
        TripTicketTemplateEnum $templateCode,
        TripTicketViewModel    $tripTicket,
        ?CompanyViewModel      $company,
        ?DriverViewModel       $driver,
        ?CarViewModel          $car,
        ?MedicFormViewModel    $medicForm,
        ?TechFormViewModel $techForm
    )
    {
        $this->templateCode = $templateCode;
        $this->tripTicket = $tripTicket;
        $this->company = $company;
        $this->driver = $driver;
        $this->car = $car;
        $this->medicForm = $medicForm;
        $this->techForm = $techForm;
    }

    public function getTemplateCode(): TripTicketTemplateEnum
    {
        return $this->templateCode;
    }

    public function getTripTicket(): TripTicketViewModel
    {
        return $this->tripTicket;
    }

    public function getCompany(): ?CompanyViewModel
    {
        return $this->company;
    }

    public function getDriver(): ?DriverViewModel
    {
        return $this->driver;
    }

    public function getCar(): ?CarViewModel
    {
        return $this->car;
    }

    public function getMedicForm(): ?MedicFormViewModel
    {
        return $this->medicForm;
    }

    public function getTechForm(): ?TechFormViewModel
    {
        return $this->techForm;
    }
}
