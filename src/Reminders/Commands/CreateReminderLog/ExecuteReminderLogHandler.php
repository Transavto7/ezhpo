<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Src\Reminders\Eloquent\ReminderLog;
use Src\Reminders\Enums\ReminderLogAction;

class ExecuteReminderLogHandler
{
    public function handle(ExecuteReminderLogCommand $command): void
    {
        $reminderLog = new ReminderLog();
        $reminderLog->reminder_id = $command->getReminderId();
        $reminderLog->action = ReminderLogAction::EXECUTE;
        $reminderLog->user_id = $command->getUserId();

        $reminderLog->save();
    }
}
