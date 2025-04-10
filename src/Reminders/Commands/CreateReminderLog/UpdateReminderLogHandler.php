<?php

namespace Src\Reminders\Commands\CreateReminderLog;

use Illuminate\Support\Facades\Auth;
use Src\Reminders\Eloquent\ReminderLog;
use Src\Reminders\Enums\ReminderLogAction;

class UpdateReminderLogHandler
{
    private $activateReminderLogHandler;

    public function __construct(ActivateReminderLogHandler $activateReminderLogHandler)
    {
        $this->activateReminderLogHandler = $activateReminderLogHandler;
    }

    public function handle(UpdateReminderLogCommand $command): void
    {
        $reminderLog = new ReminderLog();
        $reminderLog->reminder_id = $command->getOldReminderData()['id'];
        $reminderLog->action = ReminderLogAction::UPDATE;
        $reminderLog->user_id = Auth::user()->id;

        $payload = [];
        foreach ($command->getNewReminderData() as $key => $newValue) {
            $oldValue = $command->getOldReminderData()[$key] ?? null;
            if ($newValue !== $oldValue) {
                $payload[$key] = [
                    'old' => $oldValue,
                    'new' => $newValue,
                ];
            }
        }

        if (isset($payload['status'])) {
            $this->activateReminderLogHandler->handle(new ActivateReminderLogCommand(
                $command->getOldReminderData()['id'],
                $payload['status']['new']
            ));
            unset($payload['status']);
        }

        if (empty($payload)) {
            return;
        }

        $reminderLog->payload = json_encode($payload);

        $reminderLog->save();
    }
}
