<?php

declare(strict_types=1);

namespace Src\Notifications\Commands\CreateNotificationsByContext;

use App\User;
use Src\Reminders\Enums\ReminderAction;

final class CreateNotificationsByContextCommand
{
    /** @var ReminderAction */
    private $action;

    /** @var array */
    private $context;

    /**
     * @var User
     */
    private $user;

    /**
     * @param ReminderAction $action
     * @param array $context
     * @param User $user
     */
    public function __construct(ReminderAction $action, array $context, User $user)
    {
        $this->action = $action;
        $this->context = $context;
        $this->user = $user;
    }

    public function getAction(): ReminderAction
    {
        return $this->action;
    }

    public function getContext(): array
    {
        return $this->context;
    }

    public function getUser(): User
    {
        return $this->user;
    }
}
