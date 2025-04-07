<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariffsTableItems;

final class GetTariffsTableItemsQuery
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
     * @var TariffsTableFilters
     */
    private $filters;

    /**
     * @param int $page
     * @param int $perPage
     * @param string|null $sortBy
     * @param string|null $sortOrder
     * @param TariffsTableFilters $filters
     */
    public function __construct(
        int $page,
        int $perPage,
        ?string $sortBy,
        ?string $sortOrder,
        TariffsTableFilters $filters
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

    public function getFilters(): TariffsTableFilters
    {
        return $this->filters;
    }
}
