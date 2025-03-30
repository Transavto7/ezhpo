<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems;

use Src\Reminders\Queries\GetRemindersTableItems\Filters\ActionsFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\CitiesFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\CompaniesFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\PointsFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\RolesFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\SearchFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\SubjectsFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\SubjectTypeFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\UsersFilter;

final class RemindersTableFilters
{
    /** @var string|null */
    private $search;

    /** @var array<int>|null */
    private $cityIds;

    /** @var array<int>|null */
    private $companyIds;

    /** @var array<int>|null */
    private $pointIds;

    /** @var array<int>|null */
    private $roleIds;

    /** @var array<int>|null */
    private $subjectIds;

    /** @var string|null */
    private $subjectType;

    /** @var array<int>|null */
    private $userIds;

    /** @var array<string>|null */
    private $actions;

    /**
     * @param string|null $search
     * @param int[]|null $cityIds
     * @param int[]|null $companyIds
     * @param int[]|null $pointIds
     * @param int[]|null $roleIds
     * @param int[]|null $subjectIds
     * @param string|null $subjectType
     * @param int[]|null $userIds
     * @param string[]|null $actions
     */
    public function __construct(
        ?string $search,
        ?array $cityIds,
        ?array $companyIds,
        ?array $pointIds,
        ?array $roleIds,
        ?array $subjectIds,
        ?string $subjectType,
        ?array $userIds,
        ?array $actions
    ) {
        $this->search = $search;
        $this->cityIds = $cityIds;
        $this->companyIds = $companyIds;
        $this->pointIds = $pointIds;
        $this->roleIds = $roleIds;
        $this->subjectIds = $subjectIds;
        $this->subjectType = $subjectType;
        $this->userIds = $userIds;
        $this->actions = $actions;
    }

    public function toArray(): array
    {
        return [
            SearchFilter::NAME => $this->search,
            ActionsFilter::NAME => $this->actions,
            CitiesFilter::NAME => $this->cityIds,
            CompaniesFilter::NAME => $this->companyIds,
            PointsFilter::NAME => $this->pointIds,
            RolesFilter::NAME => $this->roleIds,
            SubjectsFilter::NAME => $this->subjectIds,
            SubjectTypeFilter::NAME => $this->subjectType,
            UsersFilter::NAME => $this->userIds,
        ];
    }
}
