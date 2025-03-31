<?php

namespace Src\Companies\Entities;

final class DebtStructure
{
    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $debt;

    public function __construct(string $title, string $debt)
    {
        $this->title = $title;
        $this->debt = $debt;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getDebt(): string
    {
        return $this->debt;
    }

    public function toArray(): array
    {
        return [
            'title' => $this->getTitle(),
            'debt' => $this->getDebt(),
        ];
    }
}
