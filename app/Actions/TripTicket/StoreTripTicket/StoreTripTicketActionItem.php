<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;

final class StoreTripTicketActionItem
{
    /**
     * @var string|null
     */
    private $startDate;

    /**
     * @var integer
     */
    private $validityPeriod;

    /**
     * @var string|null
     */
    private $ticketNumber;

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
     * @param string|null $startDate
     * @param int $validityPeriod
     * @param string|null $ticketNumber
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     * @param TripTicketTemplateEnum $templateCode
     */
    public function __construct(
        ?string $startDate,
        int $validityPeriod,
        ?string $ticketNumber,
        LogisticsMethodEnum $logisticsMethod,
        TransportationTypeEnum $transportationType,
        TripTicketTemplateEnum $templateCode
    ) {
        $this->startDate = $startDate;
        $this->validityPeriod = $validityPeriod;
        $this->ticketNumber = $ticketNumber;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
        $this->templateCode = $templateCode;
    }

    public function getStartDate(): ?string
    {
        return $this->startDate;
    }

    public function getValidityPeriod(): int
    {
        return $this->validityPeriod;
    }

    public function getTicketNumber(): ?string
    {
        return $this->ticketNumber;
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
