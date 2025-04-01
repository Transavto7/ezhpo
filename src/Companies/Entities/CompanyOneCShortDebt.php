<?php

namespace Src\Companies\Entities;

final class CompanyOneCShortDebt
{
    /**
     * @var string
     */
    private $hashId;

    /**
     * @var int
     */
    private $debt;

    /**
     * @param string $hashId
     * @param int $debt
     */
    public function __construct(string $hashId, int $debt)
    {
        $this->hashId = $hashId;
        $this->debt = $debt;
    }

    public function getHashId(): string
    {
        return $this->hashId;
    }

    /**
     * @return int
     */
    public function getDebt(): int
    {
        return $this->debt;
    }

    public function hasDebt(): bool
    {
        return $this->debt > 0;
    }
}
