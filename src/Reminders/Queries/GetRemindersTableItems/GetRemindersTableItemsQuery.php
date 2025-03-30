<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems;

final class GetRemindersTableItemsQuery
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
     * @var RemindersTableFilters
     */
    private $filters;

    /**
     * @param int $page
     * @param int $perPage
     * @param string|null $sortBy
     * @param string|null $sortOrder
     * @param RemindersTableFilters $filters
     */
    public function __construct(
        int $page,
        int $perPage,
        ?string $sortBy,
        ?string $sortOrder,
        RemindersTableFilters $filters
    ) {
        $this->page = $page;
        $this->perPage = $perPage;
        $this->sortBy = $sortBy;
        $this->sortOrder = $sortOrder;
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

    public function getSortOrder(): ?string
    {
        return $this->sortOrder;
    }

    public function getFilters(): RemindersTableFilters
    {
        return $this->filters;
    }
}
