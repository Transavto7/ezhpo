<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Src\Reminders\Entities\Reminder;

class ActivateReminderLogCommand
{
    private $reminderId;
    private $isActivatedOld;
    private $isActivatedNew;

    public function __construct(string $reminderId, string $isActivatedOld, string $isActivatedNew)
    {
        $this->reminderId = $reminderId;
        $this->isActivatedOld = $isActivatedOld;
        $this->isActivatedNew = $isActivatedNew;
    }

    public function getReminderId(): string
    {
        return $this->reminderId;
    }

    public function isActivatedOld(): string
    {
        return $this->isActivatedOld;
    }

    public function isActivatedNew(): string
    {
        return $this->isActivatedNew;
    }
}
