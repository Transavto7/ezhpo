<?php

declare(strict_types=1);

namespace Src\Reminders\Commands\DeleteReminder;

use Src\Core\ValueObjects\Uuid;

final class DeleteReminderCommand
{
    /** @var Uuid */
    private $reminderId;

    /**
     * @param Uuid $reminderId
     */
    public function __construct(Uuid $reminderId)
    {
        $this->reminderId = $reminderId;
    }

    public function getReminderId(): Uuid
    {
        return $this->reminderId;
    }
}
