<?php

namespace App\Actions\TripTicket\TripTicketsQuery;

final class TripTicketsQueryAction
{
    /**
     * @var bool
     */
    private $trash;

    /**
     * @var string
     */
    private $orderKey;

    /**
     * @var string
     */
    private $orderBy;

    /**
     * @var bool
     */
    private $filterActivated;

    /**
     * @var array
     */
    private $filterParams;

    /**
     * @param bool $trash
     * @param string $orderKey
     * @param string $orderBy
     * @param bool $filterActivated
     * @param array $filterParams
     */
    public function __construct(bool $trash, string $orderKey, string $orderBy, bool $filterActivated, array $filterParams)
    {
        $this->trash = $trash;
        $this->orderKey = $orderKey;
        $this->orderBy = $orderBy;
        $this->filterActivated = $filterActivated;
        $this->filterParams = $filterParams;
    }

    public function isTrash(): bool
    {
        return $this->trash;
    }

    public function getOrderKey(): string
    {
        return $this->orderKey;
    }

    public function getOrderBy(): string
    {
        return $this->orderBy;
    }

    public function isFilterActivated(): bool
    {
        return $this->filterActivated;
    }

    public function getFilterParams(): array
    {
        return $this->filterParams;
    }
}
