<?php

namespace Src\Notifications\Queries\GetNotificationTableItems;

use Carbon\Carbon;
use Src\Core\Filters\Dto\DateRange;
use Src\Core\Filters\FilterFactory;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\CompletedAtFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\CreatedAtFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\ExpiresAtFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\InitiatorUsersFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\NotificationsFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\RemindersFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\SearchFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\StatusFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\UsersFilter;
use Src\Notifications\Queries\GetNotificationTableItems\Filters\ViewedAtFilter;

final class GetNotificationTableItemsFiltersFactory extends FilterFactory
{
    protected $filterClasses = [
        SearchFilter::NAME => SearchFilter::class,
        UsersFilter::NAME => UsersFilter::class,
        InitiatorUsersFilter::NAME => InitiatorUsersFilter::class,
        NotificationsFilter::NAME => NotificationsFilter::class,
        RemindersFilter::NAME => RemindersFilter::class,
        ViewedAtFilter::NAME => ViewedAtFilter::class,
        CompletedAtFilter::NAME => CompletedAtFilter::class,
        ExpiresAtFilter::NAME => ExpiresAtFilter::class,
        CreatedAtFilter::NAME => CreatedAtFilter::class,
        StatusFilter::NAME => StatusFilter::class,
    ];
}
