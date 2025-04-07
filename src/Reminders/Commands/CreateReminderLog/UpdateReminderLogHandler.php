<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Src\Reminders\Eloquent\ReminderLog;
use Src\Reminders\Enums\ReminderLogAction;

class UpdateReminderLogHandler
{
    public function handle(UpdateReminderLogCommand $command): void
    {
        $reminderLog = new ReminderLog();
        $reminderLog->reminder_id = $command->getOldReminderData()['id'];
        $reminderLog->action = ReminderLogAction::UPDATE;

        $payload = [];
        foreach ($command->getOldReminderData() as $key => $value) {
            if ($value !== $command->getNewReminderData()[$key]) {
                $payload[$key] = $command->getNewReminderData()[$key];
            }
        }

        $reminderLog->payload = json_encode($payload);

        $reminderLog->save();
    }
}
