<?php

namespace App\Services\TripTicketExporter\ViewModels;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use Illuminate\Support\Carbon;

class TripTicket
{
    /**
     * @var string|null
     */
    private $ticketNumber;
    /**
     * @var Carbon
     */
    private $startDate;
    /**
     * @var int
     */
    private $validityPeriod;
    /**
     * @var LogisticsMethodEnum
     */
    private $logisticsMethod;
    /**
     * @var TransportationTypeEnum
     */
    private $transportationType;

    /**
     * @param string|null $ticketNumber
     * @param Carbon $startDate
     * @param int $validityPeriod
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     */
    public function __construct(
        ?string                $ticketNumber,
        Carbon                 $startDate,
        int                    $validityPeriod,
        LogisticsMethodEnum    $logisticsMethod,
        TransportationTypeEnum $transportationType
    )
    {
        $this->ticketNumber = $ticketNumber;
        $this->startDate = $startDate;
        $this->validityPeriod = $validityPeriod;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
    }

    public function getTicketNumber(): ?string
    {
        return $this->ticketNumber;
    }

    public function getStartDate(): Carbon
    {
        return $this->startDate;
    }

    public function getValidityPeriod(): int
    {
        return $this->validityPeriod;
    }

    public function getLogisticsMethod(): LogisticsMethodEnum
    {
        return $this->logisticsMethod;
    }

    public function getTransportationType(): TransportationTypeEnum
    {
        return $this->transportationType;
    }
}
