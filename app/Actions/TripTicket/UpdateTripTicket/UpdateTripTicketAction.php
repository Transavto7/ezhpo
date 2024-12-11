<?php

namespace App\Actions\TripTicket\UpdateTripTicket;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\Models\TripTicket;

final class UpdateTripTicketAction
{
    /**
     * @var TripTicket
     */
    private $tripTicket;

    /**
     * @var string|null
     */
    private $driverId;

    /**
     * @var string|null
     */
    private $carId;

    /**
     * @var string|null
     */
    private $startDate;

    /**
     * @var integer
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
     * @var TripTicketTemplateEnum
     */
    private $templateCode;

    /**
     * @param TripTicket $tripTicket
     * @param string|null $driverId
     * @param string|null $carId
     * @param string|null $startDate
     * @param int $validityPeriod
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     * @param TripTicketTemplateEnum $templateCode
     */
    public function __construct(
        TripTicket $tripTicket,
        ?string $driverId,
        ?string $carId,
        ?string $startDate,
        int $validityPeriod,
        LogisticsMethodEnum $logisticsMethod,
        TransportationTypeEnum $transportationType,
        TripTicketTemplateEnum $templateCode
    ) {
        $this->tripTicket = $tripTicket;
        $this->driverId = $driverId;
        $this->carId = $carId;
        $this->startDate = $startDate;
        $this->validityPeriod = $validityPeriod;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
        $this->templateCode = $templateCode;
    }

    public function getTripTicket(): TripTicket
    {
        return $this->tripTicket;
    }

    public function getDriverId(): ?string
    {
        return $this->driverId;
    }

    public function getCarId(): ?string
    {
        return $this->carId;
    }

    public function getStartDate(): ?string
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

    public function getTemplateCode(): TripTicketTemplateEnum
    {
        return $this->templateCode;
    }
}
