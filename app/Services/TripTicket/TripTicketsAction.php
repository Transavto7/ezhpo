<?php

namespace App\Services\TripTicket;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use Carbon\Carbon;

class TripTicketsAction
{
    /**
     * @var string
     */
    private $companyId;

    /**
     * @var string|null
     */
    private $driverId;

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
     * @param string $companyId
     * @param string|null $driverId
     * @param Carbon $dateFrom
     * @param Carbon $dateTo
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     * @param TripTicketTemplateEnum $templateCode
     */
    public function __construct(
        string $companyId,
        ?string $driverId,
        Carbon $dateFrom,
        Carbon $dateTo,
        LogisticsMethodEnum $logisticsMethod,
        TransportationTypeEnum $transportationType,
        TripTicketTemplateEnum $templateCode
    ) {
        $this->companyId = $companyId;
        $this->driverId = $driverId;
        $this->dateFrom = $dateFrom;
        $this->dateTo = $dateTo;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
        $this->templateCode = $templateCode;
    }

    public function getCompanyId(): string
    {
        return $this->companyId;
    }

    public function getDriverId(): ?string
    {
        return $this->driverId;
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
}
