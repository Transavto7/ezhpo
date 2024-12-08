<?php

namespace App\Services\TripTicketExporter\ValueObjects;

use App\Company as CompanyModel;

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
    private function __construct(string $name, ?string $whereCall)
    {
        $this->name = $name;
        $this->whereCall = $whereCall;
    }

    public static function fromEloquent(CompanyModel $company): self
    {
        return new self($company->name, $company->where_call);
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
