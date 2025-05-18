<?php

namespace Src\Notifications\Queries\GetNotificationLogTableItems;

final class GetNotificationLogTableItemsQuery
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
     * @var bool
     */
    private $sortDesc;

    /**
     * @var GetNotificationLogTableItemsFilters
     */
    private $filters;

    /**
     * @param int $page
     * @param int $perPage
     * @param string|null $sortBy
     * @param bool $sortDesc
     * @param GetNotificationLogTableItemsFilters $filters
     */
    public function __construct(
        int $page,
        int $perPage,
        ?string $sortBy,
        bool $sortDesc,
        GetNotificationLogTableItemsFilters $filters
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->sortDesc = $sortDesc;
        $this->filters = $filters;
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

    public function getFilters(): GetNotificationLogTableItemsFilters
    {
        return $this->filters;
    }
}
