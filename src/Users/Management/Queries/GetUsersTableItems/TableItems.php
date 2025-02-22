<?php

namespace Src\Users\Management\Queries\GetUsersTableItems;

final class TableItems implements \JsonSerializable
{
    /**
     * @var array
     */
    private $items;
    /**
     * @var int
     */
    private $total;

    /**
     * @param array $items
     * @param int $total
     */
    public function __construct(array $items, int $total)
    {
        $this->items = $items;
        $this->total = $total;
    }

    public function getItems(): array
    {
        return $this->items;
    }

    public function getTotal(): int
    {
        return $this->total;
    }

    public function jsonSerialize(): array
    {
        return [
            'items' => $this->items,
            'total' => $this->total,
        ];
    }
}