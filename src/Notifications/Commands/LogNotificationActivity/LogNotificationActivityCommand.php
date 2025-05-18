<?php

namespace Src\Notifications\Commands\LogNotificationActivity;

use Src\Core\ValueObjects\Uuid;
use Src\Notifications\Enums\NotificationLogAction;

final class LogNotificationActivityCommand
{
    /**
     * @var Uuid
     */
    private $notificationId;

    /**
     * @var NotificationLogAction
     */
    private $activity;

    /**
     * @var int
     */
    private $userId;

    public function getNotificationId(): Uuid
    {
        return $this->notificationId;
    }

    public function getActivity(): NotificationLogAction
    {
        return $this->activity;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }

    /**
     * @param Uuid $notificationId
     * @param NotificationLogAction $activity
     * @param int $userId
     */
    public function __construct(Uuid $notificationId, NotificationLogAction $activity, int $userId)
    {
        $this->notificationId = $notificationId;
        $this->activity = $activity;
        $this->userId = $userId;
    }
}
