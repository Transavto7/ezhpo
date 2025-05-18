<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class UsersFilter extends ArrayFilter
{
    const NAME = 'users';

    protected $column = 'notifications.user_id';
}
