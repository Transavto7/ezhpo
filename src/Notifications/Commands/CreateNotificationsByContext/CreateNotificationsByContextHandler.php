<?php

declare(strict_types=1);

namespace Src\Notifications\Commands\CreateNotificationsByContext;

use App\User;
use DateTimeImmutable;
use Illuminate\Bus\Dispatcher;
use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Commands\LogNotificationActivity\LogNotificationActivityCommand;
use Src\Notifications\Entities\NotificationsBuilder;
use Src\Notifications\Enums\NotificationLogAction;
use Src\Notifications\Repositories\NotificationRepository;
use Src\Reminders\ConditionBuilder\ContextConditionBuilder;
use Src\Reminders\Repositories\GetRemindersByContextRepository;
use Src\Reminders\ValueObjects\ReminderByContext;

final class CreateNotificationsByContextHandler
{
    /** @var GetRemindersByContextRepository */
    private $getReminderByContextRepository;

    /**
     * @var NotificationRepository
     */
    private $notificationRepository;

    /**
     * @var ContextConditionBuilder
     */
    private $conditionBuilder;

    /**
     * @var Dispatcher
     */
    private $dispatcher;

    /**
     * @param GetRemindersByContextRepository $getReminderByContextRepository
     * @param NotificationRepository $notificationRepository
     * @param ContextConditionBuilder $conditionBuilder
     * @param Dispatcher $dispatcher
     */
    public function __construct(
        GetRemindersByContextRepository $getReminderByContextRepository,
        NotificationRepository $notificationRepository,
        ContextConditionBuilder $conditionBuilder,
        Dispatcher $dispatcher
    ) {
        $this->getReminderByContextRepository = $getReminderByContextRepository;
        $this->notificationRepository = $notificationRepository;
        $this->conditionBuilder = $conditionBuilder;
        $this->dispatcher = $dispatcher;
    }

    /**
     * @param CreateNotificationsByContextCommand $command
     */
    public function handle(CreateNotificationsByContextCommand $command)
    {
        $now = new DateTimeImmutable();
        $sender = $command->getUser();
        $context = $this->conditionBuilder->build(array_merge(
            $command->getContext()->toArray(),
            ['user' => $command->getUser()->id])
        );

        $reminders = $this->getReminderByContextRepository->getRemindersByContext($command->getAction(), $context);
        $reminderIds = array_map(function (ReminderByContext $reminder) {
            return $reminder->getId();
        }, $reminders);

        $completedReminderIds = $this->getReminderIdsWithCompletedNotifications($reminderIds);

        foreach ($reminders as $reminder) {
            if ($reminder->isUntilAnyUserCompletes() && in_array($reminder->getId(), $completedReminderIds)) {
                continue;
            }

            if (! $reminder->isHiddenFromInitiator() && $command->getUser()) {
                if (! $reminder->isOneTimePerUser() || ! in_array($reminder->getId(), $this->getReminderIdsWithViewedNotificationsByUser($reminderIds, $command->getUser()->id))) {
                    $notification = NotificationsBuilder::fromReminder($reminder, $now, $sender, $sender);

                    $this->notificationRepository->add($notification);

                    $this->dispatcher->dispatch(new LogNotificationActivityCommand(
                        $notification->getId(),
                        NotificationLogAction::create(),
                        $command->getUser()->id,
                    ));
                }
            }

            foreach ($reminder->getUsersToNotify() as $userId) {
                if ($sender && ($sender->getAttribute('id') == $userId)) {
                    continue;
                }

                if ($reminder->isOneTimePerUser() && in_array($reminder->getId(), $this->getReminderIdsWithViewedNotificationsByUser($reminderIds, $userId))) {
                    continue;
                }

                /** @var User|null $recipient */
                $recipient = User::query()->find($userId);

                if ($recipient === null) {
                    continue;
                }

                $notification = NotificationsBuilder::fromReminder($reminder, $now, $recipient, $sender);

                $this->notificationRepository->add($notification);

                $this->dispatcher->dispatch(new LogNotificationActivityCommand(
                    $notification->getId(),
                    NotificationLogAction::create(),
                    $command->getUser()->id,
                ));
            }
        }
    }

    /**
     * @param Uuid[] $reminderIds
     * @return Uuid[]
     */
    private function getReminderIdsWithCompletedNotifications(array $reminderIds): array
    {
        return $this->notificationRepository->getReminderIdsWithCompletedNotifications($reminderIds);
    }

    /**
     * @param Uuid[] $reminderIds
     * @return Uuid[]
     */
    private function getReminderIdsWithViewedNotificationsByUser(array $reminderIds, int $userId): array
    {
        return $this->notificationRepository->getReminderIdsWithViewedNotificationsByUser($reminderIds, $userId);
    }
}
