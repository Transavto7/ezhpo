<?php

namespace Src\Notifications\Commands\MarkNotificationAsCompleted;

use DateTimeImmutable;
use Exception;
use Illuminate\Bus\Dispatcher;
use Src\Notifications\Commands\LogNotificationActivity\LogNotificationActivityCommand;
use Src\Notifications\Entities\NotificationLog;
use Src\Notifications\Enums\NotificationLogAction;
use Src\Notifications\Repositories\NotificationLogRepository;
use Src\Notifications\Repositories\NotificationRepository;

class MarkNotificationAsCompletedHandler
{
    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param NotificationRepository $notificationRepository
     * @param Dispatcher $dispatcher
     */
    public function __construct(NotificationRepository $notificationRepository, Dispatcher $dispatcher)
    {
        $this->notificationRepository = $notificationRepository;
        $this->dispatcher = $dispatcher;
    }


    /**
     * @throws Exception
     */
    public function handle(MarkNotificationAsCompletedCommand $command): void
    {
        $notification = $this->notificationRepository->findById($command->getNotificationId());
        if ($notification === null) {
            throw new Exception('Уведомление с указанным ID не найдено');
        }

        if ($notification->getCompletedAt() !== null) {
            return;
        }

        $now = new DateTimeImmutable();

        $notification->read($now);
        $notification->completed($now);

        $this->notificationRepository->update($notification);

        $this->dispatcher->dispatch(new LogNotificationActivityCommand(
            $notification->getId(),
            NotificationLogAction::complete(),
            $command->getUserId(),
        ));
    }
}
