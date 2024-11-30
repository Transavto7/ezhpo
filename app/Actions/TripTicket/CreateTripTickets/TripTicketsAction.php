<?php

namespace App\Actions\TripTicket\CreateTripTickets;

use App\Company;
use App\Driver;
use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use Carbon\Carbon;

final class TripTicketsAction
{
    /**
     * @var Company
     */
    private $company;

    /**
     * @var Driver|null
     */
    private $driver;

    /**
     * @var Carbon
     */
    private $dateFrom;

    /**
     * @var Carbon
     */
    private $dateTo;

    /**
     * @var LogisticsMethodEnum
     */
    private $logisticsMethod;

    /**
     * @var TransportationTypeEnum
     */
    private $transportationType;

    /**
     * @var TripTicketTemplateEnum
     */
    private $templateCode;

    /**
     * @var integer
     */
    private $validityPeriod;

    /**
     * @param Company $company
     * @param Driver|null $driver
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     * @param TripTicketTemplateEnum $templateCode
     * @param int $validityPeriod
     */
    public function __construct(
        Company $company,
        ?Driver $driver,
        Carbon $dateFrom,
        Carbon $dateTo,
        LogisticsMethodEnum $logisticsMethod,
        TransportationTypeEnum $transportationType,
        TripTicketTemplateEnum $templateCode,
        int $validityPeriod
    )
    {
        $this->company = $company;
        $this->driver = $driver;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
        $this->templateCode = $templateCode;
        $this->validityPeriod = $validityPeriod;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getDriver(): ?Driver
    {
        return $this->driver;
    }

    public function getDateFrom(): Carbon
    {
        return $this->dateFrom;
    }

    public function getDateTo(): Carbon
    {
        return $this->dateTo;
    }

    public function getLogisticsMethod(): LogisticsMethodEnum
    {
        return $this->logisticsMethod;
    }

    public function getTransportationType(): TransportationTypeEnum
    {
        return $this->transportationType;
    }

    public function getTemplateCode(): TripTicketTemplateEnum
    {
        return $this->templateCode;
    }

    public function getValidityPeriod(): int
    {
        return $this->validityPeriod;
    }
}
