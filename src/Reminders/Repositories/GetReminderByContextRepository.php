<?php

declare(strict_types=1);

namespace Src\Reminders\Repositories;

use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\ValueObjects\ReminderByContext;

interface GetReminderByContextRepository
{
    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     * @return ReminderByContext[]
     */
    public function getReminderByContext(ReminderAction $action, array $context): array;
}
