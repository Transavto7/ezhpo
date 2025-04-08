<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Src\Reminders\Eloquent\ReminderLog;
use Src\Reminders\Enums\ReminderLogAction;

class ActivateReminderLogHandler
{
    public function handle(ActivateReminderLogCommand $command): void
    {
        $reminderLog = new ReminderLog();
        $reminderLog->reminder_id = $command->getReminderId();
        $reminderLog->action = ReminderLogAction::ACTIVATE;
        $reminderLog->payload = json_encode([
            'is_activated' => $command->isActivated()
        ]);

        $reminderLog->save();
    }
}
