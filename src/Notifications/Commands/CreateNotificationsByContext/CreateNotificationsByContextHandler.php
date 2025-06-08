<?php

declare(strict_types=1);

namespace Src\Notifications\Commands\CreateNotificationsByContext;

use App\User;
use DateTimeImmutable;
use Illuminate\Bus\Dispatcher;
use Src\Notifications\Commands\LogNotificationActivity\LogNotificationActivityCommand;
use Src\Notifications\Entities\NotificationsBuilder;
use Src\Notifications\Enums\NotificationLogAction;
use Src\Notifications\Repositories\NotificationRepository;
use Src\Reminders\ConditionBuilder\ContextConditionBuilder;
use Src\Reminders\Repositories\GetRemindersByContextRepository;

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
        NotificationRepository          $notificationRepository,
        ContextConditionBuilder         $conditionBuilder,
        Dispatcher                      $dispatcher
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
        $context = $this->conditionBuilder->build(array_merge($command->getContext(), ['user' => $command->getUser()->id]));

        $reminders = $this->getReminderByContextRepository->getRemindersByContext($command->getAction(), $context);

        foreach ($reminders as $reminder) {
            if (! $reminder->isHiddenFromInitiator() && $command->getUser()) {
                $notification = NotificationsBuilder::fromReminder($reminder, $now, $sender, $sender);

                $this->notificationRepository->add($notification);

                $this->dispatcher->dispatch(new LogNotificationActivityCommand(
                    $notification->getId(),
                    NotificationLogAction::create(),
                    $command->getUser()->id,
                ));
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
}
