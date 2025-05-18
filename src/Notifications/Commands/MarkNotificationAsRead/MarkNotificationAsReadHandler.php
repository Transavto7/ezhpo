<?php

namespace Src\Notifications\Commands\MarkNotificationAsRead;

use DateTimeImmutable;
use Exception;
use Illuminate\Bus\Dispatcher;
use Src\Notifications\Commands\LogNotificationActivity\LogNotificationActivityCommand;
use Src\Notifications\Enums\NotificationLogAction;
use Src\Notifications\Repositories\NotificationRepository;

class MarkNotificationAsReadHandler
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
    public function handle(MarkNotificationAsReadCommand $command): void
    {
        $notification = $this->notificationRepository->findById($command->getNotificationId());
        if ($notification === null) {
            throw new Exception('Уведомление с указанным ID не найдено');
        }

        $now = new DateTimeImmutable();

        $notification->viewed($now);
        $notification->read($now);

        $this->notificationRepository->update($notification);

        $this->dispatcher->dispatch(new LogNotificationActivityCommand(
            $notification->getId(),
            NotificationLogAction::read(),
            $command->getUserId(),
        ));
    }
}
