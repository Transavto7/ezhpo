<?php

declare(strict_types=1);

namespace Src\Reminders\Queries\GetRemindersByContext;

use App\User;
use DateTimeImmutable;
use Src\Notifications\Entitites\NotificationsBuilder;
use Src\Notifications\Queries\NotificationViewModel;
use Src\Notifications\Repository\NotificationRepository;

final class GetNotificationsByContextHandler
{
    /** @var GetReminderByContextRepository */
    private $getReminderByContextRepository;

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @param GetReminderByContextRepository $getReminderByContextRepository
     * @param NotificationRepository $notificationRepository
     */
    public function __construct(GetReminderByContextRepository $getReminderByContextRepository, NotificationRepository $notificationRepository)
    {
        $this->getReminderByContextRepository = $getReminderByContextRepository;
        $this->notificationRepository = $notificationRepository;
    }

    /**
     * @param GetNotificationsByContextQuery $query
     * @return NotificationViewModel[]
     */
    public function handle(GetNotificationsByContextQuery $query): array
    {
        $reminders = $this->getReminderByContextRepository->getReminderByContext($query->getAction(), $query->getContext());

        $sender = $query->getUser();

        $now = new DateTimeImmutable();

        $notifications = [];

        foreach ($reminders as $reminder) {
            if (!$reminder->isHiddenFromInitiator() && $query->getUser()) {
                $notification = NotificationsBuilder::fromReminder($reminder, $now, $sender, $sender);

                $this->notificationRepository->add($notification);

                $notifications[] = NotificationViewModel::createFromNotification($notification);
            }

            foreach ($reminder->getUsersToNotify() as $userId) {
                if ($sender && ($sender->getAttribute('id') == $userId)) {
                    continue;
                }

                /** @var User|null $recipient */
                $recipient = User::query()->find($userId);

                if ($recipient === null) {
                    continue;
                }

                $this->notificationRepository->add(NotificationsBuilder::fromReminder($reminder, $now, $recipient, $sender));
            }
        }

        return $notifications;
    }
}
