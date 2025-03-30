<?php

namespace Src\Companies\Entities;

final class CompanyDebt
{
    /**
     * @var \DateTimeImmutable
     */
    private $relevantOn;

    /**
     * @var boolean
     */
    private $hasDebt;

    public function __construct(\DateTimeImmutable $relevantOn, bool $hasDebt)
    {
        $this->relevantOn = $relevantOn;
        $this->hasDebt = $hasDebt;
    }

    public function getTitle(): string
    {
        return $this->hasDebt
            ? "Есть задолженность (актуально на {$this->relevantOn->format('d.m.Y H:i')})"
            : 'Задолженности нет';
    }

    public function toArray(): array
    {
        return [
            'has_debt' => $this->hasDebt,
            'title' => $this->getTitle(),
        ];
    }
}
