<?php

declare(strict_types=1);

namespace Src\Notifications\Queries\GetNotificationTableItems\Filters;

use Src\Core\Filters\AbstractFilters\ArrayFilter;

final class InitiatorUsersFilter extends ArrayFilter
{
    const NAME = 'initiator_users';

    protected $column = 'notifications.initiator_user_id';
}
