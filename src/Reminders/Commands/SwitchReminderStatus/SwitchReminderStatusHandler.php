<?php

namespace Src\Reminders\Commands\SwitchReminderStatus;

use Src\Reminders\Commands\CreateReminderLog\ActivateReminderLogHandler;
use Src\Reminders\Commands\CreateReminderLog\UpdateReminderLogCommand;
use Src\Reminders\Commands\CreateReminderLog\UpdateReminderLogHandler;
use Src\Reminders\Normalizers\ReminderContextDatabaseNormalizer;
use Src\Reminders\Normalizers\ReminderDatabaseNormalizer;
use Src\Reminders\Repositories\RemindersRepository;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

final class SwitchReminderStatusHandler
{
    /** @var RemindersRepository */
    private $repository;

    private $activateReminderLogHandler;

    /**
     * @param RemindersRepository $repository
     * @param ActivateReminderLogHandler $activateReminderLogHandler
     */
    public function __construct(RemindersRepository $repository, ActivateReminderLogHandler $activateReminderLogHandler)
    {
        $this->repository = $repository;
        $this->activateReminderLogHandler = $activateReminderLogHandler;
    }

    public function handle(SwitchReminderStatusCommand $command)
    {
        $reminder = $this->repository->findById($command->getId());

        if ($reminder === null) {
            throw new NotFoundHttpException();
        }

        $normalizer = new ReminderDatabaseNormalizer(new ReminderContextDatabaseNormalizer());
        $oldData = $normalizer->normalize($reminder);

        $reminder->setStatus($command->getStatus());

        $this->repository->save($reminder);

        (new UpdateReminderLogHandler($this->activateReminderLogHandler))->handle(new UpdateReminderLogCommand(
            $oldData,
            $normalizer->normalize($reminder),
            $command->getUserId()
        ));
    }
}
