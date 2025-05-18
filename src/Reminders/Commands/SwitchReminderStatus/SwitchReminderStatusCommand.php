<?php

namespace Src\Reminders\Commands\SwitchReminderStatus;

use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Enums\ReminderStatus;

/**
 * @Handler()
 */
final class SwitchReminderStatusCommand
{
    /**
     * @var Uuid
     */
    private $id;
    /**
     * @var int
     */
    private $userId;
    /**
     * @var ReminderStatus
     */
    private $status;

    /**
     * @param Uuid $id
     * @param int $userId
     * @param ReminderStatus $status
     */
    public function __construct(Uuid $id, int $userId, ReminderStatus $status)
    {
        $this->id = $id;
        $this->userId = $userId;
        $this->status = $status;
    }

    public function getId(): Uuid
    {
        return $this->id;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    public function getStatus(): ReminderStatus
    {
        return $this->status;
    }


}
