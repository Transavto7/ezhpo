<?php

namespace App\Services\FindSimilarElement\FindSimilarCompany;

final class FindSimilarCompanyAction
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $inn;

    /**
     * @var string|null
     */
    private $kpp;

    /**
     * @var string|null
     */
    private $ogrn;

    /**
     * @var string|null
     */
    private $excludeId;

    /**
     * @var bool
     */
    private $withTrashed;

    public function __construct(
        string  $name,
        string  $inn,
        ?string $kpp,
        ?string $ogrn,
        ?string  $excludeId = null,
        bool    $withTrashed = false
    ) {
        $this->name = $name;
        $this->inn = $inn;
        $this->kpp = $kpp;
        $this->ogrn = $ogrn;
        $this->excludeId = $excludeId;
        $this->withTrashed = $withTrashed;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getInn(): string
    {
        return $this->inn;
    }

    public function getKpp(): ?string
    {
        return $this->kpp;
    }

    public function getOgrn(): ?string
    {
        return $this->ogrn;
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
