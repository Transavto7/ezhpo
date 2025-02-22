<?php

namespace Src\Users\Management\Queries\GetUsersTableItems;

use App\Enums\UserEntityType;
use Src\Users\Management\Enums\UserStatusEnum;

final class GetUsersTableItemsQuery
{
    /**
     * @var int
     */
    private $page;
    /**
     * @var int
     */
    private $perPage;
    /**
     * @var string|null
     */
    private $sortBy;
    /**
     * @var string|null
     */
    private $sortOrder;
    /**
     * @var string|null
     */
    private $search;
    /**
     * @var UserStatusEnum|null
     */
    private $status;
    /**
     * @var UserEntityType|null
     */
    private $entityType;
    /**
     * @var int[]
     */
    private $userIds;
    /**
     * @var bool
     */
    private $untyped;

    /**
     * @param int $page
     * @param int $perPage
     * @param string|null $sortBy
     * @param string|null $sortOrder
     * @param string|null $search
     * @param UserStatusEnum|null $status
     * @param UserEntityType|null $entityType
     * @param int[] $userIds
     * @param bool $untyped
     */
    public function __construct(
        int             $page,
        int             $perPage,
        ?string         $sortBy,
        ?string         $sortOrder,
        ?string         $search,
        ?UserStatusEnum $status,
        ?UserEntityType $entityType,
        array           $userIds,
        bool            $untyped
    )
    {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
        $this->search = $search;
        $this->status = $status;
        $this->entityType = $entityType;
        $this->userIds = $userIds;
        $this->untyped = $untyped;
    }

    public function getPage(): int
    {
        return $this->page;
    }

    public function getPerPage(): int
    {
        return $this->perPage;
    }

    public function getSortBy(): ?string
    {
        return $this->sortBy;
    }

    public function getSortOrder(): ?string
    {
        return $this->sortOrder;
    }

    public function getSearch(): ?string
    {
        return $this->search;
    }

    public function getStatus(): ?UserStatusEnum
    {
        return $this->status;
    }

    public function getEntityType(): ?UserEntityType
    {
        return $this->entityType;
    }

    public function getUserIds(): array
    {
        return $this->userIds;
    }

    public function isUntyped(): bool
    {
        return $this->untyped;
    }
}