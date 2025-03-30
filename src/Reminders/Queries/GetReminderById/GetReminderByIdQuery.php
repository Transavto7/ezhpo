<?php
declare(strict_types=1);

namespace Src\Reminders\Queries\GetReminderById;

use Src\Core\ValueObjects\Uuid;

final class GetReminderByIdQuery
{
    /** @var Uuid */
    private $reminderId;

    public function __construct(Uuid $reminderId)
    {
        $this->reminderId = $reminderId;
    }

    public function getReminderId(): Uuid
    {
        return $this->reminderId;
    }
}
