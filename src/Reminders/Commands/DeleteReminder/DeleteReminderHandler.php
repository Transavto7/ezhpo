<?php
declare(strict_types=1);

namespace Src\Reminders\Commands\DeleteReminder;

use Src\Reminders\Repositories\RemindersRepository;

final class DeleteReminderHandler
{
    /** @var RemindersRepository */
    private $reminderRepository;

    public function __construct(RemindersRepository $remindersRepository)
    {
        $this->reminderRepository = $remindersRepository;
    }

    public function handle(DeleteReminderCommand $command): void
    {
        $this->reminderRepository->remove($command->getReminderId());
    }
}
