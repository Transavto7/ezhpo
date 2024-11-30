<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use DateTimeImmutable;

final class StoreTripTicketActionItem
{
    /**
     * @var DateTimeImmutable|null
     */
    private $startDate;

    /**
     * @var string|null
     */
    private $periodPl;

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
     * @param DateTimeImmutable|null $startDate
     * @param string|null $periodPl
     * @param int $validityPeriod
     * @param string|null $ticketNumber
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     * @param TripTicketTemplateEnum $templateCode
     */
    public function __construct(
        ?DateTimeImmutable $startDate,
        ?string $periodPl,
        int $validityPeriod,
        ?string $ticketNumber,
        LogisticsMethodEnum $logisticsMethod,
        TransportationTypeEnum $transportationType,
        TripTicketTemplateEnum $templateCode
    ) {
        $this->startDate = $startDate;
        $this->periodPl = $periodPl;
        $this->validityPeriod = $validityPeriod;
        $this->ticketNumber = $ticketNumber;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
        $this->templateCode = $templateCode;
    }

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getPeriodPl(): ?string
    {
        return $this->periodPl;
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
