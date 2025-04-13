<?php

declare(strict_types=1);

namespace Src\Reminders\Commands\CreateReminder;

use Src\Core\ValueObjects\Uuid;
use Src\Reminders\Entities\Reminder;
use Src\Reminders\Repositories\RemindersRepository;

final class CreateReminderHandler
{
    /** @var RemindersRepository */
    private $repository;

    /**
     * @param RemindersRepository $repository
     */
    public function __construct(RemindersRepository $repository)
    {
        $this->repository = $repository;
    }

    /**
     * @param CreateReminderCommand $command
     * @return void
     * @throws \Exception
     */
    public function handle(CreateReminderCommand $command): void
    {
        $reminder = new Reminder(
            Uuid::next(),
            $command->getTitle(),
            $command->getContent(),
            $command->getAction(),
            $command->getContext(),
            $command->getStatus(),
            $command->getType(),
            $command->hiddenFromInitiator(),
            $command->getUsersToNotify(),
            $command->getExpiresAt(),
            $command->getExpiresInMinutes(),
        );

        $this->repository->add($reminder);
    }
}
