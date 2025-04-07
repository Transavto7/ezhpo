<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Src\Reminders\Entities\Reminder;

class ShowReminderLogCommand
{
    private $reminderId;
    private $userId;

    public function __construct(string $reminderId, int $userId)
    {
        $this->reminderId = $reminderId;
        $this->userId = $userId;
    }


    public function getReminderId(): string
    {
        return $this->reminderId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
