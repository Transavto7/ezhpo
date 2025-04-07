<?php

declare(strict_types=1);

namespace Src\Employees\Tariffs\Queries\GetTariffsTableItems;

use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\PointsFilter;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\RolesFilter;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\SearchFilter;
use Src\Employees\Tariffs\Queries\GetTariffsTableItems\Filters\TownsFilter;

final class TariffsTableFilters
{
    /** @var string|null */
    private $search;

    /** @var array<int>|null */
    private $townIds;

    /** @var array<int>|null */
    private $roleIds;

    /** @var array<int>|null */
    private $pointIds;

    /**
     * @param string|null $search
     * @param array|null $townId
     * @param array|null $roleId
     * @param array|null $pointId
     */
    public function __construct(
        ?string $search,
        ?array $townId,
        ?array $roleId,
        ?array $pointId
    ) {
        $this->search = $search;
        $this->townIds = $townId;
        $this->roleIds = $roleId;
        $this->pointIds = $pointId;
    }

    public function toArray(): array
    {
        return [
            SearchFilter::NAME => $this->search,
            TownsFilter::NAME => $this->townIds,
            RolesFilter::NAME => $this->roleIds,
            PointsFilter::NAME => $this->pointIds,
        ];
    }
}
