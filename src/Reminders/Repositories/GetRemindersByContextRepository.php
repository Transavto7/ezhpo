<?php

declare(strict_types=1);

namespace Src\Reminders\Repositories;

use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;
use Src\Reminders\ValueObjects\ReminderByContext;

interface GetRemindersByContextRepository
{
    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     * @return ReminderByContext[]
     */
    public function getRemindersByContext(ReminderAction $action, array $context): array;
}
