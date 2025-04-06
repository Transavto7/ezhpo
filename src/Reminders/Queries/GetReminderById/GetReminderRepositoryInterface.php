<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetReminderById;

use Src\Core\ValueObjects\Uuid;

interface GetReminderRepositoryInterface
{
    public function getReminderViewModelById(Uuid $reminderId): ?ReminderViewModel;
}
