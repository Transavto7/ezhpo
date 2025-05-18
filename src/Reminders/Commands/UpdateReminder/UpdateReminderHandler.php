<?php

declare(strict_types=1);

namespace Src\Reminders\Commands\UpdateReminder;

use Src\Reminders\Commands\CreateReminderLog\ActivateReminderLogHandler;
use Src\Reminders\Commands\CreateReminderLog\UpdateReminderLogCommand;
use Src\Reminders\Commands\CreateReminderLog\UpdateReminderLogHandler;
use Src\Reminders\Normalizers\ReminderContextDatabaseNormalizer;
use Src\Reminders\Normalizers\ReminderDatabaseNormalizer;
use Src\Reminders\Repositories\RemindersRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class UpdateReminderHandler
{
    /** @var RemindersRepository */
    private $repository;

    private $activateReminderLogHandler;

    /**
     * @param RemindersRepository $repository
     */
    public function __construct(RemindersRepository $repository, ActivateReminderLogHandler $activateReminderLogHandler)
    {
        $this->repository = $repository;
        $this->activateReminderLogHandler = $activateReminderLogHandler;
    }

    public function handle(UpdateReminderCommand $command): void
    {
        $reminder = $this->repository->findById($command->getId());

        if ($reminder === null) {
            throw new NotFoundHttpException();
        }

        $normalizer = new ReminderDatabaseNormalizer(new ReminderContextDatabaseNormalizer());
        $oldData = $normalizer->normalize($reminder);

        $reminder->setTitle($command->getTitle());
        $reminder->setContext($command->getContext());
        $reminder->setContent($command->getContent());
        $reminder->setAction($command->getAction());
        $reminder->setStatus($command->getStatus());
        $reminder->setType($command->getType());
        $reminder->setHiddenFromInitiator($command->hiddenFromInitiator());
        $reminder->setUsersToNotify($command->getUsersToNotify());
        $reminder->setExpiresAt($command->getExpiresAt());
        $reminder->setExpiresInMinutes($command->getExpiresInMinutes());

        $this->repository->save($reminder);

        (new UpdateReminderLogHandler($this->activateReminderLogHandler))->handle(new UpdateReminderLogCommand(
            $oldData,
            $normalizer->normalize($reminder),
            $command->getUserId()
        ));
    }
}
