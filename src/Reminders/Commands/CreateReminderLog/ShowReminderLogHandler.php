<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Illuminate\Support\Facades\DB;
use Src\Reminders\Eloquent\ReminderLog;
use Src\Reminders\Enums\ReminderLogAction;

class ShowReminderLogHandler
{
    public function handle(ShowReminderLogCommand $command): void
    {
        $reminderLog = new ReminderLog();
        $reminderLog->reminder_id = $command->getReminderId();
        $reminderLog->action = ReminderLogAction::SHOW;
        $reminderLog->user_id = $command->getUserId();
        $reminderLog->payload = json_encode(DB::table('reminders')
            ->select([
                'reminders.title',
                'reminders.content',
            ])
            ->where('reminders.id', '=', $command->getReminderId())
            ->get()
            ->toArray());

        $reminderLog->save();
    }
}
