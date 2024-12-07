<?php

namespace App\Actions\TripTicket\StoreTripTicket;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use DateTimeImmutable;

final class StoreTripTicketAction
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
     * @var string|null
     */
    private $carId;

    /**
     * @var string
     */
    private $startDate;

    /**
     * @var string[]
     */
    private $additionalDates;

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
     * @param string $companyId
     * @param string|null $driverId
     * @param string|null $carId
     * @param string $startDate
     * @param string[] $additionalDates
     * @param int $validityPeriod
     * @param string|null $ticketNumber
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     * @param TripTicketTemplateEnum $templateCode
     */
    public function __construct(
        string $companyId,
        ?string $driverId,
        ?string $carId,
        string $startDate,
        array $additionalDates,
        int $validityPeriod,
        ?string $ticketNumber,
        LogisticsMethodEnum $logisticsMethod,
        TransportationTypeEnum $transportationType,
        TripTicketTemplateEnum $templateCode
    ) {
        $this->companyId = $companyId;
        $this->driverId = $driverId;
        $this->carId = $carId;
        $this->startDate = $startDate;
        $this->additionalDates = $additionalDates;
        $this->validityPeriod = $validityPeriod;
        $this->ticketNumber = $ticketNumber;
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

    public function getCarId(): ?string
    {
        return $this->carId;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getAdditionalDates(): array
    {
        return $this->additionalDates;
    }

    public function getValidityPeriod(): int
    {
        return $this->validityPeriod;
    }

    public function getTicketNumber(): ?string
    {
        return $this->ticketNumber;
    }

    public function setTicketNumber(?string $ticketNumber): void
    {
        $this->ticketNumber = $ticketNumber;
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
