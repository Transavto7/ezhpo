<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersTableItems;

use Src\Core\Filters\FilterFactory;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\ActionsFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\CitiesFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\CompaniesFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\PointsFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\RolesFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\SearchFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\SubjectsFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\SubjectTypeFilter;
use Src\Reminders\Queries\GetRemindersTableItems\Filters\UsersFilter;

final class RemindersTableFiltersFactory extends FilterFactory
{
    protected $filterClasses = [
        SearchFilter::NAME => SearchFilter::class,
        ActionsFilter::NAME => ActionsFilter::class,
        CitiesFilter::NAME => CitiesFilter::class,
        CompaniesFilter::NAME => CompaniesFilter::class,
        PointsFilter::NAME => PointsFilter::class,
        RolesFilter::NAME => RolesFilter::class,
        SubjectsFilter::NAME => SubjectsFilter::class,
        SubjectTypeFilter::NAME => SubjectTypeFilter::class,
        UsersFilter::NAME => UsersFilter::class,
    ];
}
