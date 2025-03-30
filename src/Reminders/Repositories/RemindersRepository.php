<?php

declare(strict_types=1);

namespace Src\Reminders\Repositories;

use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Entities\Reminder;

interface RemindersRepository
{
    public function add(Reminder $reminder): void;

    public function findById(Uuid $reminderId): ?Reminder;

    public function save(Reminder $reminder): void;

    public function remove(Uuid $reminderId): void;
}
