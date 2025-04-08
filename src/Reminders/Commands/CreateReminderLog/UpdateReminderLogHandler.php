<?php

namespace Src\Reminders\Commands\CreateReminderLog;

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

        $payload = [];
        foreach ($command->getOldReminderData() as $key => $value) {
            if ($value !== $command->getNewReminderData()[$key]) {
                $payload[$key] = $command->getNewReminderData()[$key];
            }
        }

        if (isset($payload['status'])) {
            $this->activateReminderLogHandler->handle(new ActivateReminderLogCommand(
                $command->getOldReminderData()['id'],
                $payload['status']
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
