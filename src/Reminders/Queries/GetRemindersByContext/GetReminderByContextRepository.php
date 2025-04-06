<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;

interface GetReminderByContextRepository
{
    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     * @return ReminderByContextViewModel[]
     */
    public function getReminderByContext(ReminderAction $action, array $context): array;
}
