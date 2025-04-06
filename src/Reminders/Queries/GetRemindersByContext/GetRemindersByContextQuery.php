<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;

final class GetRemindersByContextQuery
{
    /** @var ReminderAction */
    private $action;

    /** @var Condition[] */
    private $context;

    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     */
    public function __construct(ReminderAction $action, array $context)
    {
        $this->action = $action;
        $this->context = $context;
    }

    public function getAction(): ReminderAction
    {
        return $this->action;
    }

    /**
     * @return Condition[]
     */
    public function getContext(): array
    {
        return $this->context;
    }
}
