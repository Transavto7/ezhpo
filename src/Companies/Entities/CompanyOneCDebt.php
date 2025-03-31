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
    private $debtStructure;

    /**
     * @var string
     */
    private $message;

    public function __construct(bool $hasDebt, array $debtStructure, string $message)
    {
        $this->hasDebt = $hasDebt;
        $this->debtStructure = $debtStructure;
        $this->message = $message;
    }

    public function isHasDebt(): bool
    {
        return $this->hasDebt;
    }

    public function getDebtStructure(): array
    {
        return $this->debtStructure;
    }

    public function getMessage(): string
    {
        return $this->message;
    }

    public function toArray(): array
    {
        $debtStructure = array_reduce($this->getDebtStructure(), function ($carry, $item) {
            $carry[] = $item->toArray();

            return $carry;
        }, []);

        return [
            'has_debt' => $this->isHasDebt(),
            'debt_structure' => $debtStructure,
            'message' => $this->getMessage(),
        ];
    }
}
