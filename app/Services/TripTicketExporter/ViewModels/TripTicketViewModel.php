<?php

namespace App\Services\TripTicketExporter\ViewModels;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use Illuminate\Support\Carbon;

final class TripTicketViewModel
{
    /**
     * @var string|null
     */
    private $ticketNumber;
    /**
     * @var Carbon|null
     */
    private $startDate;
    /**
     * @var Carbon|null
     */
    private $periodPl;
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
     * @param Carbon|null $startDate
     * @param Carbon|null $periodPl
     * @param int $validityPeriod
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     */
    public function __construct(
        ?string                $ticketNumber,
        ?Carbon                $startDate,
        ?Carbon                $periodPl,
        int                    $validityPeriod,
        LogisticsMethodEnum    $logisticsMethod,
        TransportationTypeEnum $transportationType
    )
    {
        $this->ticketNumber = $ticketNumber;
        $this->startDate = $startDate;
        $this->periodPl = $periodPl;
        $this->validityPeriod = $validityPeriod;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
    }

    public function getTicketNumber(): ?string
    {
        return $this->ticketNumber;
    }

    public function getStartDate(): ?Carbon
    {
        return $this->startDate;
    }

    public function getPeriodPl(): ?Carbon
    {
        return $this->periodPl;
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
