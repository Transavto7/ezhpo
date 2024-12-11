<?php

namespace App\Services\TripTicketExporter\ViewModels;

class Company
{
    /**
     * @var string
     */
    private $name;
    /**
     * @var string|null
     */
    private $whereCall;

    /**
     * @param string $name
     * @param string|null $whereCall
     */
    public function __construct(string $name, ?string $whereCall)
    {
        $this->name = $name;
        $this->whereCall = $whereCall;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWhereCall(): ?string
    {
        return $this->whereCall;
    }
}
