<?php

namespace Src\Companies\Entities;

final class CompanyOneCDebt
{
    /**
     * @var bool
     */
    private $hasDebt;

    /**
     * @var DebtStructure[]
     */
    private $debtStructures;

    /**
     * @var string
     */
    private $message;

    /**
     * @var string
     */
    private $hashId;

    /**
     * @param string $hashId
     * @param bool $hasDebt
     * @param DebtStructure[] $debtStructures
     * @param string $message
     */
    public function __construct(string $hashId, bool $hasDebt, array $debtStructures, string $message)
    {
        $this->hashId = $hashId;
        $this->hasDebt = $hasDebt;
        $this->debtStructures = $debtStructures;
        $this->message = $message;
    }

    public function getHashId(): string
    {
        return $this->hashId;
    }

    public function hasDebt(): bool
    {
        return $this->hasDebt;
    }

    public function getDebtStructures(): array
    {
        return $this->debtStructures;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        $debtStructure = array_reduce($this->getDebtStructures(), function ($carry, $item) {
            $carry[] = $item->toArray();

            return $carry;
        }, []);

        return [
            'has_debt' => $this->hasDebt(),
            'debt_structure' => $debtStructure,
            'message' => $this->getMessage(),
        ];
    }
}
