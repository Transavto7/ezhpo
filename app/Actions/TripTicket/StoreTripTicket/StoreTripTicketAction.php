<?php

namespace App\Actions\TripTicket\StoreTripTicket;

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
     * @var StoreTripTicketActionItem[]
     */
    private $items;

    /**
     * @param string $companyId
     * @param string|null $driverId
     * @param string|null $carId
     * @param StoreTripTicketActionItem[] $items
     */
    public function __construct(string $companyId, ?string $driverId, ?string $carId, array $items)
    {
        $this->companyId = $companyId;
        $this->driverId = $driverId;
        $this->carId = $carId;
        $this->items = $items;
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

    public function getItems(): array
    {
        return $this->items;
    }
}
