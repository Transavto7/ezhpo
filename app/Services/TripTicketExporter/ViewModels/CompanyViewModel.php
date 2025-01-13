<?php

namespace App\Services\TripTicketExporter\ViewModels;

final class CompanyViewModel
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
     * @var string|null
     */
    private $reqName;

    /**
     * @param string $name
     * @param string|null $whereCall
     * @param string|null $reqName
     */
    public function __construct(string $name, ?string $whereCall, ?string $reqName)
    {
        $this->name = $name;
        $this->whereCall = $whereCall;
        $this->reqName = $reqName;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getWhereCall(): ?string
    {
        return $this->whereCall;
    }

    public function getReqName(): ?string
    {
        return $this->reqName;
    }
}
