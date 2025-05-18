<?php

namespace Src\Notifications\Commands\LogNotificationActivity;

use Src\Notifications\Entities\NotificationLog;
use Src\Notifications\Repositories\NotificationLogRepository;

final class LogNotificationActivityHandler
{
    /**
     * @var NotificationLogRepository
     */
    private $logRepository;

    /**
     * @param NotificationLogRepository $logRepository
     */
    public function __construct(NotificationLogRepository $logRepository)
    {
        $this->logRepository = $logRepository;
    }

    /**
     * @throws \Exception
     */
    public function handle(LogNotificationActivityCommand $command)
    {

        $now = new \DateTimeImmutable();

        $this->logRepository->add(new NotificationLog(
            $command->getNotificationId(),
            $command->getUserId(),
            $now,
            $command->getActivity(),
        ));
    }
}
