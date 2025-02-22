<?php

namespace Src\Users\Management\Queries\GetUsersSelectItems;

use App\Enums\UserEntityType;

final class GetUsersSelectItemsQuery
{
    /**
     * @var string
     */
    private $search;
    /**
     * @var UserEntityType|null
     */
    private $entityType;

    /**
     * @param string $search
     * @param UserEntityType|null $entityType
     */
    public function __construct(string $search, ?UserEntityType $entityType)
    {
        $this->search = $search;
        $this->entityType = $entityType;
    }

    public function getSearch(): string
    {
        return $this->search;
    }

    public function getEntityType(): ?UserEntityType
    {
        return $this->entityType;
    }
}