<?php

declare(strict_types=1);

namespace Src\Reminders\Commands\UpdateReminder;

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

    /**
     * @param RemindersRepository $repository
     */
    public function __construct(RemindersRepository $repository)
    {
        $this->repository = $repository;
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

        $this->repository->save($reminder);

        (new UpdateReminderLogHandler())->handle(new UpdateReminderLogCommand($oldData, $normalizer->normalize($reminder)));
    }
}
