<?php

namespace App\Services\TripTicketExporter\ValueObjects;

use App\Enums\LogisticsMethodEnum;
use App\Enums\TransportationTypeEnum;
use App\Enums\TripTicketTemplateEnum;
use App\Models\TripTicket as TripTicketModel;
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
     * @var TripTicketTemplateEnum
     */
    private $templateCode;

    /**
     * @param string|null $ticketNumber
     * @param Carbon $startDate
     * @param int $validityPeriod
     * @param LogisticsMethodEnum $logisticsMethod
     * @param TransportationTypeEnum $transportationType
     * @param TripTicketTemplateEnum $templateCode
     */
    private function __construct(
        ?string                $ticketNumber,
        Carbon                 $startDate,
        int                    $validityPeriod,
        LogisticsMethodEnum    $logisticsMethod,
        TransportationTypeEnum $transportationType,
        TripTicketTemplateEnum $templateCode
    )
    {
        $this->ticketNumber = $ticketNumber;
        $this->startDate = $startDate;
        $this->validityPeriod = $validityPeriod;
        $this->logisticsMethod = $logisticsMethod;
        $this->transportationType = $transportationType;
        $this->templateCode = $templateCode;
    }

    public static function fromEloquent(TripTicketModel $tripTicket): self
    {
        return new self(
            $tripTicket->ticket_number,
            Carbon::parse($tripTicket->start_date),
            $tripTicket->validity_period,
            LogisticsMethodEnum::fromString($tripTicket->logistics_method),
            TransportationTypeEnum::fromString($tripTicket->transportation_type),
            TripTicketTemplateEnum::fromString($tripTicket->template_code)
        );
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

    public function getTemplateCode(): TripTicketTemplateEnum
    {
        return $this->templateCode;
    }


}
