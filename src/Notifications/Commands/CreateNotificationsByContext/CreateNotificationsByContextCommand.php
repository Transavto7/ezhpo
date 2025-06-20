<?php

declare(strict_types=1);

namespace Src\Notifications\Commands\CreateNotificationsByContext;

use App\User;
use Src\Reminders\Enums\ReminderAction;

final class CreateNotificationsByContextCommand
{
    /** @var ReminderAction */
    private $action;

    /** @var User */
    private $user;

    /** @var ContextBuilder */
    private $context;

    /**
     * @param ReminderAction $action
     * @param User $user
     * @param ContextBuilder $context
     */
    public function __construct(ReminderAction $action, User $user, ContextBuilder $context)
    {
        $this->action = $action;
        $this->context = $context;
        $this->user = $user;
    }

    public function getAction(): ReminderAction
    {
        return $this->action;
    }

    public function getUser(): User
    {
        return $this->user;
    }

    public function getContext(): ContextBuilder
    {
        return $this->context;
    }
}
