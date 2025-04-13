<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

use App\User;
use Src\Reminders\Conditions\Condition;
use Src\Reminders\Enums\ReminderAction;

final class GetNotificationsByContextQuery
{
    /** @var ReminderAction */
    private $action;

    /** @var Condition[] */
    private $context;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @param ReminderAction $action
     * @param Condition[] $context
     * @param User|null $user
     */
    public function __construct(ReminderAction $action, array $context, ?User $user = null)
    {
        $this->action = $action;
        $this->context = $context;
        $this->user = $user;
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

    /**
     * @return User|null
     */
    public function getUser(): ?User
    {
        return $this->user;
    }
}
