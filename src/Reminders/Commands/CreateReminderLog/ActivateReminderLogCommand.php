<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Src\Reminders\Entities\Reminder;

class ActivateReminderLogCommand
{
    private $reminderId;
    private $isActivated;

    public function __construct(string $reminderId, bool $isActivated)
    {
        $this->reminderId = $reminderId;
        $this->isActivated = $isActivated;
    }

    public function getReminderId(): string
    {
        return $this->reminderId;
    }

    public function isActivated(): bool
    {
        return $this->isActivated;
    }
}
