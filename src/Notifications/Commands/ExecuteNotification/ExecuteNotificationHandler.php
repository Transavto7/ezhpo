<?php

namespace Src\Notifications\Commands\ExecuteNotification;

use DateTimeImmutable;
use Exception;
use Src\Notifications\Entitites\NotificationLog;
use Src\Notifications\Enums\NotificationLogAction;
use Src\Notifications\Repository\NotificationLogRepository;
use Src\Notifications\Repository\NotificationRepository;

class ExecuteNotificationHandler
{
    /**
     * @var NotificationLogRepository
     */
    private $logRepository;

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    public function __construct(NotificationRepository $notificationRepository, NotificationLogRepository $logRepository)
    {
        $this->notificationRepository = $notificationRepository;
        $this->logRepository = $logRepository;
    }

    /**
     * @throws Exception
     */
    public function handle(ExecuteNotificationCommand $command): void
    {
        $notification = $this->notificationRepository->findById($command->getNotificationId());
        if ($notification === null) {
            throw new Exception('Уведомление с указанным ID не найдено');
        }

        $now = new DateTimeImmutable();

        $notification->completed($now);

        $this->notificationRepository->update($notification);

        $this->logRepository->add(new NotificationLog(
            $notification->getId(),
            $command->getUserId(),
            $now,
            NotificationLogAction::EXECUTE
        ));
    }
}
