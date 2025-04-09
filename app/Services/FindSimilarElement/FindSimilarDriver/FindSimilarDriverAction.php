<?php

namespace App\Services\FindSimilarElement\FindSimilarDriver;

use App\Company;

final class FindSimilarDriverAction
{
    /**
     * @var Company
     */
    private $company;

    /**
     * @var string
     */
    private $name;

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
        string  $name,
        ?string  $excludeId = null,
        bool    $withTrashed = false
    ) {
        $this->company = $company;
        $this->name = $name;
        $this->excludeId = $excludeId;
        $this->withTrashed = $withTrashed;
    }

    public function getCompany(): Company
    {
        return $this->company;
    }

    public function getName(): string
    {
        return $this->name;
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
