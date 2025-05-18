<?php

namespace Src\Notifications\Queries\GetNotificationTableItems;

final class GetNotificationTableItemsQuery
{
    /**
     * @var int
     */
    private $userId;

    /**
     * @var bool
     */
    private $canViewOther;

    /**
     * @var bool
     */
    private $canChangeOther;

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
     * @var bool
     */
    private $sortDesc;

    /**
     * @var GetNotificationTableItemsFilters
     */
    private $filters;

    /**
     * @param int $userId
     * @param bool $canViewOther
     * @param bool $canChangeOther
     * @param int $page
     * @param int $perPage
     * @param string|null $sortBy
     * @param bool $sortDesc
     * @param GetNotificationTableItemsFilters $filters
     */
    public function __construct(
        int $userId,
        bool $canViewOther,
        bool $canChangeOther,
        int $page,
        int $perPage,
        ?string $sortBy,
        bool $sortDesc,
        GetNotificationTableItemsFilters $filters
    ) {
        $this->userId = $userId;
        $this->canViewOther = $canViewOther;
        $this->canChangeOther = $canChangeOther;
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->sortDesc = $sortDesc;
        $this->filters = $filters;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function isCanViewOther(): bool
    {
        return $this->canViewOther;
    }

    public function isCanChangeOther(): bool
    {
        return $this->canChangeOther;
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

    public function isSortDesc(): bool
    {
        return $this->sortDesc;
    }

    public function getFilters(): GetNotificationTableItemsFilters
    {
        return $this->filters;
    }
}
