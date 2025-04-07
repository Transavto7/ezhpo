<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Src\Reminders\Entities\Reminder;

class UpdateReminderLogCommand
{
    private $oldReminderData;
    private $newReminderData;

    public function __construct(array $oldReminderData, array $newReminderData)
    {
        $this->oldReminderData = $oldReminderData;
        $this->newReminderData = $newReminderData;
    }

    public function getOldReminderData(): array
    {
        return $this->oldReminderData;
    }

    public function getNewReminderData(): array
    {
        return $this->newReminderData;
    }
}
