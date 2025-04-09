<?php

namespace App\Services\FindSimilarElement\FindSimilarCar;

use App\Company;

final class FindSimilarCarAction
{
    /**
     * @var Company
     */
    private $company;

    /**
     * @var string
     */
    private $gosNumber;

    /**
     * @var string|null
     */
    private $vin;

    /**
     * @var string|null
     */
    private $excludeId;

    /**
     * @var bool
     */
    private $withTrashed;

    public function __construct(
        Company $company,
        string  $gosNumber,
        ?string $vin,
        ?string  $excludeId = null,
        bool    $withTrashed = false
    ) {
        $this->company = $company;
        $this->gosNumber = $gosNumber;
        $this->vin = $vin;
        $this->excludeId = $excludeId;
        $this->withTrashed = $withTrashed;
    }


    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getGosNumber(): string
    {
        return $this->gosNumber;
    }

    public function getVin(): ?string
    {
        return $this->vin;
    }

    public function getExcludeId(): ?string
    {
        return $this->excludeId;
    }

    public function withTrashed(): bool
    {
        return $this->withTrashed;
    }
}
