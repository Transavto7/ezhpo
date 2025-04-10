<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Illuminate\Support\Facades\Auth;
use Src\Reminders\Eloquent\ReminderLog;
use Src\Reminders\Enums\ReminderLogAction;

class ActivateReminderLogHandler
{
    public function handle(ActivateReminderLogCommand $command): void
    {
        $reminderLog = new ReminderLog();
        $reminderLog->reminder_id = $command->getReminderId();
        $reminderLog->action = ReminderLogAction::ACTIVATE;
        $reminderLog->user_id = Auth::user()->id;
        $reminderLog->payload = json_encode([
            'is_activated' => [
                'old' => $command->isActivatedOld(),
                'new' => $command->isActivatedNew(),
            ]
        ]);

        $reminderLog->save();
    }
}
