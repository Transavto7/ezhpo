<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

use App\User;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;

interface GetReminderByContextRepository
{
    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     * @return ReminderByContext[]
     */
    public function getReminderByContext(ReminderAction $action, array $context): array;
}
