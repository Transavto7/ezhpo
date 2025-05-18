<?php

namespace Src\Reminders\Commands\CreateReminderLog;

class UpdateReminderLogCommand
{
    private $oldReminderData;
    private $newReminderData;

    /**
     * @var ?int
     */
    private $userId;

    public function __construct(array $oldReminderData, array $newReminderData, ?int $userId)
    {
        $this->oldReminderData = $oldReminderData;
        $this->newReminderData = $newReminderData;
        $this->userId = $userId;
    }

    public function getOldReminderData(): array
    {
        return $this->oldReminderData;
    }

    public function getNewReminderData(): array
    {
        return $this->newReminderData;
    }

    public function getUserId(): ?int
    {
        return $this->userId;
    }
}
