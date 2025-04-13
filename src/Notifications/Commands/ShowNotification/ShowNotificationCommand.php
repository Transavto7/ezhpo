<?php

namespace Src\Notifications\Commands\ShowNotification;

class ShowNotificationCommand
{
    private $notificationId;
    private $userId;

    public function __construct(string $reminderId, int $userId)
    {
        $this->notificationId = $reminderId;
        $this->userId = $userId;
    }


    public function getNotificationId(): string
    {
        return $this->notificationId;
    }

    public function getUserId(): int
    {
        return $this->userId;
    }
}
